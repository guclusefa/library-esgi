<?php

namespace App\Controller\Frontend;

use App\Constants\RouteConstants;
use App\Constants\ToastConstants;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoryController extends AbstractController
{
    public function __construct
    (
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $em
    )
    {
    }

    private function checkCategory(?Category $category): void
    {
        if (!$category instanceof Category) {
            $this->addFlash(ToastConstants::TOAST_ERROR, 'La catégorie n\'existe pas');
            throw $this->createNotFoundException('La catégorie n\'existe pas');
        }
    }

    #[Route('', name: RouteConstants::ROUTE_CATEGORIES, methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('frontend/category/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    #[Route('/create', name: RouteConstants::ROUTE_CATEGORIES_CREATE, methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash(ToastConstants::TOAST_SUCCESS, 'La catégorie a bien été créée');

            return $this->redirectToRoute(RouteConstants::ROUTE_CATEGORIES);
        }

        return $this->render('frontend/category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: RouteConstants::ROUTE_CATEGORIES_SHOW, methods: ['GET'])]
    public function show(?Category $category): Response
    {
        $this->checkCategory($category);
        return $this->render('frontend/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: RouteConstants::ROUTE_CATEGORIES_EDIT, methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Category $category): Response|RedirectResponse
    {
        $this->checkCategory($category);

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->flush();
                $this->addFlash(ToastConstants::TOAST_SUCCESS, 'La catégorie a bien été modifiée');

                return $this->redirectToRoute(RouteConstants::ROUTE_CATEGORIES);
            }
            $this->addFlash(ToastConstants::TOAST_ERROR, 'La catégorie n\'a pas pu être modifiée');
        }

        return $this->render('frontend/category/edit.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    }

    #[Route('/{id}', name: RouteConstants::ROUTE_CATEGORIES_DELETE, methods: ['POST'])]
    public function delete(Request $request, ?Category $category): Response|RedirectResponse
    {
        $this->checkCategory($category);

        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash(ToastConstants::TOAST_SUCCESS, 'La catégorie a bien été supprimée');
        } else {
            $this->addFlash(ToastConstants::TOAST_ERROR, 'La catégorie n\'a pas pu être supprimée');
        }

        return $this->redirectToRoute(RouteConstants::ROUTE_CATEGORIES);
    }
}
