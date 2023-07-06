<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Venue;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cities = [
            ["Nantes","44000"],
            ["Rennes","35000"],
            ["Quimper","29000"],
            ["Niort","79000"],
            ["Brest","29200"],
        ];
        $venues = [
            "Nantes"=>[
                ["Patinoire du Petit Port","Bd du Petit Port","47.24386627231798","-1.555413936689077"],
                ["Hippodrome de Nantes","5 Bd des Tribunes","47.24791521416867","-1.5624488497161957"],
                ["Le Jungle Bar","2 All. Duguay Trouin","47.21393153537024","-1.5542603504371744"],
                ["Fleming's Irish Pub","22 Rue des Carmes","47.21666485065589","-1.5552066227057677"],
            ],
            "Rennes"=>[
                ["Peacock","8 Rue Saint-Sauveur","48.1125470930782","-1.6826456960111416"],
                ["KilKenny's Pub","3 Rue du Vau Saint-Germain","48.111687487855455","-1.6770667017344096"],
                ["Stade Commandant Bougouin","10 Rue Alphonse Guérin","48.109337826793265","-1.6631192143900206"],
                ["Block'Out","1 Rue de Bray","48.1048961009904","-1.6323918305274048"],
            ],
            "Quimper"=>[
                ["La Baleine Déshydratée","16 Rue Haute","47.98881044355939","-4.11179424690568"],
                ["V And B","52 Av. de Kéradennec","47.976041647335705","-4.077000660400762"],
                ["Ceili","4 Rue Aristide Briand","47.99606282136","-4.097147145059326"],
                ["Café des Arts","4 Rue Sainte-Catherine","47.9947075052603","-4.1025305892351325"],
            ],
            "Niort"=>[
                ["Musée Bernard d'Agesci","26 Av. de Limoges","46.32127579899027","-0.4544056469753594"],
                ["Vintage","9 Rue Pierre Antoine Baugier","46.327899943026715","-0.46654796046963315"],
                ["Le 5 Mai","115 Quai Maurice Métayer","46.33402781865117","-0.4786131451288002"],
                ["Auberge de la Roussille","30 Imp. de la Roussille","46.33275649014187","-0.5079992874585502"],
            ],
            "Brest"=>[
                ["Last Player","11 Rue de la Porte","48.384427854258334","-4.498531731548255"],
                ["Blind Piper","95 Rue de Siam","48.38910746508482","-4.487624629701983"],
                ["Rïnkla Stadium","Rue de Savoie","48.405514348920455","-4.50859223068118"],
                ["Le Multiplexe","10 Av. Georges Clemenceau","48.39086844376638","-4.487331666777026"],
            ],
        ];
        foreach($cities as $city){
            $newCity = new City();
            $newCity->setName($city[0]);
            $newCity->setPostalCode($city[1]);
            foreach ($venues[$newCity->getName()] as $venue){
                $newVenue = new Venue();
                $newVenue->setCity($newCity);
                $newVenue->setName($venue[0]);
                $newVenue->setStreet($venue[1]);
                $newVenue->setLatitude($venue[2]);
                $newVenue->setLongitude($venue[3]);
                $manager->persist($newVenue);
                $newCity->addVenue($newVenue);
            }
            $manager->persist($newCity);
        }
        $manager->flush();
    }
}