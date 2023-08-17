<?php

    namespace App\Controller\Api;

    use App\Repository\ArticleRepository;
    use App\Service\ArticleService;
    use App\Service\CategoryService;
    use App\Service\MemberService;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/api/article')]
    class ArticleController extends AbstractController
    {
        #[Route('/', methods: ['GET'])]
        public function getArticles(ArticleRepository $articleRepository, MemberService $membersService, CategoryService $categoryService): JsonResponse
        {
            $articles = $articleRepository->findAll();

            $response = [];
            foreach ($articles as $article) {
                $writer = $membersService->oneMember($article->getWritter());
                $category = $categoryService->oneCategory($article->getCategory());
                $response[] = [
                    'id' => $article->getId(),
                    'writer' => $writer,
                    'video' => $article->getVideo(),
                    'image' => $article->getImage(),
                    'date' => $article->getDate()->format('Y-m-d'),
                    'summary' => $article->getSummary(),
                    'category' => $category,
                    'title' => $article->getTitle(),
                ];
            }

            return new JsonResponse($response);

        }

        #[Route('/getArticle/{id}', methods: ['GET'])]
        public function getParagraphsByArticle(ArticleRepository $articleRepository, int $id): JsonResponse
        {
            $article = $articleRepository->find($id);

            if (!$article) {
                return new JsonResponse(['error' => 'Article Introuvable'], Response::HTTP_NOT_FOUND);
            }

            $paragraphs = [];
            foreach ($article->getParagraphs() as $paragraph) {
                $paragraphs[] = [
                    'id' => $paragraph->getId(),
                    'text' => $paragraph->getText(),
                    'image' => $paragraph->getPicture(),
                    'title' => $paragraph->getTitle(),
                    'link' => $paragraph->getLink(),
                    'linkText' => $paragraph->getLinkText(),
                ];
            }

            $response = [
                'id' => $article->getId(),
                'writter' => [
                    'id' => $article->getWritter()->getId(),
                    'lastname' => $article->getWritter()->getLastName(),
                    'description' => $article->getWritter()->getDescription(),
                    'avatar' => $article->getWritter()->getAvatar(),
                    'pseudo' => $article->getWritter()->getPseudo(),
                    'firstname' => $article->getWritter()->getFirstName(),
                    'job' => [
                        'id' => $article->getWritter()->getJob()->getId(),
                        'name' => $article->getWritter()->getJob()->getName()
                    ],
                    'role' => $article->getWritter()->getRoles(),
                    'email' => $article->getWritter()->getEmail(),
                ],
                'video' => $article->getVideo(),
                'image' => $article->getImage(),
                'date' => $article->getDate(),
                'summary' => $article->getSummary(),
                'category' => [
                    'id' => $article->getCategory()->getId(),
                    'wording' => $article->getCategory()->getLibelle()
                ],
                'title' => $article->getTitle(),
                'paragraphs' => $paragraphs,
            ];

            return new JsonResponse($response);
        }

        #[Route('/IdAndTitle', methods: ['GET'])]
        public function getIdAndTitleForAllArticle(ArticleRepository $articleRepository, ArticleService $articleService): JsonResponse
        {
            $articles = $articleRepository->findAll();
            return new JsonResponse($articleService->idAndTitleOfArticles($articles));
        }

    }
