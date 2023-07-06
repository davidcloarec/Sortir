<?php

namespace App\DataFixtures;

use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StateFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $states = [
          "créée",
          "ouverte",
          "cloturé",
          "en cours",
          "passé",
          "annulée",
          "archivée",
        ];

        foreach($states as $state){
            $newState = new State();
            $newState->setLibelle($state);
            $manager->persist($newState);
        }
        $manager->flush();
    }
}