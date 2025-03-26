<?php

namespace App\Service\Handler;

use App\Repository\Guest\TechGuestRepository;
use App\Service\Handler\Guest\Create\CreateTechGuestMessage;
use App\Service\Handler\Guest\Delete\DeleteTechGuestMessage;
use App\Service\Handler\Guest\Update\UpdateTechGuestMessage;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Сервис для работы с гостями
 */
#[Autoconfigure(lazy: true)]
class GuestService
{
    public function __construct(
        private readonly TechGuestRepository $techGuestRepository
    ) {
    }


    public function createGuestFromMessage(CreateTechGuestMessage $message): mixed
    {
        return $this->techGuestRepository->createGuestFromTechGuestCommand($message->getCommand());
    }

    public function updateGuestFromMessage(UpdateTechGuestMessage $message): mixed
    {
        return $this->techGuestRepository->updateGuestFromTechGuestCommand(
            command: $message->getCommand(),
            techGuest: $message->getTechGuest()
        );
    }

    public function deleteGuestFromMessage(DeleteTechGuestMessage $message): void
    {
        $this->techGuestRepository->deleteGuestFromTechGuestMessage($message->getGuest());
    }
}