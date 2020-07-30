<?php

namespace App\Controller;

use App\Form\FeedType;
use App\Entity\Feed;
use App\Entity\FeedComment;
use App\Entity\Item;
use App\Entity\Message;
use App\Entity\Report;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FeedController extends AbstractController
{
    /**
     * @Route("/feed", name="feed")
     */
    public function index(ContainerInterface $container, Request $request, SluggerInterface $slugger)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $race = $em->getRepository(User::class)->searchRace($user->getUsername());
            if(!empty($race[0])){
                $setRace = $em->getRepository(User::class)->find($user->getId());
                $setRace->setRace($race[0]['race']);

                $em->flush();
            }

            
            $feeds = $em->getRepository(Feed::class)->searchFeed();
            $comments = $em->getRepository(FeedComment::class)->searchComment();
            $report = $em->getRepository(Report::class)->findAll();
            

            $feed = new Feed();
            $feed_form = $this->createForm(FeedType::class, $feed);
            $feed_form->handleRequest($request);
            if ($feed_form->isSubmitted() && $feed_form->isValid()) {
                $feed->setCreatedAt(new \DateTime('now'));
                $feed->setModifiedAt(new \DateTime('now'));
                $feed->setUser($user->getId());
                $feed->setIsActive(true);

                

                /** @var UploadedFile $brochureFile */
                $imageFile = $feed_form->get('image')->getData();

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
                            $this->getParameter('image_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $feed->setImage($newFilename);
                }

                $feed = $feed_form->getData();

                $em = $this->getDoctrine()->getManager();
                
                $em->persist($feed);
                $em->flush();

                return $this->redirectToRoute('feed');
            }

            $pagenator = $container->get('knp_paginator');
            $result = $pagenator->paginate(
                $feeds,
                $request->query->getInt('page',1),
                $request->query->getInt('limit',10)
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

            // print_r($data);
            // die();

            return $this->render('painel/contents/feed/feed.html.twig', [
                'chars'         =>  $chars,
                'post'          =>  $feed_form->createView(),
                'feeds'         =>  $result,
                'comments'      =>  $comments,
                'user'          =>  $user->getId(),
                'report'        =>  $report,
                'status_race'   =>  $user->getRace(),
                'status_name'   =>  $user->getName(),
                'status_image'  =>  $user->getImage(),
                'status_coins'  =>  $user->getCoin(),
                'promo'         =>  $promo,
                'players'       =>  $players,
                'expire'        =>  $data
            ]);

            // return $this->render('painel/painel.html.twig');
        }catch(\Exception $e){
            // $this->addFlash(
            //     'notice',
            //     'Faça o login.'
            // );
            // return $this->redirectToRoute('site');
        }
    }

    /**
     * @Route("/feed/full/{id}", name="feed_full")
     */
    public function fullFeedIndex($id)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $feeds = $em->getRepository(Feed::class)->searchFeed($id);
            $comments = $em->getRepository(FeedComment::class)->searchComment($id);
            $report = $em->getRepository(Report::class)->findAll();

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

            return $this->render('painel/contents/feed/feed_full.html.twig', [
                'chars'     =>  $chars,
                'feeds'     =>  $feeds,
                'comments'  =>  $comments,
                'user'      =>  $user->getId(),
                'report'    =>  $report,
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
     * @Route("/feed/inc/{id}", name="feed_inc")
     */
    public function feedInc($id, Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $feedinc = new FeedComment();

            $feedinc->setUser($user->getId());
            $feedinc->setFeed($id);
            $feedinc->setText($request->get('comment'));
            $feedinc->setIsActive(true);
            $feedinc->setCreatedAt(new \DateTime('now'));
            $feedinc->setModifiedAt(new \DateTime('now'));

            $em->persist($feedinc);

            $target = $em->getRepository(User::class)->getUserByMsg($id);

            // print_r($target);
            // die();

            //message
            if($target[0]['tagFeed'] == true){
                $message = new Message();
                $message->setUser($target[0]['id']);
                $message->setSubject('Post Comentado');
                $message->setText('Seu post foi comentado por '.$user->getName().'.');
                $message->setUnread(true);
                $message->setCreatedAt(new \DateTime('now'));
                $message->setModifiedAt(new \DateTime('now'));
                $em->persist($message);
            }

            $em->flush();

            if($request->get('direct') == 'full'){
                return $this->redirectToRoute('feed_full',[
                    'id'    =>  $id
                ]); 
            }

            return $this->redirectToRoute('feed');
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
            // return $e->getMessage();
        }
    }

    /**
     * @Route("/feed/edit", name="feed_edit")
     */
    public function feedEdit(Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $feededit = $em->getRepository(Feed::class)->find($request->get('id'));

            $feededit->setText($request->get('comment'));
            $feededit->setModifiedAt(new \DateTime('now'));

            $em->flush();

            if($request->get('direct') == 'full'){
                return $this->redirectToRoute('feed_full',[
                    'id'    =>  $request->get('id')
                ]); 
            }

            return $this->redirectToRoute('feed');
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }

    /**
     * @Route("/feed/disable/{id}", name="feed_disable")
     */
    public function feedDisable($id)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $feeddisable = $em->getRepository(Feed::class)->find($id);

            $feeddisable->setIsActive(false);

            $em->flush();

            return $this->redirectToRoute('feed');
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }

    /**
     * @Route("/feed/report/{id}", name="feed_report")
     */
    public function feedReport($id, Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $feedreport = new Report();

            $feedreport->setPid($id);
            $feedreport->setUser($user->getId());
            $feedreport->setText($request->get('report'));
            $feedreport->setType('Post');
            $feedreport->setCreatedAt(new \DateTime('now'));
            $feedreport->setModifiedAt(new \DateTime('now'));

            $em->persist($feedreport);
            $em->flush();

            if($request->get('direct') == 'full'){
                return $this->redirectToRoute('feed_full',[
                    'id'    =>  $request->get('post')
                ]); 
            }

            return $this->redirectToRoute('feed'); 
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }

    /**
     * @Route("/comment/disable/{id}", name="comment_disable")
     */
    public function commentDisable($id)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $commentdisable = $em->getRepository(FeedComment::class)->find($id);

            $commentdisable->setIsActive(false);

            $em->flush();

            return $this->redirectToRoute('feed');
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }

    /**
     * @Route("/comment/disable/{id}/{feed}", name="comment_disable_full")
     */
    public function commentFullDisable($id,$feed)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $commentdisable = $em->getRepository(FeedComment::class)->find($id);

            $commentdisable->setIsActive(false);

            $em->flush();

            return $this->redirectToRoute('feed_full',[
                'id'    =>  $feed
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
     * @Route("/comment/report/{id}", name="comment_report")
     */
    public function commentReport($id, Request $request)
    {
        try{
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $commentreport = new Report();

            $commentreport->setPid($id);
            $commentreport->setUser($user->getId());
            $commentreport->setText($request->get('report'));
            $commentreport->setType('Comment');
            $commentreport->setCreatedAt(new \DateTime('now'));
            $commentreport->setModifiedAt(new \DateTime('now'));

            $em->persist($commentreport);
            $em->flush();

            if($request->get('direct') == 'full'){
                return $this->redirectToRoute('feed_full',[
                    'id'    =>  $request->get('post')
                ]); 
            }

            return $this->redirectToRoute('feed'); 
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
    }

}
