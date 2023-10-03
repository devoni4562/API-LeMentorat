<?php

    namespace App\Controller\Api;

    use App\Entity\Category;
    use App\Entity\Member;
    use App\Repository\ArticleRepository;
    use App\Repository\CaseStudyRepository;
    use App\Repository\CategorieRepository;
    use App\Repository\MemberRepository;
    use App\Repository\ParagraphRepository;
    use App\Repository\RoleRepository;
    use App\Service\ArticleService;
    use App\Service\CaseStudyService;
    use App\Service\CategoryService;
    use App\Service\FileService;
    use App\Service\MemberService;
    use App\Service\RolesService;
    use Psr\Log\LoggerInterface;
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

        private LoggerInterface $logger;

        public function __construct(LoggerInterface $logger)
        {
            $this->logger = $logger;
        }

//--------------------------------- MEMBER -----------------------------------------//

        #[Route('/member/new', methods: ['POST'])]
        public function createNewMember(FileService $fileUploader, RoleRepository $roleRepository, MemberRepository $memberRepository, Request $request, UserPasswordHasherInterface $passwordHasher, MemberService $membersService): JsonResponse
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
                $this->logger->info($request->files->get('avatar'));
                $newMember->setAvatar($fileUploader->upload($request->files->get('avatar'), $this->getParameter('avatar_img_dir') . str_ireplace(' ', '_', $newMember->getJob()->getName()) . '/' . $newMember->getEmail() . '/', $newMember->getPseudo()));
            }


            $memberRepository->save($newMember, true);

            $data = $membersService->oneMember($newMember);

            return new JsonResponse($data);
        }

        #[Route('/member/delete/{id}', methods: ['DELETE'])]
        public function deleteMemberById(int $id, MemberService $memberService, MemberRepository $memberRepository): JsonResponse
        {
            $memberService->deleteMember($id, $memberRepository);

            return new JsonResponse(['message' => 'suppression du membre réussie']);
        }


//-----------------------------ROLE-------------------------------------------------//
        #[Route('/job/new', methods: ['POST'])]
        public function createNewRole(RoleRepository $roleRepository, Request $request, RolesService $rolesService): JsonResponse
        {
            $newRole = $rolesService->newJob($request, $roleRepository);

            return new JsonResponse($rolesService->oneRole($newRole));

        }

        #[Route('/job/update/{id}', methods: ['POST'])]
        public function updateRoleById(int $id, Request $request, RoleRepository $roleRepository, RolesService $rolesService, FileService $fileService): JsonResponse
        {

            $directory = $this->getParameter('avatar_img_dir');
            $roleToUpdate = $rolesService->updateJob($request, $id, $roleRepository, $fileService, $directory);

            return new JsonResponse($rolesService->oneRole($roleToUpdate));
        }

        #[Route('/job/delete/{id}', methods: ['DELETE'])]
        public function deleteRoleById(int $id, RoleRepository $roleRepository, RolesService $rolesService): JsonResponse
        {
            $rolesService->deleteJob($id, $roleRepository);

            return new  JsonResponse(['message' => 'suppression du job réussie']);
        }


//---------------------------Article---------------------------------------------//
        #[Route('/article/new', methods: ['POST'])]
        public function newArticle(Request $request, ArticleService $articleService, CategorieRepository $categorieRepository, MemberRepository $memberRepository, FileService $fileUploader, ArticleRepository $articleRepository, ParagraphRepository $paragraphRepository): JsonResponse
        {
            $result = $articleService->newArticle($categorieRepository, $articleRepository, $memberRepository, $request, $this->getParameter('article_img_dir'), $fileUploader, $paragraphRepository);
            if ($result !== null) {
                $response = [
                    'message' => 'article créé avec succès'
                ];
            } else {
                $response = [
                    'message' => 'Problème lors de la création de l\'article'
                ];
            }
            return new  JsonResponse($response);
        }

        #[Route('/article/delete/{id}', methods: ['DELETE'])]
        public function deleteArticleById(int $id, Request $request, ArticleService $articleService, ArticleRepository $articleRepository, ParagraphRepository $paragraphRepository, FileService $fileService): JsonResponse
        {
            $articleService->deleteArticle($request, $id, $articleRepository, $paragraphRepository, $this->getParameter('article_img_dir'), $fileService);

            return new JsonResponse(['message' => 'suppression réussie']);
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

        #[Route('/category/delete/{id}', methods: ['DELETE'])]
        public function deleteCategoryById(int $id, CategorieRepository $categorieRepository, CategoryService $categoryService): JsonResponse
        {
            $categoryService->deleteCategory($id, $categorieRepository);

            return new JsonResponse(['message' => 'suppression réussie']);
        }

//------------------------------CASE STUDY---------------------------------------//
        #[Route('/case_study/new', methods: ['POST'])]
        public function createCase(CaseStudyRepository $caseStudyRepository, CaseStudyService $caseStudyService, Request $request): JsonResponse
        {
            $newCase = $caseStudyService->newCaseStudy($request, $caseStudyRepository);
            return new JsonResponse($caseStudyService->oneCaseReturn($newCase));
        }

        #[Route('/case_study/delete/{id}', methods: ['DELETE'])]
        public function deleteCaseById(int $id, CaseStudyRepository $caseStudyRepository, CaseStudyService $caseStudyService): JsonResponse
        {
            $caseStudyService->deleteCase($id, $caseStudyRepository);

            return new JsonResponse(['message' => 'suppression réussie']);
        }


//---------------------------LIVE CONFERENCE---------------------------------------//

        #[Route('/live-conference/update', methods: ['POST'])]
        public function updateLiveconferenceLink(Request $request, FileService $fileService)
        {
            $path = $this->getParameter('kernel.project_dir') . '/public/res/txt/conference_subscribe_link.txt';
            $data = json_decode($request->getContent());
            $newUrl = $data->link;
            $fileService->writeTxtFile($path, $newUrl);

            return new JsonResponse(['message' => 'modification réussie']);
        }

//----------------------------BUSINESS COFFEE OWNER ------------------------------//

        #[Route('/business-coffee-owner/update', methods: ['POST'])]
        public function updateBusinessCoffeeInfo(Request $request, FileService $fileService)
        {
            $path = $this->getParameter('kernel.project_dir') . '/public/res/txt/coffee_subscribe_info.txt';
            $data = json_decode($request->getContent());
            $newDate = $data->date;
            $newPlace = $data->place;
            $newLink = $data->link;

            $newInfos = $fileService->coffeeInfoFormatted($newDate, $newPlace, $newLink);
            $fileService->writeTxtFile($path, $newInfos);

            return new JsonResponse(['message' => 'modification réussie']);

        }

    }