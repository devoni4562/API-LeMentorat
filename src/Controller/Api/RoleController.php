<?php

namespace App\Controller\Api;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/role')]
class RoleController extends AbstractController
{
    #[Route('/', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/RoleController.php',
        ]);
    }

    #[Route('/new', methods: ['GET'])]
    public function createNewRole(RoleRepository $roleRepository, Request $request): JsonResponse
    {
        $newRole = new Role();
        $newRole->setName($request->get('name'));

        $roleRepository->save($newRole, true);

        return new JsonResponse(['role'=>['id'=>$newRole->getId(), 'name'=>$newRole->getName()]]);

    }
}
