<?php

namespace App\Controller;

use App\Entity\CmsCarousel;
use App\Entity\CmsNotice;
use App\Entity\CmsResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    /**
     * @Route("/site/intro", name="site_intro")
     */
    public function intro()
    {
        return $this->render('site/index.html.twig', [
            'controller_name' => 'Intro',
        ]);
    }

    /**
     * @Route("/site", name="site")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

        $carousel = $em->getRepository(CmsCarousel::class)->findBy([
            'isActive'  =>  true
        ]);

        return $this->render('site/index.html.twig',[
            'carousel'  =>  $carousel
        ]);
    }

    /**
     * @Route("/site/isolate/{id}", name="site_isolate")
     */
    public function isolate($id)
    {
        $em = $this->getDoctrine()->getManager();

        $carousel = $em->getRepository(CmsCarousel::class)->findOneBy([
            'isActive'  =>  true,
            'id'        =>  $id
        ]);

        return $this->render('site/isolate/'.$id.'.html.twig', [
            'carousel'  =>  $carousel
        ]);
    }
}
