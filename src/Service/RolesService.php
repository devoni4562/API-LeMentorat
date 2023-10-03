<?php

    namespace App\Service;

    use App\Entity\Role;
    use App\Repository\RoleRepository;
    use Symfony\Component\HttpFoundation\Request;

    class RolesService
    {

        public function arrayRoles(array $roles)
        {
            $data = [];
            foreach ($roles as $role) {
                $data[] = [
                    'id' => $role->getId(),
                    'name' => $role->getName()
                ];
            }
            return $data;
        }

        public function oneRole(Role $role)
        {
            $data = [
                'id' => $role->getId(),
                'name' => $role->getName()
            ];
            return $data;
        }

        public function newJob(Request $request, RoleRepository $roleRepository): Role
        {
            $formData = json_decode($request->getContent());

            $newRole = new Role();
            $newRole->setName($formData->name);

            $roleRepository->save($newRole, true);

            return $newRole;
        }

        public function updateJob(Request $request, int $id, RoleRepository $roleRepository, FileService $fileService, string $directory): ?Role
        {
            $formData = json_decode($request->getContent());
            $roleToUpdate = $roleRepository->find($id);
            $oldDirectory = $directory . str_ireplace(' ', '_', $roleToUpdate->getName());

            $roleToUpdate->setName($formData->name);

            $roleRepository->save($roleToUpdate, true);

            $newDirectory = $directory . str_ireplace(' ', '_', $roleToUpdate->getName());
            $fileService->renameDirectory($oldDirectory, $newDirectory);

            return $roleToUpdate;
        }

        public function deleteJob(int $id, RoleRepository $roleRepository): void
        {
            $roleToDelete = $roleRepository->find($id);
            $roleRepository->remove($roleToDelete, true);
        }

    }