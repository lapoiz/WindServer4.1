<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 15/04/2019
 * Time: 16:43
 */

namespace App\Utils;


use App\Entity\MareeRestriction;
use App\Entity\Spot;
use Goutte\Client;

class GetDataMaree
{

    static function putDataFromMareeInfo(Spot $spot, $urlInfoMaree) {
        $spot->setURLMaree($urlInfoMaree);
        preg_match('#http://maree.info/([0-9]*)#',$urlInfoMaree,$data);
        $idSpotMareeInfo=$data[1];
        $tabDataExtrem = GetDataMaree::getExtremMaree($idSpotMareeInfo);
        $idDateUrl=$tabDataExtrem['max']['idDateUrl'];
        list($hauteurMBGrandeMaree,$hauteurMHGrandeMaree)=GetDataMaree::getHminHmaxMaree($idSpotMareeInfo, $idDateUrl);
        $spot->setHauteurMBGrandeMaree($hauteurMBGrandeMaree);
        $spot->setHauteurMHGrandeMaree($hauteurMHGrandeMaree);

        $idDateUrl=$tabDataExtrem['min']['idDateUrl'];
        list($hauteurMBPetiteMaree,$hauteurMHPetiteMaree)=GetDataMaree::getHminHmaxMaree($idSpotMareeInfo, $idDateUrl);
        $spot->setHauteurMBPetiteMaree($hauteurMBPetiteMaree);
        $spot->setHauteurMHPetiteMaree($hauteurMHPetiteMaree);

        $idDateUrl=$tabDataExtrem['norm']['idDateUrl'];
        list($hauteurMBMoyenneMaree,$hauteurMHMoyenneMaree)=GetDataMaree::getHminHmaxMaree($idSpotMareeInfo, $idDateUrl);
        $spot->setHauteurMBMoyenneMaree($hauteurMBMoyenneMaree);
        $spot->setHauteurMHMoyenneMaree($hauteurMHMoyenneMaree);
    }

    /*
     * scan la page des marées sur 2 mois.
     * Puis récupére la date de la marée la plus basse, de la marée normal (coef 80) et de la plus haute marée
     * Return array : {
     *
     */
    static function getExtremMaree($idSpotMareeInfo) {
        // Récupere la liste des amplitudes de marée: http://maree.info/$idURLInfoMaree/calendrier
        // Parse le tableau et récupére URL de la marée à coef le plus haut, de la marée le coef le plus bas, et de la marée à coef 80
        // sur la base de :
        // Tous les Table classe="CalendrierMois"
        //  Pour chaque TR
        //      récupére les TD class="coef"
        //          compare avec max, min et 80
        //          si OK
        //              get id de TD class="event" (en enlevant le D du début)
        //              get title de TD class="DW"

        $urlPage = "http://maree.info/".$idSpotMareeInfo."/calendrier";

        $client = new Client();
        $crawler = $client->request('GET', $urlPage);
        $status_code = $client->getResponse()->getStatus();
        if ($status_code == 200) {
            // Boucle dans tous les class="CalendrierMois"
            $mois = $crawler->filter('.CalendrierMois')->each(function ($nodeMonth, $iMonth) {
                // Boucle dans tous les tr de class="CalendrierMois"
                $day = $nodeMonth->filter('tr')->each(function ($nodeDay, $iDay) {
                    if ($iDay>0) {
                        $coef = $nodeDay->filter('.Coef')->each(function ($nodeCoef, $iCoef) {
                            return $nodeCoef->text();
                        });
                        $dateMaree=$nodeDay->filter('.Event');
                        $regExGetIdURL = '#[0-9]+#';
                        $idURL="";
                        // D20153011 -> 20153011
                        if (preg_match($regExGetIdURL,$dateMaree->attr('id'),$value)>0) {
                            $idURL = $value[0];
                        }
                        return ["coefs" => $coef,
                            "date" => $dateMaree->attr('title'),
                            "idDateUrl"=> $idURL
                        ];
                    }
                });
                return $day;
            });

            $coefMax = 0;
            $coefMin = 200;
            $coefNor = 80;
            $coefNorBis = 79; // Si pas de coef 80 durant 4 mois d'affilés (on ne sait jamais) ...
            $dataExtrem = array();

            // Récupére du tableau de tous les coef, que les extremes et le coef normal
            foreach ($mois as $unMois) {
                foreach ($unMois as $day) {
                    if ($day!=null) {
                        foreach ($day["coefs"] as $coef) {
                            if ($coef==$coefNor) {
                                $day["coef"]=$coef;
                                $dataExtrem["norm"]= $day;
                            } elseif ($coef==$coefNorBis) {
                                $day["coef"]=$coef;
                                $dataExtrem["normBis"]= $day;
                            } elseif ($coef > $coefMax) {
                                $coefMax = $coef;
                                $day["coef"]=$coef;
                                $dataExtrem["max"]= $day;
                            } elseif ($coef < $coefMin) {
                                $coefMin = $coef;
                                $day["coef"]=$coef;
                                $dataExtrem["min"]= $day;
                            }
                        }
                    }
                }
            }
            return $dataExtrem;
        } else {
            return null;
        }
    }

    static function getHminHmaxMaree($idSpotMareeInfo, $idDateUrl){
        $tabData = GetDataMaree::getHauteurMaree($idSpotMareeInfo,$idDateUrl);
        // exemple: http://windserver3/web/app_dev.php/admin/BO/ajax/maree/150/forDay/20170808?_=1494277199274
        $hauteurMax=0;
        $hauteurMin=20;
        foreach ($tabData as $hauteurStr)
        {
            preg_match('#([0-9.]*)m#',$hauteurStr,$data);
            $hauteur=floatval($data[1]);
            $hauteurMax = $hauteurMax > $hauteur ? $hauteurMax : $hauteur;
            $hauteurMin = $hauteurMin < $hauteur ? $hauteurMin : $hauteur;
        }
        return array($hauteurMin,$hauteurMax);
    }

    static function getHauteurMaree($idURLInfoMaree, $idDateURLInfoMaree) {
        // Récupere la page: http://maree.info/$idURLInfoMaree?d=$idDateURLInfoMaree
        // dateMaree = 20150106
        $url = "http://maree.info/".$idURLInfoMaree."?d=".$idDateURLInfoMaree;
        return GetDataMaree::getHauteurMareeFromURL($url);
    }

    static function getHauteurMareeFromURL($url) {
        // Récupere la page: $url=http://maree.info/$idURLInfoMaree?d=$idDateURLInfoMaree
        // $idDateURLInfoMaree = 20150106

        // Parse avec ce qui est déjà fait dans MareeGetData::getMaree
        // envoie la hauteur marée haute et marée basse
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $trMaree = $crawler->filter('#MareeJours_0');// premier elem car on arrive direct sur le bon jour

        return GetDataMaree::getElemMareeFromTr($trMaree);
    }

    static function getElemMareeFromTr($trMaree) {

        $regExGetTD = '#<[^>]*[^\/]>#i';
        $regExGetHeure = '#h#';
        $regExGetHauteur = '#[0-9]+m#';

        $trHMTL = $trMaree->html();
        //<th><a href="/16" onmouseover="QSr(this,'?d=201408121');" onclick="this.onmouseover();this.blur();return false;">Mer.<br><b>13</b></a></th>
        //<td><b>01h53</b><br>08h34<br><b>14h18</b><br>20h55</td>
        //<td><b>9,00m</b><br>0,60m<br><b>8,85m</b><br>0,80m</td>
        //<td><b>113</b><br> <br><b>112</b><br> </td>
        //$regExGetTD = '#<td>(.*)<\/td>#is';

        $elemHTML = preg_split( $regExGetTD, $trHMTL, -1, PREG_SPLIT_NO_EMPTY);
        $prevMaree=array();
        $tabHeureMaree = array();
        $numVal=0;
        foreach ($elemHTML as $elem) {
            if (preg_match($regExGetHeure,$elem)) {
                $tabHeureMaree[]= $elem;
            } elseif (preg_match($regExGetHauteur,$elem)) {
                $prevMaree[$tabHeureMaree[$numVal]]= str_replace(",",".",$elem);
                $numVal++;
            }
        }
        return $prevMaree;
    }

    // -1, 0, 1, 2, ? => "OK", "KO", "warn"
    static function getOrientationState($rowValue) {
        switch ($rowValue) {
            case -1:
                return 'KO';
            case 0:
                return 'warn';
            case 1:
                return 'OK';
            case 2:
                return 'top';
        }
        return '?'; // ? ou autre
    }

    // "OK", "KO", "warn" => -1, 0, 1, 2, ?
    static function getValueFromOrientationState($rowValue) {
        switch ($rowValue) {
            case 'KO':
                return -1;
            case 'warn':
                return 0;
            case 'OK':
                return 1;
            case 'top':
                return 2;
        }
        return -2; // ? ou autre
    }

    // exemple: "2->10,5"
    static function putMareeRestriction(Spot $spot, $mareeRestrictionValue, $mareeState)
    {
        $marreRestriction = new MareeRestriction();
        $marreRestriction->setSpot($spot);
        $marreRestriction->setState($mareeState); // "OK", "KO", "warn"

        // Récupére les valeurs max/min depuis le texte: "2->10,5"
        preg_match('#([0-9,.]*)->([0-9,.]*)#',$mareeRestrictionValue,$data);
        $marreRestriction->setHauteurMax(str_replace(',','.',$data[2]));
        $marreRestriction->setHauteurMin(str_replace(',','.',$data[1]));
        $spot->addMareeRestriction($marreRestriction);
    }
}