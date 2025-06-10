<?php

namespace App\DataFixtures;

use App\Service\UserService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

/**
 * install:
 * composer require doctrine/doctrine-fixtures-bundle --dev
 */
class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserService $userService
    ) {}

    public static function getGroups(): array
    {
        return ['UserFixtures'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->userService->createUser(
            email: 'testuser@example.com',
            password: 'StrongPass123',
            firstName: 'testuser'
        );

        $this->userService->createUser(
            email: 'seconduser@example.com',
            password: 'StrongPass123',
            firstName: 'secondUser'
        );
    }
}
