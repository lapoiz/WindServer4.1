<?php
namespace App\Utils;


class WebsiteGetData
{

	/**
	 * @param $windKmh : vent en Km/h
	 * @return float : vitesse en knds (noeuds)
	 */
	static function transformeKmhByNoeud($windKmh) {
		return round($windKmh/1.852,0);
	}

	static function transformeOrientation($orientation) {
		$result='';

		switch ($orientation) {
			case 'nord':
				$result='n';
				break;

			case 'sud':
				$result='s';
				break;

			case 'ouest':
				$result='w';
				break;

			case 'est':
				$result='e';
				break;
		}
		return $result;
	}

    static function transformeOrientationDeg($orientationNom) {
        $result=-1;

		switch ($orientationNom) {
			case 'n':
				$result=0;
				break;
            case 'nne':
				$result=22.5;
				break;
            case 'ne':
				$result=45;
				break;
            case 'ene':
				$result=67.5;
				break;
            case 'e':
				$result=90;
				break;
            case 'ese':
				$result=112.5;
				break;
            case 'se':
				$result=135;
				break;
            case 'sse':
				$result=157.5;
				break;
            case 's':
				$result=180;
				break;
            case 'ssw':
				$result=202.5;
				break;
            case 'sw':
				$result=225;
				break;
            case 'wsw':
				$result=247.5;
				break;
            case 'w':
				$result=270;
				break;
            case 'wnw':
				$result=292.5;
				break;
            case 'nw':
				$result=315;
				break;
            case 'nnw':
				$result=337.5;
				break;
        }

        return $result;
    }

    static function transformeOrientationNomLongDeg($orientationNom) {
        $result=-1;

        switch ($orientationNom) {
            case 'nord':
                $result=0;
                break;
            case 'nord-nord-est':
                $result=22.5;
                break;
            case 'nord-est':
                $result=45;
                break;
            case 'est-nord-est':
                $result=67.5;
                break;
            case 'est':
                $result=90;
                break;
            case 'est-sud-est':
                $result=112.5;
                break;
            case 'sud-est':
                $result=135;
                break;
            case 'sud-sud-est':
                $result=157.5;
                break;
            case 'sud':
                $result=180;
                break;
            case 'sud-sud-west':
                $result=202.5;
                break;
            case 'sud-west':
                $result=225;
                break;
            case 'west-sud-west':
                $result=247.5;
                break;
            case 'west':
                $result=270;
                break;
            case 'west-nord-west':
                $result=292.5;
                break;
            case 'nord-west':
                $result=315;
                break;
            case 'nord-nord-west':
                $result=337.5;
                break;
        }
        return $result;
    }

	static function transformeOrientationDegToNom($orientationDeg) {
		$result='';

		if ($orientationDeg < 11.5) {
			$result='n';
		} elseif ($orientationDeg < 33.75) {
			$result='nne';
		} elseif ($orientationDeg < 56.25) {
			$result='ne';
		} elseif ($orientationDeg < 78.75) {
			$result='ene';
		} elseif ($orientationDeg < 101.25) {
			$result='e';
		} elseif ($orientationDeg < 123.75) {
			$result='ese';
		} elseif ($orientationDeg < 146.25) {
			$result='se';
		} elseif ($orientationDeg < 168.75) {
			$result='sse';
		} elseif ($orientationDeg < 191.25) {
			$result='s';
		} elseif ($orientationDeg < 213.75) {
			$result='ssw';
		} elseif ($orientationDeg < 236.25) {
			$result='sw';
		} elseif ($orientationDeg < 258.75) {
			$result='wsw';
		} elseif ($orientationDeg < 281.25) {
			$result='w';
		} elseif ($orientationDeg < 303.75) {
			$result='wnw';
		} elseif ($orientationDeg < 326.25) {
			$result='nw';
		} elseif ($orientationDeg < 348.75) {
			$result='nnw';
		} else {
			$result='n';
		}
		return $result;
	}

    /**
     * @param $orientationState chiffre du type -1 , 0, 1 , 2
     * @return string l'état du type: "OK", "warn", "KO", "top"
     */
    static function transformeOrientationState($orientationState) {
        $result='?';

        switch ($orientationState) {
            case -1:
                $result='KO';
                break;

            case 0:
                $result='warn';
                break;

            case 1:
                $result='OK';
                break;

            case 2:
                $result='top';
                break;
        }
        return $result;
    }

}