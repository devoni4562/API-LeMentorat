<?php

    namespace App\Controller\Api;

    use App\Entity\Article;
    use App\Entity\Category;
    use App\Entity\Member;
    use App\Entity\Paragraph;
    use App\Entity\Role;
    use App\Repository\ArticleRepository;
    use App\Repository\CategorieRepository;
    use App\Repository\MemberRepository;
    use App\Repository\ParagraphRepository;
    use App\Repository\RoleRepository;
    use App\Service\CategoryService;
    use App\Service\FileUploader;
    use App\Service\MembersService;
    use App\Service\RolesService;
    use DateTime;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;

    #[
        Route('/api/admin'),
    ]
    class AdminController extends AbstractController
    {


        #[Route("/get_all_admin"), ]
        public function getAllAdmin(MemberRepository $memberRepository, MembersService $membersService): JsonResponse
        {

            $admins = [];
            foreach ($memberRepository->findAll() as $member) {
                if (in_array('ROLE_ADMIN', $member->getRoles(), true)) {

                    $admins[] = $member;
                }
            }
            $data = $membersService->arrayMembers($admins);

            return new JsonResponse($data);

        }


//--------------------------------- MEMBER -----------------------------------------//

        #[Route('/member/new', methods: ['POST'])]
        public function createNewMember(FileUploader $fileUploader, RoleRepository $roleRepository, MemberRepository $memberRepository, Request $request, UserPasswordHasherInterface $passwordHasher, MembersService $membersService): JsonResponse
        {
            $newMember = new Member();

            if ($request->get('isAdmin') !== null) {
                $plainPassword = $request->get('password');
                $hashedPassword = $passwordHasher->hashPassword($newMember, $plainPassword);
                $newMember
                    ->setRoles(["ROLE_ADMIN"])
                    ->setPassword($hashedPassword);
            }

            $newMember
                ->setFirstName($request->get('firstname'))
                ->setLastName($request->get('lastname'))
                ->setPseudo($request->get('pseudo'))
                ->setEmail($request->get('email'))
                ->setJob($roleRepository->find(intval($request->get('jobId'))))
                ->setDescription($request->get('description'));

            if ($request->files->get('avatar') !== null) {
                $newMember->setAvatar($fileUploader->upload($request->files->get('avatar'), $this->getParameter('avatar_img_dir') . str_ireplace(' ', '_', $newMember->getJob()->getName()) . '/' . $newMember->getEmail() . '/', $newMember->getPseudo()));
            }


            $memberRepository->save($newMember, true);

            $data = $membersService->oneMember($newMember);

            return new JsonResponse($data);
        }


//-----------------------------ROLE-------------------------------------------------//
        #[Route('/role/new', methods: ['POST'])]
        public function createNewRole(RoleRepository $roleRepository, Request $request, RolesService $rolesService): JsonResponse
        {
            $newRole = new Role();
            $newRole->setName($request->get('name'));

            $roleRepository->save($newRole, true);

            return new JsonResponse($rolesService->oneRole($newRole));

        }


//---------------------------Article---------------------------------------------//
        #[Route('/article/new', methods: ['POST'])]
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

//----------------------------- Category --------------------------------//
        #[Route('/category/new', methods: ['POST'])]
        public function createCategory(Request $request, CategoryService $categoryService, CategorieRepository $categorieRepository): JsonResponse
        {
            $newCategory = new Category();
            $formData = json_decode($request->getContent());

            $newCategory->setLibelle($formData->libelle);

            $categorieRepository->save($newCategory, true);

            return new JsonResponse($categoryService->oneCategory($newCategory));
        }

    }
