<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin user',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = 'admin@tuniway.com';
        $username = 'admin';
        $password = 'admin';

        $userRepository = $this->entityManager->getRepository(User::class);

        // Check if admin already exists
        if ($userRepository->findOneBy(['email' => $email]) || $userRepository->findOneBy(['username' => $username])) {
            $io->warning('Admin user already exists.');
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );

        // Required by User entity usually, setting role field if it exists separately from roles array
        if (method_exists($user, 'setRole')) {
            $user->setRole('ADMIN');
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Admin user created successfully.');
        $io->text([
            sprintf('Username: %s', $username),
            sprintf('Password: %s', $password),
        ]);

        return Command::SUCCESS;
    }
}
