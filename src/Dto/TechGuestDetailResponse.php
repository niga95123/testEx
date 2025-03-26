<?php

namespace App\Dto;

use App\Entity\Guest\TechGuest;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Groups;

#[Groups(["guest:put"])]
final class TechGuestDetailResponse
{
    public function __construct(
        #[OA\Property(
            description: "Информация для заполнения графика.",
            example: [
                [
                    "id" => 3,
                    "name" => "niga",
                    "surname" => "niga",
                    "phone_number" => 79874563135,
                    "email" => "sdfg2gsgsdgag@gmail.com",
                    "countryPhoneNumber" => [
                        "id" => 1,
                        "countryName" => "RU",
                        "phoneNumCode" => 7
                    ]
                ]
            ],
            nullable: false
        )]
        public array $data
    ) {
    }

    public static function fromEntity(TechGuest $techGuest): array
    {
        return [
            "id" => $techGuest->getId(),
            "name" => $techGuest->getName(),
            "surname" => $techGuest->getSurname(),
            "phone_number" => $techGuest->getPhoneNumber(),
            "email" => $techGuest->getEmail(),
            "countryPhoneNumber" => [
                "id" => $techGuest->getCountryPhoneNumber()->getId(),
                "countryName" => $techGuest->getCountryPhoneNumber()->getCountryName(),
                "phoneNumCode" => $techGuest->getCountryPhoneNumber()->getPhoneNumCode(),
            ]
        ];
    }
}
