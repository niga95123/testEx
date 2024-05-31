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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api')]
class UserController extends AbstractController
{
    private $entityManager;
    private $userRepository;
    private $productRepository;
    private $cartItemRepository;
    private $serializer;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, ProductRepository $productRepository, CartItemRepository $cartItemRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->cartItemRepository = $cartItemRepository;
        $this->serializer = $serializer;
    }

    #[Route('/users', name: 'user_index', methods: ['GET'])]
    #[Response(response: 200, description: 'Получаем всех пользователей')]
    #[Response(response: 404, description: 'Неправильный маршрут')]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $jsonContent = $this->serializer->serialize($users, 'json');

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/user/{id}', name: 'user_show', methods: ['GET'])]
    #[Response(response: 200, description: 'Получаем информацию о пользователе по ID')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует пользователь по ID')]
    public function show(User $user): JsonResponse
    {
        $jsonContent = $this->serializer->serialize($user, 'json');

        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/user/create', name: 'user_create', methods: ['GET'])]
    #[Response(response: 200, description: 'Создаем пользователя указывая обязательные параметры username, email, password')]
    #[Response(response: 404, description: 'Неправильный маршрут')]
    public function create(Request $request): JsonResponse
    {
        $data = $_GET;

        if (array_key_exists('username', $data)
            && array_key_exists('email', $data)
            && array_key_exists('password', $data)) {

            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Пользователь успешно создан!'], JsonResponse::HTTP_CREATED);

        } else {
            return new JsonResponse(['status' => 'Проверьте указанное количество параметров!'], JsonResponse::HTTP_CREATED);
        }

    }

    #[Route('/user/{id}/edit', name: 'user_edit', methods: ['GET'])]
    #[Response(response: 200, description: 'Изменяем данные пользователя указывая обязательные параметры username, email, password')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует пользователь по ID')]
    public function edit(Request $request, User $user): JsonResponse
    {
        $data = $_GET;

        if (array_key_exists('username', $data)
            && array_key_exists('email', $data)
            && array_key_exists('password', $data)) {

            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($data['password']);

            $this->entityManager->flush();

            return new JsonResponse(['status' => 'Данные пользователя изменены!']);

        } else {
            return new JsonResponse(['status' => 'Проверьте указанное количество параметров!']);
        }
    }

    #[Route('/user/{id}/delete', name: 'user_delete', methods: ['DELETE'])]
    #[Response(response: 200, description: 'Удаляем пользователя')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует пользователь по ID')]
    public function delete(User $user): JsonResponse
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'User deleted!']);
    }

    #[Route('/user/{id}/cart', name: 'user_cart', methods: ['GET'])]
    #[Response(response: 200, description: 'Получаем информацию о корзине пользователя')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует пользователь по ID')]
    public function getCart(User $user): JsonResponse
    {
        $cartItems = $this->cartItemRepository->findBy(['user' => $user]);

        $response = [];
        foreach ($cartItems as $cartItem) {
            $response[] = [
                'product_id' => $cartItem->getProduct()->getId(),
                'product_name' => $cartItem->getProduct()->getName(),
                'quantity' => $cartItem->getQuantity(),
                'price' => $cartItem->getProduct()->getPrice(),
            ];
        }

        return new JsonResponse($response);
    }

    #[Route('/user/{id}/cart/add', name: 'user_cart_add', methods: ['GET'])]
    #[Response(response: 200, description: 'Добавляем товар в корзину пользователя, обязательные параметры quantity, product_id')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует пользователь по ID')]
    public function addToCart(Request $request, User $user): JsonResponse
    {
        $data = $_GET;
        if (!array_key_exists('quantity', $data)) {
            return new JsonResponse(['status' => 'Укажите количество товара!'], JsonResponse::HTTP_NOT_FOUND);
        }
        if (!array_key_exists('product_id', $data)) {
            return new JsonResponse(['status' => 'Укажите ID товара!'], JsonResponse::HTTP_NOT_FOUND);
        }
        $product = $this->productRepository->find($data['product_id']);


        if (!$product) {
            return new JsonResponse(['status' => 'Товар не найден!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cartItem = $this->cartItemRepository->findOneBy(['user' => $user, 'product' => $product]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $data['quantity']);
        } else {
            $cartItem = new CartItem();
            $cartItem->setUser($user);
            $cartItem->setProduct($product);
            $cartItem->setQuantity($data['quantity']);
            $this->entityManager->persist($cartItem);
        }

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Товар добавлен в корзину!']);
    }

    #[Route('/user/{id}/cart/update', name: 'user_cart_update', methods: ['GET'])]
    #[Response(response: 200, description: 'Изменяем количества товара в корзине пользователя, обязательные параметры quantity, product_id')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует пользователь по ID')]
    public function updateCart(Request $request, User $user): JsonResponse
    {
        $data = $_GET;
        $product = $this->productRepository->find($data['product_id']);

        if (!$product) {
            return new JsonResponse(['status' => 'Product not found!'], JsonResponse::HTTP_NOT_FOUND);
        } elseif (array_key_exists('quantity', $data)) {
            return new JsonResponse(['status' => 'Укажите количество товара!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cartItem = $this->cartItemRepository->findOneBy(['user' => $user, 'product' => $product]);

        if (!$cartItem) {
            return new JsonResponse(['status' => 'Товар не найден в корзине!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cartItem->setQuantity($data['quantity']);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Количество товара изменено!']);
    }

    #[Route('/user/{id}/cart/remove', name: 'user_cart_remove', methods: ['GET'])]
    #[Response(response: 200, description: 'Удаляем товар из корзины пользователя, обязательные параметры product_id')]
    #[Response(response: 404, description: 'Неправильный маршрут или отсутствует пользователь по ID')]
    public function removeFromCart(Request $request, User $user): JsonResponse
    {
        $data = $_GET;
        $product = $this->productRepository->find($data['product_id']);

        if (!$product) {
            return new JsonResponse(['status' => 'Product not found!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cartItem = $this->cartItemRepository->findOneBy(['user' => $user, 'product' => $product]);

        if (!$cartItem) {
            return new JsonResponse(['status' => 'Товар не найден в корзине!'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($cartItem);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Товар удален из корзины!']);
    }

}