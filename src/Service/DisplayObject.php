<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 02/12/2019
 * Time: 15:42
 */

namespace App\Service;


use App\Entity\Spot;

class DisplayObject
{
    /**
     * Renvois un tableau des noms des régions, contenant les spots.
     * Utilisé pour la bar de nav
     * @param Spot[] $spots
     * @return $regions : tableau key=régions, elem = tab de Spot
     */
    public function regionsForNavBar($spots) {
        $regions = [];
        if ($spots != null) {
            foreach ($spots as $spot) {
                if (!array_key_exists($spot->getRegion()->getNom(), $regions)) {
                    // Région pas dans le tableau
                    $regions[$spot->getRegion()->getNom()] = [];
                }
                $regions[$spot->getRegion()->getNom()][] = $spot;
            }
        }
        return $regions;
    }
}