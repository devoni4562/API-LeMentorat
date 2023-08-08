<?php

    namespace App\Controller\Api;

    use App\Entity\Article;
    use App\Entity\Paragraph;
    use App\Repository\ArticleRepository;
    use App\Repository\CategorieRepository;
    use App\Repository\MemberRepository;
    use App\Repository\ParagraphRepository;
    use App\Service\FileUploader;
    use DateTime;
    use Exception;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;

    #[Route('/api/article')]
    class ArticleController extends AbstractController
    {
        #[Route('/', methods: ['GET'])]
        public function getArticles(ArticleRepository $articleRepository): JsonResponse
        {
            $articles = $articleRepository->findAll();

            $response = [];
            foreach ($articles as $article) {
                $response[] = [
                    'id' => $article->getId(),
                    'writer' => [
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
                    'date' => $article->getDate()->format('Y-m-d'),
                    'summary' => $article->getSummary(),
                    'category' => [
                        'id' => $article->getCategory()->getId(),
                        'wording' => $article->getCategory()->getLibelle()
                    ],
                    'title' => $article->getTitle(),
                ];
            }

            return new JsonResponse($response);

        }

        /**
         * @throws Exception
         */
        #[Route('/new', methods: ['POST'])]
        public function newArticle(Request $request, FileUploader $fileUploader, ArticleRepository $articleRepository, CategorieRepository $categorieRepository, MemberRepository $memberRepository, ParagraphRepository $paragraphRepository): JsonResponse
        {
            $formData = $request->request->all();

            $newArticle = new Article();
            $newArticle->setCategory($categorieRepository->find($formData['category']))
                ->setDate(new DateTime())
                ->setWritter($memberRepository->find($formData['writterId']))
                ->setSummary($formData['summary'])
                ->setTitle($formData['title'])
                ->setVideo($formData['video']);

            if ($request->files->get('image') !== null) {
                $newArticle->setImage($fileUploader->upload($request->files->get('image'), $this->getParameter('article_img_dir') . str_ireplace(' ', '_', $newArticle->getTitle()) . '/', $formData['title']));
            }

            $articleRepository->save($newArticle, true);

            if (isset($formData['paragraphs'])) {

                $paragraphs = json_decode($formData['paragraphs'], true);


                foreach ($paragraphs as $index => $paragraph) {
                    $newParagraph = new Paragraph();

                    $newParagraph->setArticle($newArticle)
                        ->setTitle($paragraph['paragraphTitle'])
                        ->setText($paragraph['paragraphText'])
                        ->setLink($paragraph['paragraphLink'])
                        ->setLinkText($paragraph['paragraphLinkText']);

                    if ($request->files->get('imageParagraph' . $index) !== null) {
                        $newParagraph->setPicture($fileUploader->upload($request->files->get('imageParagraph' . $index), $this->getParameter('article_img_dir') . str_ireplace(' ', '_', $newArticle->getTitle()) . '/paragraphs/', 'paragraph' . $index));
                    }

                    $paragraphRepository->save($newParagraph, true);
                    $newArticle->addParagraph($newParagraph);
                }
            }

            $articleParagraphs = [];
            foreach ($newArticle->getParagraphs() as $paragraph) {
                $articleParagraphs[] = [
                    'id' => $paragraph->getId(),
                    'text' => $paragraph->getText(),
                    'image' => $paragraph->getPicture(),
                    'title' => $paragraph->getTitle(),
                    'link' => $paragraph->getLink(),
                    'linkText' => $paragraph->getLinkText(),
                ];
            }

            $response = [
                'id' => $newArticle->getId(),
                'writter' => [
                    'id' => $newArticle->getWritter()->getId(),
                    'lastname' => $newArticle->getWritter()->getLastName(),
                    'description' => $newArticle->getWritter()->getDescription(),
                    'avatar' => $newArticle->getWritter()->getAvatar(),
                    'pseudo' => $newArticle->getWritter()->getPseudo(),
                    'firstname' => $newArticle->getWritter()->getFirstName(),
                    'job' => [
                        'id' => $newArticle->getWritter()->getJob()->getId(),
                        'name' => $newArticle->getWritter()->getJob()->getName()
                    ],
                    'role' => $newArticle->getWritter()->getRoles(),
                    'email' => $newArticle->getWritter()->getEmail(),
                ],
                'video' => $newArticle->getVideo(),
                'image' => $newArticle->getImage(),
                'date' => $newArticle->getDate(),
                'summary' => $newArticle->getSummary(),
                'category' => [
                    'id' => $newArticle->getCategory()->getId(),
                    'wording' => $newArticle->getCategory()->getLibelle()
                ],
                'title' => $newArticle->getTitle(),
                'paragraphs' => $articleParagraphs,

            ];


            return new  JsonResponse($response);
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

    }
