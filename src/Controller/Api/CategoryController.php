<?php

    namespace App\Controller\Api;

    use App\Repository\CategorieRepository;
    use App\Service\CategoryService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/api/category')]
    class CategoryController extends AbstractController
    {
        #[Route('/', methods: ['GET'])]
        public function getAllCategory(CategorieRepository $categorieRepository, CategoryService $categoryService): JsonResponse
        {
            $categories = $categorieRepository->findAll();
            return new JsonResponse($categoryService->arrayCategories($categories));
        }
    }
