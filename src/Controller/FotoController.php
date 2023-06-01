<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FotoController extends AbstractController
{
    #[Route('/', name: 'app_foto')]
    public function index(): Response
    {
        return $this->render('foto/index.html.twig', [
            'controller_name' => 'FotoController',
        ]);
    }
}
