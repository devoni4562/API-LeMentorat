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

        public function newJob(Request $request, RoleRepository $roleRepository)
        {
            $formData = json_decode($request->getContent());

            $newRole = new Role();
            $newRole->setName($formData->name);

            $roleRepository->save($newRole, true);

            return $newRole;
        }

    }