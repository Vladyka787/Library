<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="app_main")
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route ("/main/pdf", name="app_main_pdf", methods={"GET", "POST"})
     */
    public function pdf(BookRepository $bookRepository): Response
    {
//        Обновляем время чтения и открываем книгу
        $id = $_GET['id'];
        $book = $bookRepository->findBy(['id' => $id]);
        $book = $book[0];
        $filename = $book->getBookFile();
        $book->updateBookDateRead();
        $bookRepository->add($book, true);

        $package = new Package(new EmptyVersionStrategy());
        $path = $package->getUrl('../assets/book/file/' . $filename);
        $response = new BinaryFileResponse($path);

        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            "pdf-name-at-the-time-of-download.pdf"
        );

        return $response;
    }
}
