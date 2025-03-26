<?php 

namespace App\Service\Handler\Guest\Create;

use App\Dto\Command\CreateTechGuestCommand;

final class CreateTechGuestMessage
{
    public function __construct(private readonly CreateTechGuestCommand $command) {
    }

    public function getCommand(): CreateTechGuestCommand
    {
        return $this->command;
    }
}