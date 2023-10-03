<?php

    namespace App\Controller\Api;

    use App\Entity\Role;
    use App\Repository\RoleRepository;
    use App\Service\RolesService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/api/role')]
    class RoleController extends AbstractController
    {
        #[Route('/', methods: ['GET'])]
        public function getRoles(RoleRepository $roleRepository, RolesService $rolesService): JsonResponse
        {
            return $this->json($rolesService->arrayRoles($roleRepository->findAll()));
        }

        #[Route('/new', methods: ['GET'])]
        public function createNewRole(RoleRepository $roleRepository, Request $request): JsonResponse
        {
            $newRole = new Role();
            $newRole->setName($request->get('name'));

            $roleRepository->save($newRole, true);

            return new JsonResponse(['role' => ['id' => $newRole->getId(), 'name' => $newRole->getName()]]);

        }

        #[Route('/{id}', methods: ['GET'])]
        public function getOneRole(int $id, RoleRepository $roleRepository, RolesService $rolesService): JsonResponse
        {
            $roleToReturn = $roleRepository->find($id);
            return $this->json($rolesService->oneRole($roleToReturn));
        }
    }
