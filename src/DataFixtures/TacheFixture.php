<?php

namespace App\DataFixtures;

use App\Entity\Tache;
use DateTime;
use DateTimeInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TacheFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
         $date = new \DateTime('18/02/2020');
        $t1 = new Tache();
        $t1->setTitre("Test")
            ->setDescription("test_desc")
            ->setStatut("En cour")
            ->setDateCreation($date)
        ;

        $manager->persist($t1);

        $manager->flush();
    }
}
