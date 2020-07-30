<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message")
     */
    public function index(ContainerInterface $container, Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $message = $em->getRepository(Message::class)->findBy([
                'user'          =>  $user->getId()
            ],[
                'id'            =>  'DESC'
            ]);

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $message,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',7)
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

            return $this->render('painel/contents/message/message.html.twig', [
                'chars'     =>  $chars,
                'data'      =>  $result,
                'status_race'      =>  $user->getRace(),
                'status_name'      =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'     =>  $user->getCoin(),
                'promo'     =>  $promo,
                'players'   =>  $players,
                'expire'    =>  $data
            ]);
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }

    /**
     * @Route("/message/view/{id}", name="message_view")
     */
    public function messageViewIndex($id)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $em = $this->getDoctrine()->getManager();

            $message = $em->getRepository(Message::class)->find($id);
            
            return $this->render('painel/contents/message/message_view.html.twig', [
                'data'    =>  $message
            ]);
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        } 
        
    }

    /**
     * @Route("/message/del/{id}", name="message_del")
     */
    public function messageDelIndex($id)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $em = $this->getDoctrine()->getManager();

            $message = $em->getRepository(Message::class)->find($id);

            $em->remove($message);

            $em->flush();

            return $this->redirectToRoute('message');
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }
}
