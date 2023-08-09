<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/new', methods: ['GET'])]
public function createNewCategory(CategorieRepository $categoryRepository, Request $request): JsonResponse
    {
        $newCategory = new Category();
        $newCategory->setLibelle($request->get('libelle'));

        $categoryRepository->save($newCategory, true);

        return new JsonResponse(['category'=>['id'=>$newCategory->getId(), 'libelle'=>$newCategory->getLibelle()]]);

    }
}
