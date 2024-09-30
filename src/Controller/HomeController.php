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


       

        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://127.0.0.1:8000/api/registration',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "phone": "0600000000",
            "password": "password123"
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        echo $response;
        



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
