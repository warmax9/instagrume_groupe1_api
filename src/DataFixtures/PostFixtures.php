<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class PostFixtures extends Fixture
{
    public const KIWI_POST_REFERENCE = 'kiwi';
    public const AGRUMMES_POST_REFERENCE = 'agrummes';

    public function load(ObjectManager $manager): void
    {
        $post = new Post();
        $post->setDescription("Description photo kiwi");
        $post->setImage('kiwi.jpg');
        $post->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $post->setIsOpen(true);
        $manager->persist($post);
        $this->addReference(self::KIWI_POST_REFERENCE, $post);

        $post = new Post();
        $post->setDescription("Le petit dej d'un go muscu");
        $post->setImage('agrummes.jpg');
        $post->setUser($this->getReference(UserFixtures::USER1_USER_REFERENCE));
        $post->setIsOpen(true);
        $manager->persist($post);
        $this->addReference(self::AGRUMMES_POST_REFERENCE, $post);

        $manager->flush();
    }
}
