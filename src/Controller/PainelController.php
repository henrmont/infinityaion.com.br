<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PainelController extends AbstractController
{
    /**
     * @Route("/painel", name="painel")
     */
    public function index()
    {
        return new Response('painel');
    }
}
