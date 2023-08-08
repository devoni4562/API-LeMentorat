<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Repository\MemberRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LoginController extends AbstractController
{

    #[Route('/api/login', methods: ['POST'])]

    public function login(Request $request,UserPasswordHasherInterface $hasher, MemberRepository $memberRepository, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        $formData = json_decode($request->getContent());

        $user = $memberRepository->findByEmail($formData->username);


        if ($user === null) {
            $response = [
                'message' => 'Utilisateur non trouvé.',
            ];
        } else {
            if ($hasher->isPasswordValid($user, $formData->password)) {
                $token = $JWTManager->create($user);
                $admin = [
                    'id' => $user->getId(),
                    'lastname' => $user->getLastName(),
                    'description' => $user->getDescription(),
                    'avatar' => $user->getAvatar(),
                    'pseudo' => $user->getPseudo(),
                    'firstname' => $user->getFirstName(),
                    'job' => [
                        'id' => $user->getJob()->getId(),
                        'name' => $user->getJob()->getName()
                    ],
                    'role' => $user->getRoles(),
                    'email' => $user->getEmail(),
                ];
                $response = [

                    'message' => 'Connexion réussie.',
                    'admin' => $admin,
                    'token' => $token
                ];
            } else {
                $response = [
                    'message' => 'Mot de passe incorrect.',
                ];
            }
        }

        return new JsonResponse($response);
    }

}
