<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testRoleUserIsAlwaysGrantedWithoutDuplicates(): void
    {
        $user = new User();
        self::assertSame(['ROLE_USER'], $user->getRoles());

        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        self::assertEqualsCanonicalizing(['ROLE_ADMIN', 'ROLE_USER'], $user->getRoles());
    }

    public function testUserIdentifierIsEmail(): void
    {
        $user = (new User())->setEmail('jane@exemple.test')->setName('Jane');

        self::assertSame('jane@exemple.test', $user->getUserIdentifier());
        self::assertSame('jane@exemple.test', $user->getEmail());
        self::assertSame('Jane', $user->getName());
        self::assertNull($user->getId());
    }

    public function testEraseCredentialsClearsPlainPasswordOnly(): void
    {
        $user = (new User())->setPassword('hash')->setPlainPassword('secret');
        self::assertSame('secret', $user->getPlainPassword());

        $user->eraseCredentials();

        self::assertNull($user->getPlainPassword());
        self::assertSame('hash', $user->getPassword());
    }

    public function testCollectionsAreInitialized(): void
    {
        $user = new User();

        self::assertCount(0, $user->getArticles());
        self::assertCount(0, $user->getComments());
    }
}
