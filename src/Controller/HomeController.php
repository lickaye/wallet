<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
           // Récupérer l'apitoken-uuid de l'en-tête de la requête
           $apiToken = $request->headers->get('apitoken-uuid');
        
           // Valider l'apiToken
           if (!$this->isValidApiToken($apiToken)) {
               return new JsonResponse(['error' => 'Invalid API token'], 403);
           }
   
           // Récupérer les produits (exemple fictif)
           $products = [
               ['id' => 1, 'name' => 'Product 1', 'price' => 10.99],
               ['id' => 2, 'name' => 'Product 2', 'price' => 29.99],
           ];
   
           return new JsonResponse($products);
       }
   
       private function isValidApiToken(?string $apiToken): bool
       {
           // Logique pour vérifier la validité du token (vous pouvez la personnaliser)
           return $apiToken === '6a6eab7d-00dc-45d8-a79d-cd679fe3fbb6';
       }
    
}
