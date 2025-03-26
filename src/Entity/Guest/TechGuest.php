<?php

namespace App\Entity\Guest;

use App\Entity\TechCountryPhoneNumber;
use App\Repository\Guest\TechGuestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "tech_guest", schema: "public")]
#[ORM\Entity(repositoryClass: TechGuestRepository::class)]
class TechGuest
{
    #[ORM\Id]
    #[ORM\GeneratedValue("IDENTITY")]
    #[ORM\Column(
        name: "id",
        type: Types::BIGINT
    )]
    private ?int $id = null;

    /** Имя гостя */
    #[ORM\Column(
        name: "name",
        type: Types::STRING,
        length: 255,
        nullable: false
    )]
    private string $name;

    /** Фамилия гостя */
    #[ORM\Column(
        name: "surname",
        type: Types::STRING,
        length: 255,
        nullable: false
    )]
    private string $surname;

    /** Номер телефона гостя */
    #[ORM\Column(
        name: "phone_number",
        type: Types::BIGINT,
        nullable: false
    )]
    private int $phone_number;

    /** EMAIL гостя */
    #[ORM\Column(
        name: "email",
        type: Types::STRING,
        length: 255,
        nullable: false
    )]
    private string $email;

    /** Код Страны номера */
    #[ORM\ManyToOne(
        targetEntity: TechCountryPhoneNumber::class,
        inversedBy: "tech_guest",
    )]
    #[ORM\JoinColumn(
        name: "country_phone_number_id",
        referencedColumnName: "id",
        nullable: false
    )]
    private TechCountryPhoneNumber $country_phone_number;

    public function __construct(
        string $name,
        string $surname,
        string $phoneNumber,
        string $email,
        TechCountryPhoneNumber $country_phone_number
    ) {
        $this->setName($name)
            ->setSurname($surname)
            ->setPhoneNumber($phoneNumber)
            ->setEmail($email)
            ->setCountryPhoneNumber($country_phone_number);
    }

    #region Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;
        return $this;
    }

    public function getPhoneNumber(): int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(int $phone_number): self
    {
        $this->phone_number = $phone_number;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCountryPhoneNumber(): TechCountryPhoneNumber
    {
        return $this->country_phone_number;
    }

    public function setCountryPhoneNumber(TechCountryPhoneNumber $country_phone_number): self
    {
        $this->country_phone_number = $country_phone_number;
        return $this;
    }

    #endregion

}