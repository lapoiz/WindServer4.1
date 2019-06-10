<?php

namespace App\DataFixtures;

use App\Entity\Spot;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SpotFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /* Plus besoin avec import

        $spot = new Spot();
        $spot->setName("Merville-Franceville-Plage");
        $spot->setGpsLat(49.2828000);
        $spot->setGpsLong(-0.2313900);
        $spot->setKmAutorouteFromParis(204);
        $spot->setKmFromParis(223);
        $spot->setTimeFromParis(139);
        $spot->setDescription("Le spot de Franceville fonctionne essentiellement par brise marine qui vient du nord-est et se lève l'après midi. Mais il fonctionne également trés bien par NW et W, qui peut lever de grosses vagues. Les vents de sud ouest rendent le spot principal Side off, mais permet de surfer parfois de trés longue gauche jamais trés grosses mais trés propres. Enfin tous les secteurs de vent du SE au SW sont navigable dans l'estuaire avec encore de magnifiques flacs, pour être en sécurité. Quand le vent rentre de Nord Est, il y a une sorte d'effet venturi qui accélère largement.");

        $manager->persist($spot);

        $manager->flush();
        */
    }
}
