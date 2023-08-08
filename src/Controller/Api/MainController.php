<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/getConferenceSubscribeLink', methods: ['GET'])]
    public function getConferenceLink(): JsonResponse
    {
        $filePath = $this->getParameter('kernel.project_dir').'/public/res/txt/conference_subscribe_link.txt';

        if (file_exists($filePath)){
            $link = file_get_contents($filePath);

            return new JsonResponse($link);
        }

        return new JsonResponse(null);
    }

    #[Route('/getCoffeeSubscribeInfo', methods: ['GET'])]
    public function getCoffeeInfo(): JsonResponse
    {
        $filePath = $this->getParameter('kernel.project_dir').'/public/res/txt/coffee_subscribe_info.txt';

        if (file_exists($filePath)){
            $lignes = file($filePath);

            $data = [];
            foreach ($lignes as $ligne){
                $parts = explode(':', $ligne, 2);
                if (count($parts) === 2){
                    $key = strtolower(trim($parts[0]));
                    $value = trim(str_replace(["\r", "\n"], '', $parts[1]));
                    $data[$key]=$value;
                }
            }

            return new JsonResponse($data);
        }

        return new JsonResponse(null);
    }
}
