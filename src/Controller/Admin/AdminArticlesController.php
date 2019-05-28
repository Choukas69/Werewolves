<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminArticlesController
 * @package App\Controller\Admin
 */
class AdminArticlesController extends AbstractController
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * AdminArticlesController constructor.
     * @param ArticleRepository $articleRepository
     * @param ObjectManager $em
     */
    public function __construct(ArticleRepository $articleRepository, ObjectManager $em)
    {
        $this->articleRepository = $articleRepository;
        $this->em = $em;
    }

    /**
     * Admin article index page.
     * @Route("admin/articles", name="admin_articles")
     *
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $articles = $paginator->paginate($this->articleRepository->findAllQuery(),
            $request->query->getInt('page', 1),
            10);

        return $this->render('admin/articles/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * Admin article edit page.
     * @Route("admin/articles/edit/{id}", name="admin_articles_edit", requirements={"id": "\d+"})
     *
     * @param Article $article
     * @param Request $request
     * @return Response
     */
    public function edit(Article $article, Request $request): Response
    {
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            $this->addFlash('success', "L'article a bien été modifié");

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/articles/edit.html.twig', [
            'form'    => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * Admin article creation page.
     * @Route("admin/articles/create", name="admin_articles_create")
     *
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function create(Request $request): Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser())
                ->setCreatedAt(new DateTime());

            $this->em->persist($article);
            $this->em->flush();

            $this->addFlash('success', "L'article a bien été crée");

            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/articles/create.html.twig', [
            'form'    => $form->createView(),
            'article' => $article
        ]);
    }

    /**
     * Admin article removal page.
     * @Route("admin/articles/remove/{id}", name="admin_articles_remove", requirements={"id": "\d+"})
     *
     * @param Article $article
     * @return Response
     */
    public function remove(Article $article): Response
    {
        $this->em->remove($article);
        $this->em->flush();

        $this->addFlash('success', "L'article a bien été supprimé");

        return $this->redirectToRoute('admin_articles');
    }
}