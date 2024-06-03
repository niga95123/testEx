<?php


namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\CartItem;
use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/api')]
class ProductController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $productRepository;
    private $cartItemRepository;
    private $serializer;
    private $paginator;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository,
                                ProductRepository $productRepository, CartItemRepository $cartItemRepository,
                                SerializerInterface $serializer,  PaginatorInterface $paginator)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->serializer = $serializer;
        $this->paginator = $paginator;
    }

    #[Route('/products', name: 'product_index', methods: ['GET'])]
    #[Response(response: 200, description: 'Получаем все товары')]
    #[Response(response: 404, description: 'Неправильный маршрут')]
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $jsonContent = $this->serializer->serialize($products, 'json');

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/product/create', name: 'product_create', methods: ['GET'])]
    #[Response(response: 200, description: 'Создаем товар указывая обязательные параметры name, price, description')]
    #[Response(response: 404, description: 'Неправильный маршрут')]
    public function create(Request $request): JsonResponse
    {
        $data = $_GET;

        if (array_key_exists('name', $data)
            && array_key_exists('price', $data)
            && array_key_exists('description', $data)) {

            $product = new Product();
            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setDescription($data['description']);

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Товар успешно создан!'], JsonResponse::HTTP_CREATED);

        } else {
            return new JsonResponse(['status' => 'Проверьте указанное количество параметров!'], JsonResponse::HTTP_CREATED);
        }

    }

    #[Route('/product/{id}/edit', name: 'product_edit', methods: ['GET'])]
    #[Response(response: 200, description: 'Изменяем данные товара указывая обязательные параметры name, price, description')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует товар по ID')]
    public function edit(Request $request, Product $product): JsonResponse
    {
        $data = $_GET;

        if (array_key_exists('name', $data)
            && array_key_exists('price', $data)
            && array_key_exists('description', $data)) {

            $product->setName($data['name']);
            $product->setPrice($data['price']);
            $product->setDescription($data['description']);

            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Данные товара изменены!']);

        } else {
            return new JsonResponse(['status' => 'Проверьте указанное количество параметров!']);
        }
    }

    #[Route('/product/{id}/delete', name: 'product_delete', methods: ['DELETE'])]
    #[Response(response: 200, description: 'Удаляем товар')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует товар по ID')]
    public function delete(Product $product): JsonResponse
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted!']);
    }

    #[Route('/products/paginate', name: 'product_paginate', methods: ['GET'])]
    #[Response(response: 200, description: 'Получаем все товары с пагинацией, обязательный параметр: page_num. page_num - параметр задающий какую страницу по счету выгрузить')]
    #[Response(response: 404, description: 'Неправильный маршрут')]
    public function productsPaginate(Request $request): JsonResponse
    {
        $queryBuilder = $this->productRepository->createQueryBuilder('p');
        $page_number = (array_key_exists('page_num', $_GET) ? $_GET['page_num'] : 1);
        $pagination = $this->paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', $page_number), /*page number*/
            $request->query->getInt('limit', 3) /*limit per page*/
        );

        $jsonContent = $this->serializer->serialize($pagination->getItems(), 'json');

        return new JsonResponse([
            'items' => json_decode($jsonContent),
            'pagination' => [
                'total' => $pagination->getTotalItemCount(),
                'current_page' => $pagination->getCurrentPageNumber(),
                'items_per_page' => $pagination->getItemNumberPerPage(),
                'total_pages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            ]
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/products/detail/{id}', name: 'product_details', methods: ['GET'])]
    #[Response(response: 200, description: 'Получаем детальную информацию по ID товара')]
    #[Response(response: 404, description: 'Неправильный маршрут')]
    public function getProductDetails(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $jsonContent = $this->serializer->serialize($product, 'json');

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }
}