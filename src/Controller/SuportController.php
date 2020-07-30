<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\TicketType;
use App\Entity\Ticket;
use App\Entity\TicketMessage;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SuportController extends AbstractController
{
    /**
     * @Route("/suport", name="suport")
     */
    public function index(ContainerInterface $container, Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $ticket = new Ticket();
            $ticket_form = $this->createForm(TicketType::class, $ticket);
            $ticket_form->handleRequest($request);
            if ($ticket_form->isSubmitted() && $ticket_form->isValid()) {
                $ticket->setCreatedAt(new \DateTime('now'));
                $ticket->setModifiedAt(new \DateTime('now'));
                $ticket->setStatus('Open');
                $ticket->setUser($user->getUsername());

                $ticket = $ticket_form->getData();
                
                $em->persist($ticket);
                $em->flush();

                return $this->redirectToRoute('suport');
            }

            $ticket_view = $em->getRepository(Ticket::class)->searchTicket($request->get('search'), $user->getUsername());

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

            return $this->render('painel/contents/suport/suport.html.twig', [
                'chars'     =>  $chars,
                'ticket_new'    =>  $ticket_form->createView(),
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
     * @Route("/suport/ticket/{id}", name="ticket")
     */
    public function ticketIndex($id, Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
            
            return $this->render('painel/contents/suport/ticket.html.twig', [
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
     * @Route("/suport/ticket/send/{id}", name="ticket_send")
     */
    public function ticketSendIndex($id, Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $response = new TicketMessage();

            $response->setTicket($id);
            $response->setSender($user->getUsername());
            $response->setMessage($request->get('response'));
            $response->setCreatedAt(new \DateTime('now'));
            $response->setModifiedAt(new \DateTime('now'));

            $em->persist($response);
            $em->flush();

            return $this->redirectToRoute('suport');
        }catch(\Exception $e){
            return $e->getMessage();
        } 
        
    }
}
