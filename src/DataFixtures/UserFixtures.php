<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Admin
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@demo.fr')
            ->setPassword($this->passwordHasher->hashPassword($user, 'admin'))
            ->setUsername('Admin');
        $manager->persist($user);

        // 10 Users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email)
                ->setPassword($this->passwordHasher->hashPassword($user, 'test'))
                ->setUsername($faker->firstName());
            $manager->persist($user);
            $this->addReference('USER' . $i, $user);
        }

        $manager->flush();
    }
}
