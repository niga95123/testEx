<?php

namespace App\Service\Handler\Guest\Update;

use App\Service\Handler\GuestService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateTechGuestHandler
{
    public function __construct(private readonly GuestService $guestService) {
    }

    public function __invoke(UpdateTechGuestMessage $message): mixed
    {
        return $this->guestService->updateGuestFromMessage($message);
    }
}
