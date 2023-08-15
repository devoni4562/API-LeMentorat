<?php

    namespace App\Service;

    use App\Entity\Member;

    class MemberService
    {

        public function arrayMembers(array $members): array
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

        public function oneMember(Member $member): array
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