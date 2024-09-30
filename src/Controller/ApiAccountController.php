<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ApiAccountController extends AbstractController
{
    #[Route('/api/account/create', name: 'app_account_create', methods: ['POST','GET'])]
    public function createAccount(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        SluggerInterface $slugger
    ): Response {
        $data = array();

        // Récupérer les données du corps de la requête en JSON
        $requestContent = json_decode($request->getContent(), true);

        // Récupérer les fichiers depuis la requête
        $fileOne = $request->files->get('fileOne'); // fichier 1
        $fileTwo = $request->files->get('fileTwo'); // fichier 2

        // Vérifier que toutes les données nécessaires sont présentes
        $lastName = $request->get('lastName');
        $firstName = $request->get('firstName');
        $birthday = $request->get('birthday');
        $placeOfBirth = $request->get('placeOfBirth');
        $typeIdentity = $request->get('typeIdentity');
        $userId = $request->get('userId');
        
        // Vérifier que toutes les données nécessaires sont présentes
        if ($lastName && $firstName && $birthday && $placeOfBirth && $typeIdentity && $fileOne && $fileTwo && $userId) {
           

            // Rechercher l'utilisateur correspondant à l'ID fourni
            $user = $userRepository->find($userId);

            if ($user) {
                // Créer un nouvel objet Account et définir ses propriétés
                $account = new Account();
                $account->setLastName($lastName);
                $account->setFirstName($firstName);
                $account->setBirthday($birthday);
                $account->setPalceOfBirth($placeOfBirth);
                $account->setTypeIdentity($typeIdentity);
                $account->setUsers($user);

                // Gestion du premier fichier
                if ($fileOne) {
                    $originalFilename = pathinfo($fileOne->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$fileOne->guessExtension();

                    // Déplacer le fichier vers le répertoire uploads
                    try {
                        $fileOne->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Gérer l'erreur si le fichier ne peut pas être déplacé
                        $status = 500;
                        $message = "Erreur lors du téléchargement du fichier 1.";
                        $data['ACCOUNT'] = array(
                            'status' => $status,
                            'message' => $message,
                        );

                        return new Response(json_encode($data), $status, ['Content-Type' => 'application/json']);
                    }

                    // Enregistrer le nom du fichier dans la base de données
                    $account->setFileOne($newFilename);
                }

                // Gestion du deuxième fichier
                if ($fileTwo) {
                    $originalFilename = pathinfo($fileTwo->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$fileTwo->guessExtension();

                    // Déplacer le fichier vers le répertoire uploads
                    try {
                        $fileTwo->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Gérer l'erreur si le fichier ne peut pas être déplacé
                        $status = 500;
                        $message = "Erreur lors du téléchargement du fichier 2.";
                        $data['ACCOUNT'] = array(
                            'status' => $status,
                            'message' => $message,
                        );

                        return new Response(json_encode($data), $status, ['Content-Type' => 'application/json']);
                    }

                    // Enregistrer le nom du fichier dans la base de données
                    $account->setFileTwo($newFilename);
                }

                // Persister et enregistrer le compte dans la base de données
                $entityManager->persist($account);
                $entityManager->flush();

                // Préparer la réponse en cas de succès
                $status = 201;
                $message = "Le compte a été créé avec succès.";
                $data['ACCOUNT'] = array(
                    'status' => $status,
                    'message' => $message,
                    'accountId' => $account->getId(),
                );
            } else {
                // Utilisateur non trouvé
                $status = 404;
                $message = "Utilisateur introuvable.";
                $data['ACCOUNT'] = array(
                    'status' => $status,
                    'message' => $message,
                );
            }
        } else {
            // Si certaines données sont manquantes
            $status = 400;
            $message = "Tous les champs obligatoires doivent être renseignés.";
            $data['ACCOUNT'] = array(
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
