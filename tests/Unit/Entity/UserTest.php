<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use App\RequestObject\RegisterUserRequest;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserTest extends TestCase
{
    public function test_user_creates_successfully(): void
    {
        $username = 'test';
        $passwordHash = '$argon2id$v=19$m=65536,t=4,p=1$RF7/M71LlaEg/kOGclzuQA$ZdtsOfLQe9DqdO8lMkHKuhWUmwk23xbwSInA6veVDao';

        $request = new RegisterUserRequest();
        $request->setUsername($username);
        $request->setPassword('test_password');

        $passwordEncoder = $this->createConfiguredMock(UserPasswordEncoderInterface::class, [
            'encodePassword' => $passwordHash,
        ]);

        $user = User::with($request, $passwordEncoder);

        self::assertSame($username, $user->getUsername());
        self::assertSame($passwordHash, $user->getPassword());
        self::assertEqualsWithDelta(new DateTimeImmutable(), $user->createdAt(), 1);
        self::assertIsString($user->uuid());
        self::assertSame(['ROLE_USER'], $user->getRoles());
    }
}
