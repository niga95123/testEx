<?php

namespace App\Repository;

use App\Entity\TechCountryPhoneNumber;
use App\Repository\Base\BaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class TechCountryPhoneNumberRepository extends BaseRepository
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, TechCountryPhoneNumber::class);
    }
}