<?php

namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RegionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $regionPDC = new Region();
        $regionPDC->setNom("Pas de calais");
        $regionPDC->setCodeRegion('pdc');
        $regionPDC->setDescription("Dans le grand nord.");
        $regionPDC->setNumDisplay(1);
        $manager->persist($regionPDC);

        $regionS = new Region();
        $regionS->setNom("Somme");
        $regionS->setCodeRegion('som');
        $regionS->setDescription("De ... à ...");
        $regionS->setNumDisplay(2);
        $manager->persist($regionS);

        $regionC = new Region();
        $regionC->setNom("Calvados");
        $regionC->setCodeRegion('cal');
        $regionC->setDescription("De ... à ...");
        $regionC->setNumDisplay(3);
        $manager->persist($regionC);

        $regionSM = new Region();
        $regionSM->setNom("Seine-Maritime");
        $regionSM->setCodeRegion('sem');
        $regionSM->setDescription("Du Havre jusqu'après Dieppe.");
        $regionSM->setNumDisplay(4);
        $manager->persist($regionSM);

        $regionPA = new Region();
        $regionPA->setNom("Provence-Alpes-Cote d'Azur");
        $regionPA->setCodeRegion('paca');
        $regionPA->setDescription("Soleil et parfois vent.");
        $regionPA->setNumDisplay(10);
        $manager->persist($regionPA);

        $manager->flush();
    }
}