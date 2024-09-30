<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiTransactionController extends AbstractController
{
    #[Route('/api/transaction', name: 'app_api_transaction')]
    public function index(): Response
    {
        return $this->render('api_transaction/index.html.twig', [
            'controller_name' => 'ApiTransactionController',
        ]);
    }
}
