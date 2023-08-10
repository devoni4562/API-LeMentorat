<?php

    namespace App\Service;

    use App\Entity\Role;

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

    }