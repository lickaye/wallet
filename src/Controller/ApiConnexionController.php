<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        dd($response);

        return $this->render('api_connexion/index.html.twig', [
            'controller_name' => 'ApiConnexionController',
        ]);
    }


    #[Route('/api/login', name: 'app_api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $data = array();

        // Récupérer les données du corps de la requête en JSON
        $requestContent = json_decode($request->getContent(), true);

        if (isset($requestContent['phone']) && isset($requestContent['password'])) {
            $phone = $requestContent['phone'];
            $password = $requestContent['password'];

            // Vérifier si l'utilisateur avec ce numéro de téléphone existe
            $user = $userRepository->findOneBy(['phone' => $phone]);

            if ($user) {
                // Vérifier si le mot de passe correspond
                if ($userPasswordHasher->isPasswordValid($user, $password)) {
                    // Le mot de passe est correct, connexion réussie
                    $status = 200;
                    $message = "Connexion réussie.";
                    $data['USER'] = array(
                        'status' => $status,
                        'message' => $message,
                        'user' => array(
                            'id' => $user->getId(),
                            'phone' => $user->getPhone(),
                            'uuid' => $user->getUuid(),
                            
                        ),
                    );
                } else {
                    // Mot de passe incorrect
                    $status = 401;
                    $message = "Mot de passe incorrect.";
                    $data['USER'] = array(
                        'status' => $status,
                        'message' => $message,
                    );
                }
            } else {
                // L'utilisateur avec ce numéro de téléphone n'existe pas
                $status = 404;
                $message = "Utilisateur introuvable avec ce numéro de téléphone.";
                $data['USER'] = array(
                    'status' => $status,
                    'message' => $message,
                );
            }
        } else {
            // Si les champs 'phone' ou 'password' ne sont pas présents
            $status = 400;
            $message = "Les champs phone et password sont requis.";
            $data['USER'] = array(
                'status' => $status,
                'message' => $message,
            );
        }

        // Retourner la réponse JSON
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    #[Route('/api/registration', name: 'app_api_registration', methods: ['POST', 'GET'])]
    public function registration(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        Request $request,
        UserRepository $userRepository // Ajouter le UserRepository ici
    ): Response {
        $data = array();
    
        // Récupérer les données du corps de la requête en JSON
        $requestContent = json_decode($request->getContent(), true);
    
        if (isset($requestContent['phone']) && isset($requestContent['password'])) {
            $phone = $requestContent['phone'];
            $password = $requestContent['password'];
    
            // Vérifier si le numéro de téléphone existe déjà
            $existingUser = $userRepository->findOneBy(['phone' => $phone]);
    
            if ($existingUser) {
                // Si l'utilisateur existe déjà avec ce numéro de téléphone
                $status = 400;
                $message = "Ce numéro de téléphone est déjà utilisé.";
                $data['USER'] = array(
                    'status' => $status,
                    'message' => $message,
                );
            } else {
                // Si le numéro de téléphone est unique, créer un nouvel utilisateur
                $user = new User();
                $uuid = Uuid::uuid4()->toString();
    
                // Hasher le mot de passe
                $cryptepassword = $userPasswordHasher->hashPassword(
                    $user,
                    $password
                );
    
                // Définir les propriétés de l'utilisateur
                $user->setPhone($phone);
                $user->setUuid($uuid);
                $user->setPassword($cryptepassword);
                $user->setRoles(["ROLE_USER"]);
    
                // Persister l'utilisateur dans la base de données
                $entityManager->persist($user);
                $entityManager->flush();
    
                $status = 200;
                $message = "Votre compte a été créé avec succès !";
                $data['USER'] = array(
                    'status' => $status,
                    'message' => $message,
                );
            }
        } else {
            // Si les données 'phone' ou 'password' ne sont pas présentes
            $status = 400;
            $message = "Les champs phone et password sont requis.";
            $data['USER'] = array(
                'status' => $status,
                'message' => $message,
            );
        }
    
        // Retourner la réponse JSON
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
    
}
