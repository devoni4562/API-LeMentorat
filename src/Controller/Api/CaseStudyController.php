<?php

    namespace App\Controller\Api;

    use App\Repository\CaseStudyRepository;
    use App\Service\CaseStudyService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/api/case_study')]
    class CaseStudyController extends AbstractController
    {
        #[Route('/', name: 'app_case_study_index', methods: ['GET'])]
        public function getAll(CaseStudyRepository $caseStudyRepository, CaseStudyService $caseStudyService): JsonResponse
        {
            $cases = $caseStudyRepository->findAll();

            return new JsonResponse($caseStudyService->manyCaseReturn($cases));

        }

        #[Route('/new', methods: ['POST'])]
        public function createCase(CaseStudyRepository $caseStudyRepository, CaseStudyService $caseStudyService, Request $request): JsonResponse
        {
            $newCase = $caseStudyService->newCaseStudy($request, $caseStudyRepository);
            return new JsonResponse($caseStudyService->oneCaseReturn($newCase));
        }


    }
