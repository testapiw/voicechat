<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\UserRole;
use App\Enum\UserStatus;
//use App\Service\EmailService;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
      //  private EmailService $emailService
    ) {}

    public function createUser(
        string $email,
        string $password,
        string $firstName,
        ?string $lastName = null,
        ?string $phone = null,
       // ?bool $isActive = true,
        array $roles = ['ROLE_USER']
    ): User {
        
        $invalidRoles = array_diff($roles, UserRole::values());

        if (!empty($invalidRoles)) {
            throw new \InvalidArgumentException('Invalid roles: ' . implode(', ', $invalidRoles));
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPhone($phone);
        $user->setConfirmationCode(bin2hex(random_bytes(16))); // TD: ...
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->setLastLoginAt(new \DateTimeImmutable());
    
        $hashed = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashed);
    
        $this->userRepository->save($user);


        //$this->emailService->sendConfirmationCode($user->getEmail(), $user->getConfirmationCode());

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->userRepository->remove($user);
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['email'])) {
            // $user->setEmail($data['email']);
        }

        if (isset($data['isActive'])) {
            $user->setActive();
        }

        $user->setUpdatedAt(new \DateTimeImmutable());
        $this->userRepository->save($user);

        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }
}
