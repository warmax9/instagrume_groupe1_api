<?php

namespace App\DataFixtures;

use App\Entity\Like;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class LikeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $like1 = new Like();
        $like1->setValue(true);
        $like1->setPost($this->getReference(PostFixtures::AGRUMMES_POST_REFERENCE));
        $like1->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $manager->persist($like1);

        $like2 = new Like();
        $like2->setValue(false);
        $like2->setCommentaire($this->getReference(CommantaireFixtures::COM_POST_REFERENCE));
        $like2->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $manager->persist($like2);
        $manager->flush();

        $manager->flush();
    }
}
