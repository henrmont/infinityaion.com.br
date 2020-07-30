<?php

namespace App\Controller;

use App\Entity\Aion;
use App\Entity\History;
use App\Entity\Inventory;
use App\Entity\Item;
use App\Entity\ItemType;
use App\Entity\Mail;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function index(ContainerInterface $container, Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $data = $em->getRepository(Item::class)->searchItem($request->get('search'), $request->get('category'), $user->getRace());
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $category = $em->getRepository(ItemType::class)->findAll();
            $cube = $em->getRepository(History::class)->searchExpandCard($user->getId());

            // print_r($cube);
            // die();

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $data,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',24)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/shop/shop.html.twig', [
                'cube'      =>  $cube,
                'chars'     =>  $chars,
                'data'      =>  $result,
                'players'   =>  $players,
                'category'  =>  $category,
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            // $this->addFlash(
            //     'notice',
            //     'Faça o login.'
            // );
            // return $this->redirectToRoute('site');
        } 
        
    }

    // /**
    //  * @Route("/shop/cart", name="cart")
    //  */
    // public function cartIndex(ContainerInterface $container, Request $request)
    // {
    //     try{
    //         $user = $this->getUser();

    //         $em = $this->getDoctrine()->getManager();

    //         $data = $em->getRepository(ShopItem::class)->searchCartItens($user->getId());
    //         $players = $em->getRepository(ShopItem::class)->searchChar($user->getUsername());

    //         // echo "<pre>";
    //         // print_r($data);
    //         // echo "<pre>";
    //         // die();

    //         return $this->render('painel/contents/shop/cart.html.twig', [
    //             'data'      =>  $data,
    //             'players'   =>  $players
    //         ]);
    //     }catch(\Exception $e){
    //         return $e->getMessage();
    //     }
        
    // }

    // /**
    //  * @Route("/shop/inc", name="shop_inc")
    //  */
    // public function shopInc(Request $request)
    // {
    //     try{
    //         $user = $this->getUser();
            
    //         $em = $this->getDoctrine()->getManager();
            
    //         $item = $em->getRepository(Item::class)->findOneBy([
    //             'aion' =>   $request->get('aionId')
    //         ]);

    //         $player = explode('|',$request->get('selchar'));

    //         $shopitem = new ShopItem();

    //         $shopitem->setUser($user->getId());
    //         $shopitem->setPlayer($player[0]);
    //         $shopitem->setPlayerName($player[1]);
    //         $shopitem->setItem($request->get('aionId'));
    //         if($item->getAmount() == 1){
    //             $shopitem->setAmount($request->get('amount'));
    //         } else {
    //             $shopitem->setAmount(1);
    //         }
    //         $shopitem->setStatus('Cart');
    //         $shopitem->setPrice($item->getPrice());
    //         $shopitem->setCreatedAt(new \DateTime('now'));
    //         $shopitem->setModifiedAt(new \DateTime('now'));

    //         $em->persist($shopitem);
    //         $em->flush();

    //         return $this->redirectToRoute('shop');
    //     }catch(\Exception $e){
    //         return $e->getMessage();
    //     }
        
    // }

    // /**
    //  * @Route("/shop/del", name="shop_del")
    //  */
    // public function shopDel(Request $request)
    // {
    //     try{
    //         $user = $this->getUser();
            
    //         $em = $this->getDoctrine()->getManager();
            
    //         $item = $em->getRepository(ShopItem::class)->find($request->get('id'));

    //         $em->remove($item);
    //         $em->flush();

    //         return $this->redirectToRoute('cart');
    //     }catch(\Exception $e){
    //         return $e->getMessage();
    //     }
        
    // }

    // /**
    //  * @Route("/shop/edit", name="shop_edit")
    //  */
    // public function shopEdit(Request $request)
    // {
    //     try{
    //         $user = $this->getUser();
            
    //         $em = $this->getDoctrine()->getManager();
            
    //         $item = $em->getRepository(ShopItem::class)->find($request->get('id'));

    //         $player = explode('|',$request->get('selchar'));
    //         $item->setPlayer($player[0]);
    //         $item->setPlayerName($player[1]);
    //         $item->setAmount($request->get('amount'));

    //         $em->flush();

    //         return $this->redirectToRoute('cart');
    //     }catch(\Exception $e){
    //         return $e->getMessage();
    //     }
        
    // }

    /**
     * @Route("/shop/buy", name="shop_buy")
     */
    public function shopBuy(Request $request)
    {
        $con = $this->getDoctrine()->getConnection();
        $con->beginTransaction();
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $em_aion_gs = $this->getDoctrine()->getManager('aiongs');

            $item = $em->getRepository(Item::class)->findOneBy([
                'aion'  =>  $request->get('aionId')
            ]);

            $chk = substr($item->getAionid(),0,3);
            $vip = substr($item->getAionid(),3,2);

            //mail e inventory
            $unique_id = $em->getRepository(Inventory::class)->getUnique();
            $mail_id = $em->getRepository(Mail::class)->getMailId();

            if($user->getCoin() >= $item->getPrice()){
                $mail = new Mail();
                $message = new Message();

                if($chk == 519){
                    

                    $vip = substr($item->getAionid(),3,2);
                    $em->getRepository(User::class)->insertVip($user->getUsername(), $vip);

                    //history
                    $history = new History();
                    $history->setUser($user->getId());
                    $history->setCreatedAt(new \DateTime('now'));
                    $history->setModifiedAt(new \DateTime('now'));
                    $history->setItem($item->getAionId());
                    $history->setAmount($item->getAmount());
                    $history->setPrice($item->getPrice());
                    $history->setTotal($item->getPrice()*$item->getAmount());
                    $history->setGift(false);
                    $em->persist($history);

                    //message
                    if($user->getTagShop() == true){
                        $message = new Message();
                        $message->setUser($user->getId());
                        $message->setSubject('VIP Adquirido');
                        $message->setText('Você adquiriu o pacote '.$item->getName().'. A equipe do Infinity Aion agradece.');
                        $message->setUnread(true);
                        $message->setCreatedAt(new \DateTime('now'));
                        $message->setModifiedAt(new \DateTime('now'));
                        $em->persist($message);
                    }
                    
                }else{
                    //history
                    $history = new History();
                    $history->setUser($user->getId());
                    $history->setCreatedAt(new \DateTime('now'));
                    $history->setModifiedAt(new \DateTime('now'));
                    $history->setItem($item->getAionId());
                    $history->setAmount($item->getAmount());
                    $history->setPrice($item->getPrice());
                    $history->setUnique($unique_id[0]['unique_id']+1);
                    $history->setGift(false);
                    $history->setTotal($item->getPrice()*$item->getAmount());

                    $player = explode('|',$request->get('selchar'));
                    $history->setPlayer($player[0]);
                    $history->setPlayerName($player[1]);
                    $em->persist($history);

                    $inventory = new Inventory();
                    $inventory->setId($unique_id[0]['unique_id']+1);
                    $inventory->setItemId($item->getAionId());
                    $inventory->setItemCount($item->getAmount());
                    $inventory->setItemSkin($item->getAionId());
                    $inventory->setItemOwner($player[0]);
                    $em_aion_gs->persist($inventory);
                    // $em_aion_gs->flush();

                    //mail
                    $mail->setId($mail_id[0]['mail_id']+1);
                    $mail->setMailRecipientId($player[0]);
                    $mail->setSenderName('Infinity Aion');
                    $mail->setMailTitle('Aion Shop');
                    $mail->setMailMessage('Obrigado por adquirir esse item. A equipe do Infinity Aion agradece.');
                    $mail->setUnread(1);
                    $mail->setAttachedItemId(0);
                    $mail->setAttachedKinahCount(0);
                    $mail->setExpress(2);
                    $mail->setRecievedTime(new \DateTime('now'));
                    $em_aion_gs->persist($mail);

                    //message
                    if($user->getTagShop() == true){
                        $message = new Message();
                        $message->setUser($user->getId());
                        $message->setSubject('Item Adquirido');
                        $message->setText('Você adquiriu o item '.$item->getName().'. A equipe do Infinity Aion agradece.');
                        $message->setUnread(true);
                        $message->setCreatedAt(new \DateTime('now'));
                        $message->setModifiedAt(new \DateTime('now'));
                        $em->persist($message);
                    }
                }

                if($item->getPromo()){
                    $discount = $item->getPrice()*($item->getDiscount()/100);
                    $user->setCoin($user->getCoin() - ($item->getPrice() - $discount));
                }else{
                    $user->setCoin($user->getCoin() - $item->getPrice());    
                }

                $em->flush();
                $em_aion_gs->flush();

                $con->commit();
            } else {
                // print_r($item);
                // die();
            }
            
            return $this->redirectToRoute('shop');
        }catch(\Exception $e){
            $con->rollBack();
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }

    // /**
    //  * @Route("/shop/reset", name="shop_reset")
    //  */
    // public function shopReset()
    // {
    //     try{
    //         $user = $this->getUser();
            
    //         $em = $this->getDoctrine()->getManager();
            
    //         $item = $em->getRepository(ShopItem::class)->resetCart($user->getId());

    //         return $this->redirectToRoute('cart');
    //     }catch(\Exception $e){
    //         return $e->getMessage();
    //     }
        
    // }

    /**
     * @Route("/shop/gift", name="shop_gift")
     */
    public function shopGift(Request $request)
    {
        $con = $this->getDoctrine()->getConnection();
        $con->beginTransaction();
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $em_aion_gs = $this->getDoctrine()->getManager('aiongs');

            $item = $em->getRepository(Item::class)->findOneBy([
                'aion'  =>  $request->get('id_gift')
            ]);

            $chk = substr($item->getAionId(),0,3);
            $vip = substr($item->getAionId(),3,2);

            //gift target
            $char = $em->getRepository(User::class)->searchCharById($request->get('selgift'));
            $target = $em->getRepository(User::class)->findOneBy([
                'username'      =>  $char[0]["account_name"]
            ]);

            //mail e inventory
            $unique_id = $em->getRepository(Inventory::class)->getUnique();
            $mail_id = $em->getRepository(Mail::class)->getMailId();

            if($user->getCoin() >= $item->getPrice()){
                $mail = new Mail();
                $message = new Message();

                if($chk == 519){
                    $vip = substr($item->getAionid(),3,2);
                    $em->getRepository(User::class)->insertVip($target->getUsername(), $vip);

                    //history
                    $history = new History();
                    $history->setUser($user->getId());
                    $history->setCreatedAt(new \DateTime('now'));
                    $history->setModifiedAt(new \DateTime('now'));
                    $history->setItem($item->getAionId());
                    $history->setAmount($item->getAmount());
                    $history->setPrice($item->getPrice());
                    $history->setTotal($item->getPrice()*$item->getAmount());
                    $history->setGift(true);
                    $history->setGiftTo($target->getId());
                    $em->persist($history);

                    //message
                    if($target->getTagShop() == true){
                        $message = new Message();
                        $message->setUser($target->getId());
                        $message->setSubject('VIP Presenteado');
                        $message->setText('Você foi presentado com o pacote '.$item->getName().'. A equipe do Infinity Aion agradece.');
                        $message->setUnread(true);
                        $message->setCreatedAt(new \DateTime('now'));
                        $message->setModifiedAt(new \DateTime('now'));
                        $em->persist($message);
                    }
                    
                }else{
                    //history
                    $history = new History();
                    $history->setUser($user->getId());
                    $history->setCreatedAt(new \DateTime('now'));
                    $history->setModifiedAt(new \DateTime('now'));
                    $history->setItem($item->getAionId());
                    $history->setAmount($item->getAmount());
                    $history->setPrice($item->getPrice());
                    $history->setUnique($unique_id[0]['unique_id']+1);
                    $history->setGift(true);
                    $history->setGiftTo($target->getId());
                    $history->setTotal($item->getPrice()*$item->getAmount());

                    $player = explode('|',$request->get('selgift'));
                    $history->setPlayer($player[0]);
                    $history->setPlayerName($player[1]);
                    $em->persist($history);

                    $inventory = new Inventory();
                    $inventory->setId($unique_id[0]['unique_id']+1);
                    $inventory->setItemId($item->getAionId());
                    $inventory->setItemCount($item->getAmount());
                    $inventory->setItemSkin($item->getAionId());
                    $inventory->setItemOwner($player[0]);
                    $em_aion_gs->persist($inventory);
                    // $em_aion_gs->flush();

                    //mail
                    $mail->setId($mail_id[0]['mail_id']+1);
                    $mail->setMailRecipientId($player[0]);
                    $mail->setSenderName('Infinity Aion');
                    $mail->setMailTitle('Aion Shop');
                    $mail->setMailMessage('Você foi presenteado com esse item. A equipe do Infinity Aion agradece.');
                    $mail->setUnread(1);
                    $mail->setAttachedItemId(0);
                    $mail->setAttachedKinahCount(0);
                    $mail->setExpress(2);
                    $mail->setRecievedTime(new \DateTime('now'));
                    $em_aion_gs->persist($mail);

                    //message
                    if($target->getTagShop() == true){
                        $message = new Message();
                        $message->setUser($target->getId());
                        $message->setSubject('Item Presenteado');
                        $message->setText('Você foi presenteado com o item '.$item->getName().'. A equipe do Infinity Aion agradece.');
                        $message->setUnread(true);
                        $message->setCreatedAt(new \DateTime('now'));
                        $message->setModifiedAt(new \DateTime('now'));
                        $em->persist($message);
                    }
                }

                if($item->getPromo()){
                    $discount = $item->getPrice()*($item->getDiscount()/100);
                    $user->setCoin($user->getCoin() - ($item->getPrice() - $discount));
                }else{
                    $user->setCoin($user->getCoin() - $item->getPrice());    
                }

                $em->flush();
                $em_aion_gs->flush();

                $con->commit();
            } else {

            }
            
            return $this->redirectToRoute('shop');
        }catch(\Exception $e){
            $con->rollBack();
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }
}
