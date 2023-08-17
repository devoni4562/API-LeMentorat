<?php

    namespace App\Service;

    use App\Entity\Article;
    use App\Entity\Paragraph;
    use App\Repository\ArticleRepository;
    use App\Repository\CategorieRepository;
    use App\Repository\MemberRepository;
    use App\Repository\ParagraphRepository;
    use DateTime;
    use Symfony\Component\HttpFoundation\Request;

    class ArticleService
    {

        public function shortenedArticles(array $articles)
        {
            $data = [];
            foreach ($articles as $article) {
                $data[] = [
                    'id' => $article->getId(),
                    'date' => $article->getDate(),
                    'image' => $article->getImage(),
                    'title' => $article->getTitle(),
                    'summary' => $article->getSummary(),
                    'category' => $article->getCategory()->getLibelle(),
                    'writerPseudo' => $article->getWritter()->getPseudo(),
                    'writerAvatar' => $article->getWritter()->getAvatar(),
                    'writerEmail' => $article->getWritter()->getEmail(),
                    'writerJob' => $article->getWritter()->getJob()->getName()
                ];
            }
            return $data;
        }

        public function idAndTitleOfArticles(array $articles)
        {
            $data = [];
            foreach ($articles as $article) {
                $data[] = [
                    'id' => $article->getId(),
                    'title' => $article->getTitle()
                ];
            }
            return $data;
        }

        public function newArticle(CategorieRepository $categorieRepository, ArticleRepository $articleRepository, MemberRepository $memberRepository, Request $request, string $directory, FileService $fileService, ParagraphRepository $paragraphRepository)
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
                $newArticle->setImage($fileService->upload($request->files->get('image'), $directory . str_ireplace(' ', '_', $newArticle->getTitle()) . '/', $formData['title']));
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
                        $newParagraph->setPicture($fileService->upload($request->files->get('imageParagraph' . $index), $directory . str_ireplace(' ', '_', $newArticle->getTitle()) . '/paragraphs/', 'paragraph' . $index));
                    }

                    $paragraphRepository->save($newParagraph, true);
                    $newArticle->addParagraph($newParagraph);
                }
            }
            return $newArticle;
        }

        public function deleteArticle(Request $request, int $id, ArticleRepository $articleRepository, ParagraphRepository $paragraphRepository, string $directory, FileService $fileService)
        {
            $articleToDelete = $articleRepository->find($id);

            $fileService->deleteDirectory($directory . str_ireplace(' ', '_', $articleToDelete->getTitle()));

            foreach ($articleToDelete->getParagraphs() as $paragraph) {
                $paragraphRepository->remove($paragraph, true);
            }

            $articleRepository->remove($articleToDelete, true);
        }

    }