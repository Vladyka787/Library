<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/book")
 */
class BookController extends AbstractController
{
    /**
     * @Route("/", name="app_book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_book_new", methods={"GET", "POST"})
     */
    public function new(Request $request, BookRepository $bookRepository, SluggerInterface $slugger): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            /** @var UploadedFile $bookFile */
//            Получем файл пдф из формы
            $bookFile = $form->get('BookFile')->getData();

            if ($bookFile) {
//                Проверили что файл загрузили и после придумываем уникальное имя
                $originalFilenameBook = pathinfo($bookFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilenameBook = $slugger->slug($originalFilenameBook);
                $newFilenameBook = $safeFilenameBook . '-' . uniqid('', true) . '.' . $bookFile->guessExtension();
//              Сохраняем на сервере
                try {
                    $bookFile->move(
                        $this->getParameter('BookFile_directory'),
                        $newFilenameBook
                    );
                } catch (FileException $e) {
//
                }

                $book->setBookFile($newFilenameBook);
            }

            $bookCover = $form->get('BookCover')->getData();
//          Получаем файл обложки из формы
            if ($bookCover) {
                $originalFilenameCover = pathinfo($bookCover->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilenameCover = $slugger->slug($originalFilenameCover);
                $newFilenameCover = 'cover/'. $safeFilenameCover . '-' . uniqid('', true) . '.' . $bookCover->guessExtension();
//              Проверили наличие файла, назвали его и поместили на сервер
                try {
                    $bookCover->move(
                        $this->getParameter('BookCover_directory'),
                        $newFilenameCover
                    );
                } catch (FileException $e) {
//
                }

                $book->setBookCover($newFilenameCover);
            }
//          Обновили время чтения
            $book->updateBookDateRead();

            $bookRepository->add($book, true);

            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_book_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Book $book, BookRepository $bookRepository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        $oldBookFile = $book->getBookFile();
        $oldBookCover = $book->getBookCover();

        if ($form->isSubmitted() && $form->isValid()) {
//           Получили файл из формы.Назвали и загрузили на сервер.
            $bookFile = $form->get('BookFile')->getData();

            if ($bookFile) {
                $originalFilenameBook = pathinfo($bookFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilenameBook = $slugger->slug($originalFilenameBook);
                $newFilenameBook = $safeFilenameBook . '-' . uniqid('', true) . '.' . $bookFile->guessExtension();

                try {
                    $bookFile->move(
                        $this->getParameter('BookFile_directory'),
                        $newFilenameBook
                    );
                } catch (FileException $e) {
//
                }

                $book->setBookFile($newFilenameBook);
            }
//           Получили файл из формы.Назвали и загрузили на сервер.
            $bookCover = $form->get('BookCover')->getData();

            if ($bookCover) {
                $originalFilenameCover = pathinfo($bookCover->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilenameCover = $slugger->slug($originalFilenameCover);
                $newFilenameCover = 'cover/'. $safeFilenameCover . '-' . uniqid('', true) . '.' . $bookCover->guessExtension();

                try {
                    $bookCover->move(
                        $this->getParameter('BookCover_directory'),
                        $newFilenameCover
                    );
                } catch (FileException $e) {
//
                }

                $book->setBookCover($newFilenameCover);
            }
//          ОБновили время прочтения
            $book->updateBookDateRead();

            $bookRepository->add($book, true);

//          Если файлы были загружены в форму, то удаляем с сервера старые файлы
            if ($bookFile) {
                unlink('../assets/book/file/' . $oldBookFile);
            }
            if ($bookCover) {
                unlink('../public/' . $oldBookCover);
            }

            return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_book_delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book, BookRepository $bookRepository): Response
    {
//      Получаем старые пути к файлам
        $oldBookFile = $book->getBookFile();
        $oldBookCover = $book->getBookCover();

        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
//            Удаляем файлы с сервера
            unlink('../assets/book/file/' . $oldBookFile);
            unlink('../public/' . $oldBookCover);
            $bookRepository->remove($book, true);
        }

        return $this->redirectToRoute('app_main', [], Response::HTTP_SEE_OTHER);
    }
}
