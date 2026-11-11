<?php

namespace App\Tests\Unit\State;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\State\UserPasswordHasher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordHasherTest extends TestCase
{
    public function testHashesPlainPasswordThenDelegates(): void
    {
        $user = (new User())->setPlainPassword('secret');

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::once())->method('hashPassword')->with($user, 'secret')->willReturn('hashed');

        $inner = $this->createMock(ProcessorInterface::class);
        $inner->expects(self::once())->method('process')->with($user)->willReturnArgument(0);

        $result = (new UserPasswordHasher($inner, $hasher))->process($user, new Post());

        self::assertSame($user, $result);
        self::assertSame('hashed', $user->getPassword());
        self::assertNull($user->getPlainPassword());
    }

    public function testDoesNotHashWithoutPlainPassword(): void
    {
        $user = (new User())->setPassword('existing');

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::never())->method('hashPassword');

        $inner = $this->createMock(ProcessorInterface::class);
        $inner->method('process')->willReturnArgument(0);

        (new UserPasswordHasher($inner, $hasher))->process($user, new Post());

        self::assertSame('existing', $user->getPassword());
    }
}
