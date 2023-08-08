<?php

namespace App\Controller\Api;

use App\Entity\CaseStudy;
use App\Form\CaseStudyType;
use App\Repository\CaseStudyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/case_study')]
class CaseStudyController extends AbstractController
{
    #[Route('/', name: 'app_case_study_index', methods: ['GET'])]
    public function getAll(CaseStudyRepository $caseStudyRepository): JsonResponse
    {
        $cases = $caseStudyRepository->findAll();

        $data = [];
        foreach ($cases as $case) {
            $data[] = [
                'id' => $case->getId(),
                'title' => $case->getTitle(),
                'link' => $case->getLink(),
                'htmlId' => $case->getHtmlId(),
            ];
        }


        return new JsonResponse($data);

    }


}
