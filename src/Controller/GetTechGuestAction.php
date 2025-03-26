<?php

namespace App\Controller;

use App\Dto\TechGuestDetailResponse;
use App\Entity\Guest\TechGuest;
use App\Service\Http\StreamedJsonResponseBuilder;
use Nelmio\ApiDocBundle\Attribute as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/guest/{id}', name: 'getTechGuest', methods: [Request::METHOD_GET])]
class GetTechGuestAction extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface         $messageBus,
        private readonly StreamedJsonResponseBuilder $responder
    )
    {
    }

    #[OA\Get(
        path: '/api/guest/{id}',
        operationId: 'getTechGuest',
        description: 'Получение гостя.',
        summary: 'Получение гостя',
        tags: ['Guest'],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Запрашиваемый гость.',
                content: new Nelmio\Model(
                    type: TechGuestDetailResponse::class,
                )
            ),
            new OA\Response(
                response: Response::HTTP_BAD_REQUEST,
                description: 'Некорректный запрос'
            ),
            new OA\Response(
                response: Response::HTTP_UNAUTHORIZED,
                description: 'Не авторизован'
            ),
            new OA\Response(
                response: Response::HTTP_UNPROCESSABLE_ENTITY,
                description: 'Ошибка валидации данных'
            ),
            new OA\Response(
                response: Response::HTTP_INTERNAL_SERVER_ERROR,
                description: 'Внутренняя ошибка сервера'
            )
        ]
    )]
    public function __invoke(TechGuest $guest): Response
    {
        $startTime = microtime(true);
       return $this->responder->ok(TechGuestDetailResponse::fromEntity($guest));
    }

}
