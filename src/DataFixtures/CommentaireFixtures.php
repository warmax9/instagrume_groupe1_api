<?php

namespace App\DataFixtures;

use App\Entity\Commentaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class CommentaireFixtures extends Fixture
{
    public const COM_POST_REFERENCE = 'commentaire';
    public const SUBCOM_POST_REFERENCE = 'sub_commentaire';

    public function load(ObjectManager $manager): void
    {
        $commentaire = new Commentaire();
        $commentaire->setPost($this->getReference(PostFixtures::AGRUMMES_POST_REFERENCE));
        $commentaire->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $commentaire->setContent("je suis content");
        $manager->persist($commentaire);
        $this->addReference(self::COM_POST_REFERENCE, $commentaire);

        $commentaire = new Commentaire();
        $commentaire->setCommentaireParent($this->getReference(self::COM_POST_REFERENCE));
        $commentaire->setUser($this->getReference(UserFixtures::USER1_USER_REFERENCE));
        $commentaire->setContent("ok.");
        $manager->persist($commentaire);
        $this->addReference(self::SUBCOM_POST_REFERENCE, $commentaire);

        $manager->flush();
    }
}
