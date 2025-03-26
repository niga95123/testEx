<?php

namespace App\Controller;

use App\Dto\Command\CreateTechGuestCommand;
use App\Dto\TechGuestDetailResponse;
use App\Service\Handler\Guest\Create\CreateTechGuestMessage;
use App\Service\Http\StreamedJsonResponseBuilder;
use Nelmio\ApiDocBundle\Attribute as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/guest', name: 'createTechGuest', methods: [Request::METHOD_PUT])]
class CreateTechGuestAction extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface         $messageBus,
        private readonly StreamedJsonResponseBuilder $responder
    )
    {
    }

    #[OA\Put(
        path: '/api/guest',
        operationId: 'createTechGuest',
        description: 'Создание гостя.',
        summary: 'Создать гостя',
        requestBody: new OA\RequestBody(
            content: new Nelmio\Model(type: CreateTechGuestCommand::class)
        ),
        tags: ['Guest'],
        responses: [
            new OA\Response(
                response: Response::HTTP_CREATED,
                description: 'Созданный гость.',
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
    public function __invoke(#[MapRequestPayload(acceptFormat: 'json', validationFailedStatusCode: 422)] CreateTechGuestCommand $command): Response
    {
        $source = $this->messageBus->dispatch(new CreateTechGuestMessage($command))
            ->last(HandledStamp::class)
            ->getResult();

       return $this->responder->ok($source);
    }

}
