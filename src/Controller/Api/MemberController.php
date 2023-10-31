<?php

    namespace App\Controller\Api;

    use App\Repository\MemberRepository;
    use App\Service\MemberService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/api/members')]
    class MemberController extends AbstractController
    {

        #[Route("/staff", methods: ['GET'])]
        public function getStaff(MemberRepository $memberRepository, MemberService $membersService): JsonResponse
        {

            $staff = $memberRepository->findAll();
            $data = $membersService->arrayMembers($staff);
            return new JsonResponse($data);
        }

        #[Route("/{id}", methods: ['GET'])]
        public function getMemberById(int $id, MemberRepository $memberRepository, MemberService $memberService): JsonResponse
        {
            $member = $memberRepository->find($id);
            return new JsonResponse($memberService->oneMember($member));
        }

        //----------------------------------------------MENTORS-----------------------------------------------------------//
        #[Route('/mentors', methods: ['GET'])]
        public function getMentors(MemberRepository $memberRepository, MemberService $membersService): JsonResponse
        {

            $mentors = $memberRepository->getByJobId(2);
            $data = $membersService->arrayMembers($mentors);
            return new JsonResponse($data);

        }

        //---------------------------------------------WITNESSES----------------------------------------------------------//

        #[Route('/witnesses', methods: ['GET'])]
        public function getWitnesses(MemberRepository $memberRepository, MemberService $membersService): JsonResponse
        {
            $witnesses = $memberRepository->getByJobId(3);
            $data = $membersService->arrayMembers($witnesses);
            return new JsonResponse($data);
        }

    }
