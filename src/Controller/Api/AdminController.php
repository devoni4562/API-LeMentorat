<?php

    namespace App\Controller\Api;

    use App\Entity\Category;
    use App\Entity\Member;
    use App\Entity\Role;
    use App\Repository\ArticleRepository;
    use App\Repository\CategorieRepository;
    use App\Repository\MemberRepository;
    use App\Repository\ParagraphRepository;
    use App\Repository\RoleRepository;
    use App\Service\ArticleService;
    use App\Service\CategoryService;
    use App\Service\FileUploader;
    use App\Service\MemberService;
    use App\Service\RolesService;
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
        public function getAllAdmin(MemberRepository $memberRepository, MemberService $membersService): JsonResponse
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
        public function createNewMember(FileUploader $fileUploader, RoleRepository $roleRepository, MemberRepository $memberRepository, Request $request, UserPasswordHasherInterface $passwordHasher, MemberService $membersService): JsonResponse
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
        public function newArticle(Request $request, ArticleService $articleService, CategorieRepository $categorieRepository, MemberRepository $memberRepository, FileUploader $fileUploader, ArticleRepository $articleRepository, ParagraphRepository $paragraphRepository): JsonResponse
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
