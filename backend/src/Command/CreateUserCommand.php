<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Crée (ou met à jour) un utilisateur en ligne de commande.
 * Utile pour bootstrapper l'admin en prod (les fixtures ne tournent qu'en dev/test).
 *
 *   php bin/console app:user:create admin@exemple.fr --role=ROLE_ADMIN
 */
#[AsCommand(
    name: 'app:user:create',
    description: 'Crée un utilisateur (admin par défaut) avec mot de passe hashé.',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Adresse e-mail (identifiant de connexion)')
            ->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Mot de passe (sinon demandé en masqué)')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, "Nom affiché (défaut : partie locale de l'e-mail)")
            ->addOption('role', 'r', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Rôle(s) à attribuer', ['ROLE_ADMIN']);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getArgument('email');
        $name = (string) ($input->getOption('name') ?? strstr($email, '@', true) ?: $email);
        $roles = $input->getOption('role');

        $password = $input->getOption('password');
        if (null === $password) {
            $question = (new Question('Mot de passe : '))->setHidden(true)->setHiddenFallback(false);
            $password = $io->askQuestion($question);
        }
        if (!\is_string($password) || '' === trim($password)) {
            $io->error('Mot de passe vide.');

            return Command::FAILURE;
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);
        $isNew = null === $user;
        if ($isNew) {
            $user = (new User())->setEmail($email);
        }

        $user
            ->setName($name)
            ->setRoles($roles);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf(
            '%s : %s (%s)',
            $isNew ? 'Utilisateur créé' : 'Utilisateur mis à jour',
            $email,
            implode(', ', $roles),
        ));

        return Command::SUCCESS;
    }
}
