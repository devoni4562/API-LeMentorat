<?php

namespace App\Controller\Api;

use App\Entity\Member;
use App\Repository\MemberRepository;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/members')]
class MemberController extends AbstractController
{

#[Route("/staff", methods: ['GET'])]
    public function getStaff(MemberRepository $memberRepository):JsonResponse{

        $staff = $memberRepository->findAll();
        $data = [];
    foreach ($staff as $member) {
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
    return new JsonResponse($data);
    }

    //----------------------------------------------MENTORS-----------------------------------------------------------//
    #[Route('/mentors', methods: ['GET'])]
    public function getMentors(MemberRepository $memberRepository): JsonResponse
    {

        $mentors = $memberRepository->getByJobId(2);
        $data = [];
        foreach ($mentors as $mentor) {
            $data[] = [
                'lastname' => $mentor->getLastName(),
                'description' => $mentor->getDescription(),
                'avatar' => $mentor->getAvatar(),
                'pseudo' => $mentor->getPseudo(),
                'firstname' => $mentor->getFirstName(),
                'job' => [
                    'id' => $mentor->getJob()->getId(),
                    'name' => $mentor->getJob()->getName()
                ],
                'role' => $mentor->getRoles(),
                'email' => $mentor->getEmail(),
            ];
        }


        return new JsonResponse($data);

    }

    //---------------------------------------------WITNESSES----------------------------------------------------------//

    #[Route('/witnesses', methods: ['GET'])]
    public function getWitnesses(MemberRepository $memberRepository): JsonResponse
    {
        $witnesses = $memberRepository->getByJobId(3);
        $data = [];
        foreach ($witnesses as $witness) {
            $data[] = [
                'lastname' => $witness->getLastName(),
                'description' => $witness->getDescription(),
                'avatar' => $witness->getAvatar(),
                'pseudo' => $witness->getPseudo(),
                'firstname' => $witness->getFirstName(),
                'job' => [
                    'id' => $witness->getJob()->getId(),
                    'name' => $witness->getJob()->getName()
                ],
                'role' => $witness->getRoles(),
                'email' => $witness->getEmail(),
            ];
        }


        return new JsonResponse($data);
    }

    #[Route('/new')]
public function createNewMember(RoleRepository $roleRepository, MemberRepository $memberRepository, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
{
    $newMember = new Member();
    $plainPassword = 'Mentorat38300!';
    $hashedPassword = $passwordHasher->hashPassword($newMember, $plainPassword);

    $newMember->setJob($roleRepository->find(1))
        ->setEmail('puppomaxence2@gmail.com')
        ->setRoles(["ROLE_ADMIN"])
        ->setPassword($hashedPassword)
        ->setLastName('Puppo')
        ->setFirstName('Maxence')
        ->setPseudo('MaxenceP')
        ->setAvatar('maxence.jpg')
        ->setDescription('Co-fondateur et responsable du succès client');

    $memberRepository->save($newMember, true);

    return new JsonResponse(['member' => $newMember]);
}

}
