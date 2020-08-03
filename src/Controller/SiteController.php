<?php

namespace App\Controller;

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
        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }

    /**
     * @Route("/site/isolate", name="site_isolate")
     */
    public function isolate()
    {
        return $this->render('site/isolate.html.twig', [
            'controller_name' => 'SiteController',
        ]);
    }
}
