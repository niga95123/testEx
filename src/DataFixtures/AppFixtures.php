<?php

namespace App\DataFixtures;

use App\Entity\TechCountryPhoneNumber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Много добавлять не стал, просто чтобы было удобно тестировать :)
        $countryPhoneEntity = new TechCountryPhoneNumber(
            country_name: "BELARUS",
            phone_num_code: 380
        );

        $manager->persist($countryPhoneEntity);
        $manager->flush();
    }
}
