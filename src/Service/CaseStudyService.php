<?php

    namespace App\Service;

    use App\Entity\CaseStudy;
    use App\Repository\CaseStudyRepository;
    use Symfony\Component\HttpFoundation\Request;

    class CaseStudyService
    {

        public function newCaseStudy(Request $request, CaseStudyRepository $caseStudyRepository): CaseStudy
        {
            $formData = json_decode($request->getContent());

            $newCase = new CaseStudy();
            $newCase->setTitle($formData->title)
                ->setLink($formData->link)
                ->setHtmlId('youtube-' . str_ireplace(' ', '-', strtolower($formData->title)));
            $caseStudyRepository->save($newCase, true);

            return $newCase;
        }

        public function oneCaseReturn(CaseStudy $caseStudy): array
        {
            return ['id' => $caseStudy->getId(),
                'title' => $caseStudy->getTitle(),
                'link' => $caseStudy->getLink(),
                'htmlId' => $caseStudy->getHtmlId()];
        }

        public function manyCaseReturn(CaseStudy $cases): array
        {
            $data = [];
            foreach ($cases as $case) {
                $data[] = [
                    'id' => $case->getId(),
                    'title' => $case->getTitle(),
                    'link' => $case->getLink(),
                    'htmlId' => $case->getHtmlId(),
                ];
            }

            return $data;
        }

    }