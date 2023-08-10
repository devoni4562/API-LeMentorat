<?php

    namespace App\Service;

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

    }