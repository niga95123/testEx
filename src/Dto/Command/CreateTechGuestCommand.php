<?php

namespace App\Dto\Command;

use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Attribute as Nelmio;

class CreateTechGuestCommand
{
    #[OA\Property(
        description: "Имя.",
        type: "string",
        maxLength: 255,
        minLength: 1,
        example: "Случайное имя",
        nullable: false
    )]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $name;

    #[OA\Property(
        description: "Фамилия.",
        type: "string",
        maxLength: 255,
        minLength: 1,
        example: "Случайная фамилия",
        nullable: false
    )]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $surname;

    #[OA\Property(
        description: "Номер телефона.",
        type: "integer",
        example: "79874563145",
        nullable: false
    )]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 15)]
    public int $phone_number;

    #[OA\Property(
        description: "Email.",
        type: "string",
        maxLength: 255,
        minLength: 1,
        example: "sdfg2gsgsdg@gmail.com",
        nullable: false
    )]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
    )]
    public string $email;

    #[OA\Property(
        description: "ID страны.",
        type: "integer",
        example: 1,
        nullable: false
    )]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 15)]
    public int $country_phone_number_id;
}
