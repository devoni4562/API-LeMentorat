<?php

    namespace App\Service;

    use App\Entity\Member;

    class MembersService
    {

        public function arrayMembers(array $members)
        {
            $data = [];
            foreach ($members as $member) {
                $data[] = [
                    'lastname' => $member->getLastName(),
                    'description' => $member->getDescription(),
                    'avatar' => $member->getAvatar(),
                    'pseudo' => $member->getPseudo(),
                    'firstname' => $member->getFirstName(),
                    'job' => [
                        'id' => $member->getJob()->getId(),
                        'name' => $member->getJob()->getName()
                    ],
                    'role' => $member->getRoles(),
                    'email' => $member->getEmail(),
                ];
            }
            return $data;
        }

        public function oneMember(Member $member)
        {
            $data[] = [
                'lastname' => $member->getLastName(),
                'description' => $member->getDescription(),
                'avatar' => $member->getAvatar(),
                'pseudo' => $member->getPseudo(),
                'firstname' => $member->getFirstName(),
                'job' => [
                    'id' => $member->getJob()->getId(),
                    'name' => $member->getJob()->getName()
                ],
                'role' => $member->getRoles(),
                'email' => $member->getEmail(),
            ];

            return $data;
        }
    }