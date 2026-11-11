<?php

namespace App\Tests\Integration\Command;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommandTest extends KernelTestCase
{
    private CommandTester $tester;

    protected function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $this->tester = new CommandTester($application->find('app:user:create'));
    }

    public function testCreatesAdminByDefault(): void
    {
        $status = $this->tester->execute(['email' => 'new.admin@exemple.test', '--password' => 'secret']);

        self::assertSame(Command::SUCCESS, $status);
        self::assertStringContainsString('Utilisateur créé', $this->tester->getDisplay());

        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'new.admin@exemple.test']);
        self::assertNotNull($user);
        self::assertSame('new.admin', $user->getName());
        self::assertContains('ROLE_ADMIN', $user->getRoles());
        self::assertTrue(self::getContainer()->get(UserPasswordHasherInterface::class)->isPasswordValid($user, 'secret'));
    }

    public function testUpdatesExistingUser(): void
    {
        $status = $this->tester->execute([
            'email' => 'user@goodpractice.test',
            '--password' => 'nouveau',
            '--name' => 'Lecteur renommé',
            '--role' => ['ROLE_EDITOR'],
        ]);

        self::assertSame(Command::SUCCESS, $status);
        self::assertStringContainsString('Utilisateur mis à jour', $this->tester->getDisplay());

        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email' => 'user@goodpractice.test']);
        self::assertSame('Lecteur renommé', $user?->getName());
        self::assertContains('ROLE_EDITOR', $user->getRoles());
    }

    public function testPasswordIsAskedInteractivelyAndMustNotBeEmpty(): void
    {
        $this->tester->setInputs(['   ']);

        $status = $this->tester->execute(['email' => 'empty@exemple.test']);

        self::assertSame(Command::FAILURE, $status);
        self::assertStringContainsString('Mot de passe vide', $this->tester->getDisplay());
    }
}
