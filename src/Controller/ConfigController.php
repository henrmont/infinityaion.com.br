<?php

namespace App\Controller;

use App\Entity\Item;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ConfigController extends AbstractController
{
    /**
     * @Route("/config", name="config")
     */
    public function index(Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $msgs = $em->getRepository(User::class)->getTags($user->getId());

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

            return $this->render('painel/contents/config/config.html.twig', [
                'chars'     =>  $chars,
                'tags'                  =>  $msgs,
                'status_race'                  =>  $user->getRace(),
                'status_name'           =>  $user->getName(),
                'status_image'      =>  $user->getImage(),
                'status_coins'          =>  $user->getCoin(),
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
     * @Route("/config/user/info", name="config_user_info")
     */
    public function userInfo(Request $request, string $uploadDir, FileUploader $uploader)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $file = $request->files->get('file');

            $filen = $file->getClientOriginalName();
            $filex = explode(".",$filen);

            $filename = $filex[0].uniqid().'.'.$filex[1];

            $email = $em->getRepository(User::class)->findBy([
                'email'     =>  $request->get('email')
            ]);

            if(empty($email)){
                if((substr($filename, -3)=='jpg')||(substr($filename, -3)=='png')||(substr($filename, -4)=='jpeg')){
                    $uploader->upload($uploadDir, $file, $filename);
    
                    $update = $em->getRepository(User::class)->find($user->getId());
                    if($request->get('name') != ""){
                        $update->setName($request->get('name'));
                    }
                    if($request->get('email') != ""){
                        $update->setName($request->get('email'));
                    }
                    $update->setImage($filename);
                    $update->setModifiedAt(new \DateTime('now'));
    
                    $em->flush();

                    return $this->redirectToRoute('config');
                }else{
                    $this->addFlash(
                        'notice',
                        'Formato de arquivo inválido.'
                    );
                    return $this->redirectToRoute('site');
                }    
            }else{
                $this->addFlash(
                    'notice',
                    'Email já cadastrado.'
                );
                return $this->redirectToRoute('site');
            }
            
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
        
    }

    /**
     * @Route("/config/msn", name="config_msn")
     */
    public function msnConfig(Request $request)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $tag = $em->getRepository(User::class)->find($user->getId());

            if($request->get('feed') == 'on'){
                $tag->setTagFeed(true);
            }else{
                $tag->setTagFeed(false);
            }

            if($request->get('shop') == 'on'){
                $tag->setTagShop(true);
            }else{
                $tag->setTagShop(false);
            }

            if($request->get('coin') == 'on'){
                $tag->setTagCoin(true);
            }else{
                $tag->setTagCoin(false);
            }

            if($request->get('ticket') == 'on'){
                $tag->setTagTicket(true);
            }else{
                $tag->setTagTicket(false);
            }

            $em->flush();

            return $this->redirectToRoute('config');
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
        
    }

    /**
     * @Route("/config/redefinir/senha", name="config_redefinir_senha")
     */
    public function redefinirSenha(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        try{
            $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
            
            $user = $this->getUser();

            $em = $this->getDoctrine()->getManager();

            $rsenha = $em->getRepository(User::class)->find($user->getId());

            $rsenha->setPassword(
                $passwordEncoder->encodePassword(
                    $rsenha,
                    $request->get('pwd')
                )
            );
            $rsenha->setModifiedAt(new \DateTime('now'));
            $em->getRepository(User::class)->definePassword($user->getUsername(),(base64_encode(sha1($request->get('pwd'), true))));

            $em->flush();

            return $this->redirectToRoute('logout');
        }catch(\Exception $e){
            $this->addFlash(
                'notice',
                'Faça o login.'
            );
            return $this->redirectToRoute('site');
        }
        
    }
    
}
