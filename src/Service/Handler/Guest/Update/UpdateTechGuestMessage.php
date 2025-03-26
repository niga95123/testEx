<?php 

namespace App\Service\Handler\Guest\Update;

use App\Dto\Command\CreateTechGuestCommand;
use App\Entity\Guest\TechGuest;

final class UpdateTechGuestMessage
{
    public function __construct(
        private readonly CreateTechGuestCommand $command,
        private readonly TechGuest $techGuest
    ) {
    }

    public function getCommand(): CreateTechGuestCommand
    {
        return $this->command;
    }

    public function getTechGuest(): TechGuest
    {
        return $this->techGuest;
    }
}