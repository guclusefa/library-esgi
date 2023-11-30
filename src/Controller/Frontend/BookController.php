<?php

namespace App\Controller\Frontend;

use App\Constants\RouteConstants;
use App\Constants\ToastConstants;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/books')]
class BookController extends AbstractController
{
    public function __construct
    (
        private readonly BookRepository $bookRepository,
        private readonly EntityManagerInterface $em
    )
    {
    }

    private function checkBook(?Book $book): void
    {
        if (!$book instanceof Book) {
            $this->addFlash(ToastConstants::TOAST_ERROR, 'Le livre n\'existe pas');
            throw $this->createNotFoundException('Le livre n\'existe pas');
        }
    }

    #[Route('', name: RouteConstants::ROUTE_BOOKS, methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('frontend/book/index.html.twig', [
            'books' => $this->bookRepository->findAll(),
        ]);
    }

    #[Route('/create', name: RouteConstants::ROUTE_BOOKS_CREATE, methods: ['GET', 'POST'])]
    public function create(Request $request): Response|RedirectResponse
    {
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($book);
            $this->em->flush();
            $this->addFlash(ToastConstants::TOAST_SUCCESS, 'Le livre a bien été créé');

            return $this->redirectToRoute(RouteConstants::ROUTE_BOOKS);
        }

        return $this->render('frontend/book/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}', name: RouteConstants::ROUTE_BOOKS_SHOW, methods: ['GET'])]
    public function show(?Book $book): Response
    {
        $this->checkBook($book);
        return $this->render('frontend/book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: RouteConstants::ROUTE_BOOKS_EDIT, methods: ['GET', 'POST'])]
    public function edit(Request $request, ?Book $book): Response|RedirectResponse
    {
        $this->checkBook($book);

        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->flush();
                $this->addFlash(ToastConstants::TOAST_SUCCESS, 'Le livre a bien été modifié');

                return $this->redirectToRoute(RouteConstants::ROUTE_BOOKS);
            }
            $this->addFlash(ToastConstants::TOAST_ERROR, 'Le livre n\'a pas pu être modifié');
        }

        return $this->render('frontend/book/edit.html.twig', [
            'form' => $form,
            'book' => $book
        ]);
    }

    #[Route('/{id}', name: RouteConstants::ROUTE_BOOKS_DELETE, methods: ['POST'])]
    public function delete(Request $request, ?Book $book): Response|RedirectResponse
    {
        $this->checkBook($book);

        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $this->em->remove($book);
            $this->em->flush();
            $this->addFlash(ToastConstants::TOAST_SUCCESS, 'Le livre a bien été supprimé');
        } else {
            $this->addFlash(ToastConstants::TOAST_ERROR, 'Le livre n\'a pas pu être supprimé');
        }

        return $this->redirectToRoute(RouteConstants::ROUTE_BOOKS);
    }
}
