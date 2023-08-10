<?php

    namespace App\Controller\Api;

    use App\Entity\Member;
    use App\Repository\MemberRepository;
    use App\Repository\RoleRepository;
    use App\Service\FileUploader;
    use App\Service\MembersService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;

    #[
        Route('/api/admin'),
    ]
    class AdminController extends AbstractController
    {


        #[Route("/get_all_admin"), ]
        public function getAllAdmin(MemberRepository $memberRepository, MembersService $membersService): JsonResponse
        {

            $admins = [];
            foreach ($memberRepository->findAll() as $member) {
                if (in_array('ROLE_ADMIN', $member->getRoles(), true)) {

                    $admins[] = $member;
                }
            }
            $data = $membersService->arrayMembers($admins);

            return new JsonResponse($data);

        }

        #[Route('/member/new')]
        public function createNewMember(FileUploader $fileUploader, RoleRepository $roleRepository, MemberRepository $memberRepository, Request $request, UserPasswordHasherInterface $passwordHasher, MembersService $membersService): JsonResponse
        {
            $newMember = new Member();

            if ($request->get('isAdmin')) {
                $plainPassword = $request->get('password');
                $hashedPassword = $passwordHasher->hashPassword($newMember, $plainPassword);
                $newMember
                    ->setRoles(["ROLE_ADMIN"])
                    ->setPassword($hashedPassword);
            }

            $newMember
                ->setJob($roleRepository->find(intval($request->get('jobId'))))
                ->setEmail($request->get('email'))
                ->setLastName($request->get('lastname'))
                ->setFirstName($request->get('firstname'))
                ->setPseudo($request->get('pseudo'))
                ->setDescription($request->get('description'));

            if ($request->files->get('avatar') !== null) {
                $newMember->setAvatar($fileUploader->upload($request->files->get('avatar'), $this->getParameter('avatar_img_dir') . str_ireplace(' ', '_', $newMember->getJob()->getName()) . '/' . $newMember->getEmail() . '/', $newMember->getPseudo()));
            }


            $memberRepository->save($newMember, true);

            $data = $membersService->oneMember($newMember);

            return new JsonResponse($data);
        }
    }
