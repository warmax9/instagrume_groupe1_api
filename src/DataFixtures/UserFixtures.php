<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public const ADMIN_USER_REFERENCE = 'admin';
    public const USER1_USER_REFERENCE = 'user1';

    public  function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setPhoto("admin.jpeg");
        $user->setModo(true);
        $manager->persist($user);
        $this->addReference(self::USER1_USER_REFERENCE, $user);

        $user = new User();
        $user->setUsername('user1');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setPhoto("user1.jpg");
        $manager->persist($user);
        $this->addReference(self::ADMIN_USER_REFERENCE, $user);

        $manager->flush();
    }
}
