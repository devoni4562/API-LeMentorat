<?php

namespace App\Controller\Api;

use App\Repository\MemberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[
        Route('/api/admin'),
]
class AdminController extends AbstractController
{


    #[Route("/get_all_admin"), ]
    public function getAllAdmin(MemberRepository $memberRepository): JsonResponse
    {

        $admins = $memberRepository->getAdmins();

        $data = [];
        foreach ($admins as $admin) {
            $data[] = [
                'lastname' => $admin->getLastName(),
                'description' => $admin->getDescription(),
                'avatar' => $admin->getAvatar(),
                'pseudo' => $admin->getPseudo(),
                'firstname' => $admin->getFirstName(),
                'job' => [
                    'id' => $admin->getJob()->getId(),
                    'name' => $admin->getJob()->getName()
                ],
                'role' => $admin->getRoles(),
                'email' => $admin->getEmail(),
            ];
        }


        return new JsonResponse($data);

    }
}
