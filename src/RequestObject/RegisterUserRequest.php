<?php

declare(strict_types=1);

namespace App\RequestObject;

use App\Validator\Constraint\UniqueDTO;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueDTO(
 *     fields={"username"},
 *     entityClass="App\Entity\User",
 *     message="User with such username already exists"
 * )
 */
final class RegisterUserRequest implements RequestObject
{
    /**
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="255")
     *
     * @Serializer\Type("string")
     */
    private ?string $username = null;

    /**
     * @Assert\NotBlank
     * @Assert\Length(min="3", max="255")
     *
     * @Serializer\Type("string")
     */
    private ?string $password = null;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
}
