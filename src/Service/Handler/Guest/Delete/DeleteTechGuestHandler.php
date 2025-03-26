<?php

namespace App\Service\Handler\Guest\Delete;

use App\Service\Handler\GuestService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteTechGuestHandler
{
    public function __construct(private readonly GuestService $guestService) {
    }

    public function __invoke(DeleteTechGuestMessage $message): void
    {
        $this->guestService->deleteGuestFromMessage($message);
    }
}
