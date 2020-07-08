<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookController
 * @package App\Controller
 * @Route("/api", name="book_api")
 */
class BookController extends AbstractController
{
    /**
     * @param BookRepository $bookRepository
     * @return JsonResponse
     * @Route("/books", name="books", methods={"GET"})
     */
    public function getBooks(BookRepository $bookRepository)
    {
        $result = $bookRepository->findAll();
        $data = [];
        foreach ($result as $book) {
            $data[] = [
                'id' => $book->getId(),
                'name' => $book->getName(),
                'author' => $book->getAuthor(),
                'isRead' => $book->getIsRead()
            ];
        }

        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param BookRepository $bookRepository
     * @return JsonResponse
     * @Route("/books", name="books_add", methods={"POST"})
     */
    public function addBook(Request $request, EntityManagerInterface $entityManager, BookRepository $bookRepository)
    {
        try {
            $request = $this->transformJsonBody($request);

            if (!$request
                || !$request->get('name')
                || !$request->get('author')
                || $request->get('isRead') == null
            ) {
                throw new \Exception();
            }

            $book = new Book();
            $book->setName($request->get('name'));
            $book->setAuthor($request->get('author'));
            $book->setIsRead(($request->get('isRead') == 0) ? false : true);
            $entityManager->persist($book);
            $entityManager->flush();

            $data = [
                'status' => 200,
                'success' => "Book added successfully",
            ];

            return $this->response($data);
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];

            return $this->response($data, 422);
        }
    }

    /**
     * @param BookRepository $bookRepository
     * @param $id
     * @return JsonResponse
     * @Route("/books/{id}", name="books_get", methods={"GET"})
     */
    public function getBook(BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            $data = [
                'status' => 404,
                'errors' => "Book not found",
            ];

            return $this->response($data, 404);
        }

        return $this->response([
            'id' => $book->getId(),
            'name' => $book->getName(),
            'author' => $book->getAuthor(),
            'isRead' => $book->getIsRead()
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param BookRepository $bookRepository
     * @param $id
     * @return JsonResponse
     * @Route("/books/{id}", name="books_put", methods={"PUT"})
     */
    public function updateBook(Request $request, EntityManagerInterface $entityManager, BookRepository $bookRepository, $id)
    {

        try {
            $book = $bookRepository->find($id);

            if (!$book) {
                $data = [
                    'status' => 404,
                    'errors' => "Book not found",
                ];

                return $this->response($data, 404);
            }

            $request = $this->transformJsonBody($request);

            if (!$request
                || !$request->get('name')
                || !$request->get('author')
                || !$request->get('isRead')
            ) {
                throw new \Exception();
            }

            $book->setName($request->get('name'));
            $book->setAuthor($request->get('author'));
            $book->setIsRead($request->get('isRead'));
            $entityManager->flush();

            $data = [
                'status' => 200,
                'errors' => "Book updated successfully",
            ];

            return $this->response($data);

        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];

            return $this->response($data, 422);
        }

    }

    /**
     * @param EntityManagerInterface $entityManager
     * @param BookRepository $bookRepository
     * @param $id
     * @return JsonResponse
     * @Route("/books/{id}", name="books_delete", methods={"DELETE"})
     */
    public function deleteBook(EntityManagerInterface $entityManager, BookRepository $bookRepository, $id)
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            $data = [
                'status' => 404,
                'errors' => "Book not found",
            ];

            return $this->response($data, 404);
        }

        $entityManager->remove($book);
        $entityManager->flush();
        $data = [
            'status' => 200,
            'errors' => "Book deleted successfully",
        ];

        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param BookRepository $bookRepository
     * @return JsonResponse
     * @Route("/author", name="books_by_author", methods={"POST"})
     */
    public function getBooksByAuthor(
        Request $request,
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository
    ) {
        try {
            $request = $this->transformJsonBody($request);

            if (!$request || !$request->get('author')) {
                throw new \Exception();
            }

            $books = $bookRepository->findBy(['author' => $request->get('author')]);
            $data = [];
            foreach ($books as $book) {
                $data[] = ['id' => $book->getId(), 'name' => $book->getName(), 'isRead' => $book->getIsRead()];
            }

            return $this->response($data);
        } catch (\Exception $e) {
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];

            return $this->response($data, 422);
        }
    }

    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }
}
