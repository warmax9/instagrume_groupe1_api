<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use App\Entity\Like;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    public  function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {

        $userAdmin = new User();
        
        $userAdmin->setUsername('admin');
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, 'password'));
        $userAdmin->setPhoto("photo");
        $userAdmin->setModo(true);
        $manager->persist($userAdmin);

        $post = new Post();
        $post->setDescription("description");
        $post->setImage('image');
        $post->setUser($userAdmin);
        $post->setIsOpen(true);
        $manager->persist($post);

        $commentaire = new Commentaire();
        $commentaire->setPost($post);
        $commentaire->setUser($userAdmin);
        $commentaire->setContent("je suis content");
        $manager->persist($commentaire);

        $like1 = new Like();
        $like1->setValue(true);
        $like1->setPost($post);
        $like1->setUser($userAdmin);
        $manager->persist($like1);

        $like2 = new Like();
        $like2->setValue(false);
        $like2->setCommentaire($commentaire);
        $like2->setUser($userAdmin);
        $manager->persist($like2);
        $manager->flush();
    }
}
