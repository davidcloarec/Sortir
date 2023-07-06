<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $campuses = [
            "Nantes",
            "Rennes",
            "Quimper",
            "Niort",
            "Campus en ligne",
        ];
        foreach($campuses as $campus){
            $newCampus = new Campus();
            $newCampus->setName($campus);
            $manager->persist($newCampus);
        }
        $manager->flush();
    }
}