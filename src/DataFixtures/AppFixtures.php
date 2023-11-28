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
        $userAdmin->setPhoto("admin.jpg");
        $userAdmin->setModo(true);
        $manager->persist($userAdmin);

        $user = new User();
        $user->setUsername('user1');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setPhoto("user1.jpg");
        $user->setModo(false);
        $manager->persist($user);

        $post = new Post();
        $post->setDescription("Description photo kiwi");
        $post->setImage('kiwi.jpg');
        $post->setUser($userAdmin);
        $post->setIsOpen(true);
        $manager->persist($post);

        $post = new Post();
        $post->setDescription("Le petit dej d'un go muscu");
        $post->setImage('agrummes.jpg');
        $post->setUser($user);
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
