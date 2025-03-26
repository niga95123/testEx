<?php

namespace App\Service\Handler\Guest\Create;

use App\Entity\Guest\TechGuest;
use App\Service\Handler\GuestService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTechGuestHandler
{
    public function __construct(private readonly GuestService $guestService) {
    }

    public function __invoke(CreateTechGuestMessage $message): mixed
    {
        return $this->guestService->createGuestFromMessage($message);
    }
}
