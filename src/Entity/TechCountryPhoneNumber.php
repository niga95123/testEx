<?php

namespace App\Entity;

use App\Entity\Guest\TechGuest;
use App\Repository\TechCountryPhoneNumberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
#[ORM\Table(name: "tech_country_phone_number", schema: "public")]
#[ORM\Entity(repositoryClass: TechCountryPhoneNumberRepository::class)]
class TechCountryPhoneNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue("IDENTITY")]
    #[ORM\Column(
        name: "id",
        type: Types::BIGINT
    )]
    private ?int $id = null;

    /** Название страны */
    #[ORM\Column(
        name: "country_name",
        type: Types::STRING,
        length: 255,
        nullable: false
    )]
    private string $country_name;

    /** Код страны */
    #[ORM\Column(
        name: "phone_num_code",
        type: Types::INTEGER,
        nullable: false
    )]
    private int $phone_num_code;

    #[ORM\OneToMany(
        targetEntity: TechGuest::class,
        mappedBy: "country_phone_number"
    )]
    private Collection $techGuest;

    public function __construct(
        string $country_name,
        int $phone_num_code
    ) {
        $this->setCountryName($country_name)
            ->setPhoneNumCode($phone_num_code);

        $this->techGuest = new ArrayCollection();
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
    public function getCountryName(): string
    {
        return $this->country_name;
    }

    public function setCountryName(string $country_name): self
    {
        $this->country_name = $country_name;
        return $this;
    }

    public function getPhoneNumCode(): int
    {
        return $this->phone_num_code;
    }

    public function setPhoneNumCode(int $phone_num_code): self
    {
        $this->phone_num_code = $phone_num_code;
        return $this;
    }

    #endregion


}