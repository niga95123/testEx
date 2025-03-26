<?php 

namespace App\Service\Handler\Guest\Delete;

use App\Entity\Guest\TechGuest;

final class DeleteTechGuestMessage
{
    public function __construct(private readonly TechGuest $guest) {
    }

    public function getGuest(): TechGuest
    {
        return $this->guest;
    }
}