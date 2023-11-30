<?php

namespace App\Controller\Frontend;

use App\Constants\RouteConstants;
use App\Constants\ToastConstants;
use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/authors')]
class AuthorController extends AbstractController
{
    public function __construct
    (
        private readonly AuthorRepository $authorRepository,
        private readonly EntityManagerInterface $em
    )
    {
    }

    private function checkAuthor(?Author $author): void
    {
        if (!$author instanceof Author) {
            $this->addFlash(ToastConstants::TOAST_ERROR, 'L\'auteur n\'existe pas');
            throw $this->createNotFoundException('L\'auteur n\'existe pas');
        }
    }

    #[Route('', name: RouteConstants::ROUTE_AUTHORS, methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('frontend/author/index.html.twig', [
            'authors' => $this->authorRepository->findAll(),
        ]);
    }

    #[Route('/create', name: RouteConstants::ROUTE_AUTHORS_CREATE, methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($author);
            $this->em->flush();
            $this->addFlash(ToastConstants::TOAST_SUCCESS, 'L\'auteur a bien été créé');

            return $this->redirectToRoute(RouteConstants::ROUTE_AUTHORS);
        }

        return $this->render('frontend/author/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: RouteConstants::ROUTE_AUTHORS_SHOW, methods: ['GET'])]
    public function show(?Author $author): Response
    {
        $this->checkAuthor($author);
        $books = $author->getBooks();
        return $this->render('frontend/author/show.html.twig', [
            'author' => $author,
            'books' => $books
        ]);
    }

    #[Route('/{id}/edit', name: RouteConstants::ROUTE_AUTHORS_EDIT, methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Author $author): Response|RedirectResponse
    {
        $this->checkAuthor($author);

        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->flush();
                $this->addFlash(ToastConstants::TOAST_SUCCESS, 'L\'auteur a bien été modifié');

                return $this->redirectToRoute(RouteConstants::ROUTE_AUTHORS);
            }
            $this->addFlash(ToastConstants::TOAST_ERROR, 'L\'auteur n\'a pas pu être modifié');
        }

        return $this->render('frontend/author/edit.html.twig', [
            'form' => $form,
            'author' => $author
        ]);
    }

    #[Route('/{id}', name: RouteConstants::ROUTE_AUTHORS_DELETE, methods: ['POST'])]
    public function delete(Request $request, ?Author $author): Response|RedirectResponse
    {
        $this->checkAuthor($author);

        if ($this->isCsrfTokenValid('delete' . $author->getId(), $request->request->get('_token'))) {
            $this->em->remove($author);
            $this->em->flush();
            $this->addFlash(ToastConstants::TOAST_SUCCESS, 'L\'auteur a bien été supprimé');
        } else {
            $this->addFlash(ToastConstants::TOAST_ERROR, 'L\'auteur n\'a pas pu être supprimé');
        }

        return $this->redirectToRoute(RouteConstants::ROUTE_AUTHORS);
    }
}
