<?php

namespace App\DataFixtures;

use App\Entity\Post;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class PostFixtures extends Fixture
{
    public const KIWI_POST_REFERENCE = 'kiwi';
    public const AGRUMMES_POST_REFERENCE = 'agrummes';
    public const SALADE_FRUIT_POST_REFERENCE = 'salade-fruit';
    public const FRAISE_POST_REFERENCE = 'fraise';
    public const ORANGE_POST_REFERENCE = 'orange';
    public const ANANAS_POST_REFERENCE = 'ananas';

    public function load(ObjectManager $manager): void
    {
        $post = new Post();
        $post->setDescription("Description photo kiwi");
        $post->setImage('kiwi.jpg');
        $post->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $manager->persist($post);
        $this->addReference(self::KIWI_POST_REFERENCE, $post);

        $post = new Post();
        $post->setDescription("Le petit dej d'un go muscu");
        $post->setImage('agrummes.jpg');
        $post->setUser($this->getReference(UserFixtures::USER1_USER_REFERENCE));
        $manager->persist($post);
        $this->addReference(self::AGRUMMES_POST_REFERENCE, $post);

        $post = new Post();
        $post->setDescription("Une petit salade de fruits");
        $post->setImage('salade-fruit.jpg');
        $post->setUser($this->getReference(UserFixtures::USER1_USER_REFERENCE));
        $manager->persist($post);
        $this->addReference(self::SALADE_FRUIT_POST_REFERENCE, $post);

        $post = new Post();
        $post->setDescription("Delicieuse fraise");
        $post->setImage('fraise.jpg');
        $post->setUser($this->getReference(UserFixtures::USER1_USER_REFERENCE));
        $manager->persist($post);
        $this->addReference(self::FRAISE_POST_REFERENCE, $post);

        $post = new Post();
        $post->setDescription("Hmmm");
        $post->setImage('orange.jpeg');
        $post->setUser($this->getReference(UserFixtures::USER1_USER_REFERENCE));
        $manager->persist($post);
        $this->addReference(self::ORANGE_POST_REFERENCE, $post);

        $post = new Post();
        $post->setDescription("Magnifique");
        $post->setImage('ananas.jpeg');
        $post->setUser($this->getReference(UserFixtures::USER1_USER_REFERENCE));
        $manager->persist($post);
        $this->addReference(self::ANANAS_POST_REFERENCE, $post);

        $manager->flush();
    }
}
