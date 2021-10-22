<?php

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Countries
 *
* @ORM\Table(name="countries", uniqueConstraints={@ORM\UniqueConstraint(name="countries_code_unique")})
 * @ORM\Entity
 */
class Country
{

    /** @ORM\Column(name="code", type="string", length=3, nullable=false) 
    * @ORM\Id
    */
    private string $code;
    /** @ORM\Column(name="name", type="string", length=80, nullable=false) */
    private string $name;
    /** @ORM\Column(name="phone_code", type="smallint", nullable=false) */
    private int $phoneCode;

    public function __construct(string $code, string $name, int $phoneCode)
    {
        $this->code = $code;
        $this->name = $name;
        $this->phoneCode = $phoneCode;
    }
    public function getCode(): string
    {
        return $this->code;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getPhoneCode(): string
    {
        return $this->phoneCode;
    }

    public static function create(string $code, string $name, int $phoneCode)
    {
        return new self(
            $code,
            $name,
            $phoneCode
        );
    }
}
