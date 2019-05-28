<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUsersController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminUsersController constructor.
     * @param UserRepository $userRepository
     * @param ObjectManager $em
     */
    public function __construct(UserRepository $userRepository, ObjectManager $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    /**
     * Admin user index page.
     * @Route("admin/users", name="admin_users")
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $users = $paginator->paginate($this->userRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            10);

        return $this->render('admin/users/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("admin/users/add", name="admin_users_add")
     *
     * @return Response
     */
    public function add(): Response
    {

    }

    /**
     * @Route("admin/users/edit/{id}", name="admin_users_edit", requirements={"id": "\d+"})
     *
     * @param User $user
     * @return Response
     */
    public function edit(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', "Les données de l'utilisateur ont bien été modifiées");

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("admin/users/remove/{id}", name="admin_users_remove", requirements={"id": "\d+"})
     *
     * @param User $user
     * @return Response
     */
    public function remove(User $user): Response
    {

    }
}