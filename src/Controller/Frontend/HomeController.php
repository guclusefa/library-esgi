<?php

namespace App\Controller\Frontend;

use App\Constants\RouteConstants;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct
    (
        private readonly BookRepository $bookRepository,
        private readonly AuthorRepository $authorRepository,
        private readonly CategoryRepository $categoryRepository
    )
    {
    }

    #[Route('', name: RouteConstants::ROUTE_HOME)]
    public function index(): Response
    {
        $books = $this->bookRepository->findBy([], ['id' => 'DESC'], 3);
        $authors = $this->authorRepository->findBy([], ['id' => 'DESC'], 3);
        $categories = $this->categoryRepository->findBy([], ['id' => 'DESC'], 3);
        return $this->render('frontend/home/index.html.twig', [
            'books' => $books,
            'authors' => $authors,
            'categories' => $categories
        ]);
    }
}
