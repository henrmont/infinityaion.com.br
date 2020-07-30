<?php

namespace App\Controller;

use App\Entity\History;
use App\Entity\HistoryCoin;
use App\Entity\Item;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**
     * @Route("/history", name="history")
     */
    public function index()
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $history = $em->getRepository(History::class)->searchHistoryItens($user->getId());

            $history_coin = $em->getRepository(HistoryCoin::class)->findBy([
                'user'      =>  $user->getId(),
                'status'    =>  'Aprove'
            ],[ 
                'modified_at' => 'DESC'
            ]
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

            return $this->render('painel/contents/history/history.html.twig', [
                'chars'     =>  $chars,
                'history_item'      =>  $history,
                'history_coin'      =>  $history_coin,
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
}
