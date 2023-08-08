<?php

namespace App\Controller\Api;

use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/category')]
class CategoryController extends AbstractController
{
    #[Route('/', methods: ['GET'])]
    public function getAllCategory(CategorieRepository $categorieRepository): JsonResponse
    {
        $categories = $categorieRepository->findAll();
        $data = [];
        foreach ($categories as $category){
            $data[]=[
                'id' => $category->getId(),
                'wording' => $category->getLibelle()
                ];
        }


        return new JsonResponse($data);
    }
}
