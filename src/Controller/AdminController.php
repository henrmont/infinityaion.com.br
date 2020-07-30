<?php

namespace App\Controller;

use App\Entity\HistoryCoin;
use App\Entity\Item;
use App\Entity\Feed;
use App\Entity\FeedComment;
use App\Entity\ItemType;
use App\Entity\Message;
use App\Entity\Report;
use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Entity\User;
use App\Entity\CmsCarousel;
use App\Entity\CmsNotice;
use App\Entity\CmsResource;
use App\Entity\History;
use App\Entity\Inventory;
use App\Form\CmsCarouselType;
use App\Form\CmsNoticeType;
use App\Form\CmsResourceType;
use App\Form\FormItem;
use App\Form\FormItemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN", message="Page not found.")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index(Request $request, SluggerInterface $slugger)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine();

            $select_itemtype = $em->getRepository(ItemType::class)->findAll();
            $select_item = $em->getRepository(Item::class)->findAll();
            $select_user = $em->getRepository(User::class)->findAll();
            // echo "<pre>";
            // print_r($select_item);
            // echo "</pre>";
            // die();


            /* Categoria */

            // Inc
            $itemtype = new ItemType();
            $itemtype_form = $this->createForm(FormItemType::class, $itemtype);
            $itemtype_form->handleRequest($request);
            if ($itemtype_form->isSubmitted() && $itemtype_form->isValid()) {
                $itemtype->setCreatedAt(new \DateTime('now'));
                $itemtype->setModifiedAt(new \DateTime('now'));

                $itemtype = $itemtype_form->getData();

                $em = $this->getDoctrine()->getManager();
                
                $em->persist($itemtype);
                $em->flush();

                return $this->redirectToRoute('admin');
            }

            /* Item */
            
            // Inc
            $item = new Item();
            $item_form = $this->createForm(FormItem::class, $item);
            $item_form->handleRequest($request);
            if ($item_form->isSubmitted() && $item_form->isValid()) {
                $item->setCreatedAt(new \DateTime('now'));
                $item->setModifiedAt(new \DateTime('now'));
                $item->setType($request->get('selcat'));

                /** @var UploadedFile $brochureFile */
                $imageFile = $item_form->get('image')->getData();

                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $imageFile->move(
                            $this->getParameter('item_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $item->setImage($newFilename);
                }

                $item = $item_form->getData();

                $em = $this->getDoctrine()->getManager();

                
                
                $em->persist($item);
                $em->flush();

                return $this->redirectToRoute('admin');
            }

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/admin.html.twig', [
                'chars'     =>  $chars,
                'users'     =>  $select_user,
                'cat_new'   =>  $itemtype_form->createView(),
                'cat_sel'   =>  $select_itemtype,
                'item_new'  =>  $item_form->createView(),
                'item_sel'  =>  $select_item,
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);

        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/itens", name="admin_itens")
     */
    public function adminItens(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $item = $em->getRepository(Item::class)->searchItem($request->get('search'), $request->get('category'));
            $cat = $em->getRepository(ItemType::class)->findAll();

            // echo "<pre>";
            // print_r($item);
            // echo "</pre>";
            // die();

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $item,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',30)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/itens/itens.html.twig', [
                'chars'     =>  $chars,
                'itens'             =>  $result,
                'category'          =>  $cat,
                'status_race'       =>  $user->getRace(),
                'status_name'       =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'      =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/edit/item", name="edit_item")
     */
    public function editItem(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $item = $em->getRepository(Item::class)->findBy([
                'aion'  =>  $request->get('id')
            ]);

            $item[0]->setType($request->get('edittype'));
            $item[0]->setLevel($request->get('editlevel'));
            $item[0]->setName($request->get('editname'));
            $item[0]->setPrice($request->get('editprice'));
            $item[0]->setDiscount($request->get('editdiscount'));
            $item[0]->setAmount($request->get('editamount'));
            $item[0]->setBbcode($request->get('editbbcode'));
            $item[0]->setModifiedAt(new \DateTime('now'));

            $em->flush();

            return $this->redirectToRoute('admin_itens');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/delete/item", name="delete_item")
     */
    public function deleteItem(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $item = $em->getRepository(Item::class)->find($request->get('id'));

            $em->remove($item);

            $em->flush();

            return $this->redirectToRoute('admin_itens');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/admin/reply", name="admin_reply")
     */
    public function adminReply(ContainerInterface $container,Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $history = $em->getRepository(History::class)->searchHistoryItens($request->get('search'));

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $history,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',6)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/itens/history.html.twig', [
                'chars'     =>  $chars,
                'itens'             =>  $result,
                'status_race'       =>  $user->getRace(),
                'status_name'       =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'      =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/admin/item/reply", name="admin_item_reply")
     */
    public function adminItemReply(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();
            $em_aion_gs = $this->getDoctrine()->getManager('aiongs');

            $item = $em->getRepository(History::class)->find($request->get('id'));

            $unique_id = $em->getRepository(Inventory::class)->getUnique();
            
            $inventory = new Inventory();
            $inventory->setId($unique_id[0]['unique_id']+1);
            $inventory->setItemId($item->getItem());
            $inventory->setItemCount($item->getAmount());
            $inventory->setItemSkin($item->getItem());
            $inventory->setItemOwner($item->getPlayer());
            $em_aion_gs->persist($inventory);
            $em_aion_gs->flush();

            return $this->redirectToRoute('admin_reply');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/promo/item", name="promo_item")
     */
    public function promoItem(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $item = $em->getRepository(Item::class)->find($request->get('id'));

            if($item->getPromo() == true){
                $item->setPromo(false);
            }else{
                $item->setPromo(true);
            }

            $em->flush();

            return $this->redirectToRoute('admin_itens');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/types", name="admin_types")
     */
    public function adminTypes(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $cat = $em->getRepository(ItemType::class)->findAll();

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $cat,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',6)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/itens/types.html.twig', [
                'chars'     =>  $chars,
                'category'             =>  $result,
                'status_race'       =>  $user->getRace(),
                'status_name'       =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'      =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/edit/type", name="edit_type")
     */
    public function editCat(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $type = $em->getRepository(ItemType::class)->find($request->get('id'));

            $type->setName($request->get('editname'));
            $type->setModifiedAt(new \DateTime('now'));

            $em->flush();

            return $this->redirectToRoute('admin_types');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/delete/type", name="delete_type")
     */
    public function deleteType(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $type = $em->getRepository(ItemType::class)->find($request->get('id'));

            $em->remove($type);

            $em->flush();

            return $this->redirectToRoute('admin_types');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/suport", name="admin_suport")
     */
    public function adminIndex(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $ticket_view = $em->getRepository(Ticket::class)->searchTicket($request->get('search'), null);

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $ticket_view,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',6)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/suport/suport.html.twig', [
                'chars'     =>  $chars,
                'data'          =>  $result,
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/suport/ticket/{id}", name="admin_ticket")
     */
    public function ticketIndex($id, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $ticket = $em->getRepository(Ticket::class)->selectedTicket($id);
            $ticketMessage = $em->getRepository(TicketMessage::class)->searchTicketMessager($id);

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();
            
            return $this->render('painel/contents/admin/suport/ticket.html.twig', [
                'chars'     =>  $chars,
                'ticket'    =>  $ticket,
                'messages'  =>  $ticketMessage,
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }

    /**
     * @Route("/suport/ticket/send/{id}", name="admin_ticket_send")
     */
    public function ticketSendIndex($id, Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $response = new TicketMessage();

            $response->setTicket($id);
            $response->setSender('Admin');
            $response->setMessage($request->get('response'));
            $response->setCreatedAt(new \DateTime('now'));
            $response->setModifiedAt(new \DateTime('now'));

            $em->persist($response);

            $target = $em->getRepository(User::class)->getUserByTicket($id);

            //message
            if($target[0]['tagTicket'] == true){
                $message = new Message();
                $message->setUser($target[0]['id']);
                $message->setSubject('Ticket Respondido');
                $message->setText('Seu ticket foi respondido.');
                $message->setUnread(true);
                $message->setCreatedAt(new \DateTime('now'));
                $message->setModifiedAt(new \DateTime('now'));
                $em->persist($message);
            }
            
            $em->flush();

            return $this->redirectToRoute('admin_suport');
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }

    /**
     * @Route("/suport/ticket/close/{id}", name="admin_ticket_close")
     */
    public function ticketCloseIndex($id, Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $close = $em->getRepository(Ticket::class)->find($id);

            $close->setStatus('Closed');
            $close->setModifiedAt(new \DateTime('now'));

            $em->flush();

            return $this->redirectToRoute('admin_suport');
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }

    /**
     * @Route("/coin", name="admin_coin")
     */
    public function coinIndex(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();
            
            $em = $this->getDoctrine()->getManager();

            $ticket_view = $em->getRepository(HistoryCoin::class)->searchCoins($request->get('search'));

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $ticket_view,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',30)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/coin/coin.html.twig', [
                'chars'     =>  $chars,
                'data'          =>  $result,
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/coin/aprove", name="admin_coin_aprove")
     */
    public function coinAproveIndex(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $close = $em->getRepository(HistoryCoin::class)->find($request->get('id'));

            $close->setStatus('Aprove');
            $close->setModifiedAt(new \DateTime('now'));

            $user = $em->getRepository(User::class)->find($request->get('user'));

            $user->setCoin($user->getCoin()+$close->getAmount());
            $user->setModifiedAt(new \DateTime('now'));

            //message
            if($user->getTagCoin() == true){
                $message = new Message();
                $message->setUser($user->getId());
                $message->setSubject('Infinity Coins Aprovada');
                $message->setText('Você adquiriu '.$close->getAmount().' Infinity Coins. A equipe do Infinity Aion agradece.');
                $message->setUnread(true);
                $message->setCreatedAt(new \DateTime('now'));
                $message->setModifiedAt(new \DateTime('now'));
                $em->persist($message);
            }

            $em->flush();

            return $this->redirectToRoute('admin_coin');
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }

    /**
     * @Route("/coin/remove", name="admin_coin_remove")
     */
    public function coinRemoveIndex(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $target = $em->getRepository(HistoryCoin::class)->find($request->get('id'));

            $em->remove($target);

            $em->flush();

            return $this->redirectToRoute('admin_coin');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/history", name="admin_history")
     */
    public function adminHistory(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $history = $em->getRepository(HistoryCoin::class)->historyCoins($request->get('search'));

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $history,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',8)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/coin/history.html.twig', [
                'chars'     =>  $chars,
                'coin'              =>  $result,
                'status_race'       =>  $user->getRace(),
                'status_name'       =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'      =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/posts", name="admin_posts")
     */
    public function adminPosts(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $report = $em->getRepository(Report::class)->searchReportPost();

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $report,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',8)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/feed/posts.html.twig', [
                'chars'     =>  $chars,
                'report'              =>  $result,
                'status_race'       =>  $user->getRace(),
                'status_name'       =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'      =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/comments", name="admin_comments")
     */
    public function adminComments(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $report = $em->getRepository(Report::class)->searchReportComment();

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $report,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',8)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/feed/comments.html.twig', [
                'chars'     =>  $chars,
                'report'              =>  $result,
                'status_race'       =>  $user->getRace(),
                'status_name'       =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'      =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/notify/posts", name="admin_notify_post")
     */
    public function adminNotifyPosts(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $feed = $em->getRepository(Feed::class)->find($request->get('pid'));
            $target = $em->getRepository(User::class)->find($request->get('id'));

            $feed->setIsActive(false);

            $msg = new Message();

            if($target->getIsReport() == true){
                $target->setIsActive(false);

                //message
                $msg->setUser($request->get('id'));
                $msg->setSubject('Usuário Bloqueado');
                $msg->setText('Sua conta está sendo bloqueada por apresentar uma reincidência de conduta inadequada ao utilizar o feed de notícias. Entre em contato com a administração.');
                $msg->setUnread(false);
                $msg->setCreatedAt(new \DateTime('now'));
                $msg->setModifiedAt(new \DateTime('now'));
                $em->persist($msg);

            }else{
                $target->setIsReport(true);

                //message
                $msg->setUser($request->get('id'));
                $msg->setSubject('Usuário Notificado');
                $msg->setText('Sua conta está sendo notificada por apresentar uma conduta inadequada ao utilizar o feed de notícias. Caso haja reincidência tomaremos medidas que devem impactar no uso da plataforma como um todo.');
                $msg->setUnread(false);
                $msg->setCreatedAt(new \DateTime('now'));
                $msg->setModifiedAt(new \DateTime('now'));
                $em->persist($msg);
            }

            $em->flush();

            return $this->redirectToRoute('admin');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/notify/comments", name="admin_notify_comment")
     */
    public function adminNotifyComents(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $comment = $em->getRepository(FeedComment::class)->find($request->get('pid'));
            $target = $em->getRepository(User::class)->find($request->get('id'));

            $comment->setIsActive(false);

            $msg = new Message();

            if($target->getIsReport() == true){
                $target->setIsActive(false);

                //message
                $msg->setUser($request->get('id'));
                $msg->setSubject('Usuário Bloqueado');
                $msg->setText('Sua conta está sendo bloqueada por apresentar uma reincidência de conduta inadequada ao utilizar o feed de notícias. Entre em contato com a administração.');
                $msg->setUnread(false);
                $msg->setCreatedAt(new \DateTime('now'));
                $msg->setModifiedAt(new \DateTime('now'));
                $em->persist($msg);

            }else{
                $target->setIsReport(true);

                //message
                $msg->setUser($request->get('id'));
                $msg->setSubject('Usuário Notificado');
                $msg->setText('Sua conta está sendo notificada por apresentar uma conduta inadequada ao utilizar o feed de notícias. Caso haja reincidência tomaremos medidas que devem impactar no uso da plataforma como um todo.');
                $msg->setUnread(false);
                $msg->setCreatedAt(new \DateTime('now'));
                $msg->setModifiedAt(new \DateTime('now'));
                $em->persist($msg);
            }

            $em->flush();

            return $this->redirectToRoute('admin');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/player", name="admin_player")
     */
    public function adminPlayer(ContainerInterface $container, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $player = $em->getRepository(User::class)->getPlayers($request->get('search'));

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $player,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',6)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/player/player.html.twig', [
                'chars'     =>  $chars,
                'data'          =>  $result,
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/all/vip", name="admin_allvip")
     */
    public function adminAllVip(Request $request)
    {
        try{
            $em = $this->getDoctrine()->getManager();

            $user = $em->getRepository(User::class)->findAll();

            foreach($user as $item){
                $em->getRepository(User::class)->insertVip($item->getUsername(),$request->get('days'));
                $em->flush();
            }

            return $this->redirectToRoute('admin');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/carousel", name="admin_cms_carousel")
     */
    public function cmsCarousel(ContainerInterface $container, Request $request, SluggerInterface $slugger)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $carousel = new CmsCarousel();
            $carousel_form = $this->createForm(CmsCarouselType::class, $carousel);
            $carousel_form->handleRequest($request);
            if ($carousel_form->isSubmitted()) {
                $carousel->setCreatedAt(new \DateTime('now'));
                $carousel->setModifiedAt(new \DateTime('now'));
                $carousel->setIsActive(true);

                /** @var UploadedFile $brochureFile */
                $imageFile = $carousel_form->get('image_full')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('cms_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $carousel->setImageFull($newFilename);
                }

                /** @var UploadedFile $brochureFile */
                $imageFile2 = $carousel_form->get('image_small')->getData();
                if ($imageFile2) {
                    $originalFilename2 = pathinfo($imageFile2->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename2 = $slugger->slug($originalFilename2);
                    $newFilename2 = $safeFilename2.'-'.uniqid().'.'.$imageFile2->guessExtension();

                    try {
                        $imageFile2->move(
                            $this->getParameter('cms_directory'),
                            $newFilename2
                        );
                    } catch (FileException $e) {
                    }

                    $carousel->setImageSmall($newFilename2);
                }

                $carousel = $carousel_form->getData();

                $em->persist($carousel);
                $em->flush();

                return $this->redirectToRoute('admin_cms_carousel');
            }

            $carousel_list = $em->getRepository(CmsCarousel::class)->searchCarousel($request->get('search'));

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $carousel_list,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',6)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/cms/carousel.html.twig', [
                'chars'     =>  $chars,
                'data'          =>  $result,
                'form'   =>  $carousel_form->createView(),
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }

    /**
     * @Route("/cms/carousel/edit", name="admin_cms_carousel_edit")
     */
    public function adminCarouselEdit(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $carousel = $em->getRepository(CmsCarousel::class)->find($request->get('id'));

            $carousel->setTitle($request->get('edittitle'));
            $carousel->setText($request->get('edittext'));

            $em->flush();

            return $this->redirectToRoute('admin_cms_carousel');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/carousel/active", name="admin_cms_carousel_active")
     */
    public function adminCarouselActive(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $carousel = $em->getRepository(CmsCarousel::class)->find($request->get('id'));

            if($carousel->getIsActive() == true){
                $carousel->setIsActive(false);    
            }else{
                $carousel->setIsActive(true);    
            }

            $em->flush();

            return $this->redirectToRoute('admin_cms_carousel');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/carousel/delete", name="admin_cms_carousel_del")
     */
    public function adminCarouselDelete(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $carousel = $em->getRepository(CmsCarousel::class)->find($request->get('id'));

            $em->remove($carousel);

            $em->flush();

            return $this->redirectToRoute('admin_cms_carousel');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/notice", name="admin_cms_notice")
     */
    public function cmsNotice(ContainerInterface $container, Request $request, SluggerInterface $slugger)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $notice = new CmsNotice();
            $notice_form = $this->createForm(CmsNoticeType::class, $notice);
            $notice_form->handleRequest($request);
            if ($notice_form->isSubmitted()) {
                $notice->setCreatedAt(new \DateTime('now'));
                $notice->setModifiedAt(new \DateTime('now'));
                $notice->setIsActive(true);

                /** @var UploadedFile $brochureFile */
                $imageFile = $notice_form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('cms_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $notice->setImage($newFilename);
                }

                /** @var UploadedFile $brochureFile */
                $imageFile = $notice_form->get('image_small')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('cms_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $notice->setImageSmall($newFilename);
                }

                $notice = $notice_form->getData();

                $em->persist($notice);
                $em->flush();

                return $this->redirectToRoute('admin_cms_notice');
            }

            $notice_list = $em->getRepository(CmsNotice::class)->searchNotice($request->get('search'));

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $notice_list,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',6)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/cms/notice.html.twig', [
                'chars'     =>  $chars,
                'data'          =>  $result,
                'form'   =>  $notice_form->createView(),
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }

    /**
     * @Route("/cms/notice/edit", name="admin_cms_notice_edit")
     */
    public function adminNoticeEdit(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $notice = $em->getRepository(CmsNotice::class)->find($request->get('id'));

            $notice->setTitle($request->get('edittitle'));

            $em->flush();

            return $this->redirectToRoute('admin_cms_notice');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/notice/active", name="admin_cms_notice_active")
     */
    public function adminNoticeActive(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $notice = $em->getRepository(CmsNotice::class)->find($request->get('id'));

            if($notice->getIsActive() == true){
                $notice->setIsActive(false);    
            }else{
                $notice->setIsActive(true);    
            }

            $em->flush();

            return $this->redirectToRoute('admin_cms_notice');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/notice/delete", name="admin_cms_notice_del")
     */
    public function adminNoticeDelete(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $notice = $em->getRepository(CmsNotice::class)->find($request->get('id'));

            $em->remove($notice);

            $em->flush();

            return $this->redirectToRoute('admin_cms_notice');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/resource", name="admin_cms_resource")
     */
    public function cmsResource(ContainerInterface $container, Request $request, SluggerInterface $slugger)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $resource = new CmsResource();
            $resource_form = $this->createForm(CmsResourceType::class, $resource);
            $resource_form->handleRequest($request);
            if ($resource_form->isSubmitted()) {
                $resource->setCreatedAt(new \DateTime('now'));
                $resource->setModifiedAt(new \DateTime('now'));
                $resource->setIsActive(true);

                /** @var UploadedFile $brochureFile */
                $imageFile = $resource_form->get('image')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('cms_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $resource->setImage($newFilename);
                }

                /** @var UploadedFile $brochureFile */
                $imageFile = $resource_form->get('image_small')->getData();
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('cms_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                    }

                    $resource->setImageSmall($newFilename);
                }

                $resource = $resource_form->getData();

                $em->persist($resource);
                $em->flush();

                return $this->redirectToRoute('admin_cms_resource');
            }

            $resource_list = $em->getRepository(CmsResource::class)->searchResource($request->get('search'));

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $resource_list,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',6)
            );

            $promo = $em->getRepository(Item::class)->findBy([
                'promo'     =>  true
            ]);
            $players = $em->getRepository(User::class)->searchChar($user->getUsername());
            $expire = $em->getRepository(User::class)->searchExpire($user->getUsername());
            if ($expire[0]['expire']) {
                $dateexpire = explode('-',$expire[0]['expire']);
                $data = ($dateexpire[2]."/".$dateexpire[1]."/".$dateexpire[0]);
            } else {
                $data = 'Sem VIP';
            }
            $chars = $em->getRepository(User::class)->searchPlayers();

            return $this->render('painel/contents/admin/cms/resource.html.twig', [
                'chars'     =>  $chars,
                'data'          =>  $result,
                'form'   =>  $resource_form->createView(),
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }

    /**
     * @Route("/cms/resource/edit", name="admin_cms_resource_edit")
     */
    public function adminResourceEdit(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $resource = $em->getRepository(CmsResource::class)->find($request->get('id'));

            $resource->setTitle($request->get('edittitle'));

            $em->flush();

            return $this->redirectToRoute('admin_cms_resource');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/resource/active", name="admin_cms_resource_active")
     */
    public function adminResourceActive(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $resource = $em->getRepository(CmsResource::class)->find($request->get('id'));

            if($resource->getIsActive() == true){
                $resource->setIsActive(false);    
            }else{
                $resource->setIsActive(true);    
            }

            $em->flush();

            return $this->redirectToRoute('admin_cms_resource');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @Route("/cms/resource/delete", name="admin_cms_resource_del")
     */
    public function adminResourceDelete(Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $resource = $em->getRepository(CmsResource::class)->find($request->get('id'));

            $em->remove($resource);

            $em->flush();

            return $this->redirectToRoute('admin_cms_resource');
        }catch(\Exception $e){
            return $e->getMessage();
        }
    }

    // /**
    //  * @Route("/ajust/coin", name="admin_ajust_coin")
    //  */
    // public function adminAjustCoin(Request $request)
    // {
    //     try{
    //         $em = $this->getDoctrine()->getManager();

    //         $history =  $em->getRepository(HistoryCoin::class)->searchAproveItens();
    //         $ajust = $em->getRepository(User::class)->findAll();

    //         $donates = [];

    //         foreach($ajust as $user){
    //             foreach($history as $item){
    //                 if($user->getId() == $item["user_id"]){
    //                     $donates[$item['user_id']] = $item['amount'];
    //                 }
    //             }
    //         }

    //         foreach($donates as $chv => $vlr){
    //             $ajust = $em->getRepository(User::class)->find($chv);
    //             $ajust->setCoin($ajust->getCoin() + ($vlr/10));
    //             $em->flush();
    //         }

    //         return $this->redirectToRoute('admin');
    //     }catch(\Exception $e){
    //         return $e->getMessage();
    //     }
    // }

}
