<?php

declare(strict_types=1);

namespace App\Entity;

use App\RequestObject\RegisterUserRequest;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="uuid", type="string", length=36)
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Serializer\Expose
     */
    private string $uuid;

    /**
     * @ORM\Column(name="username", type="string", length=255)
     *
     * @Serializer\Expose
     */
    private string $username;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    private string $password;

    /**
     * @ORM\Column(name="created_at", type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    private function __construct()
    {
    }

    public static function with(RegisterUserRequest $registerRequest, UserPasswordEncoderInterface $encoder): self
    {
        $self = new self();

        $self->uuid = Uuid::uuid4()->toString();
        $self->username = $registerRequest->getUsername();
        $self->password = $encoder->encodePassword($self, $registerRequest->getPassword());

        $self->createdAt = new DateTimeImmutable();

        return $self;
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
    }
}
