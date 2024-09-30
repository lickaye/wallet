<?php

namespace App\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiConnexionController extends AbstractController
{
    #[Route('/api/connexion', name: 'app_api_connexion')]
    public function index(): Response
    {


        $uuid = Uuid::uuid4()->toString();
        dump($uuid); 

       

        $transactionReference = uniqid(bin2hex(random_bytes(12)));
       dump(strtoupper($transactionReference));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://127.0.0.1:8000',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'apitoken-uuid: 6a6eab7d-00dc-45d8-a79d-cd679fe3fbb6'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        dd ($response);

        return $this->render('api_connexion/index.html.twig', [
            'controller_name' => 'ApiConnexionController',
        ]);
    }
}
