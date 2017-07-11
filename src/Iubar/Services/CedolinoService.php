<?php

namespace Iubar\Services;

use Iubar\Crypt\AesCbcPkcs5Padding;

class CedolinoService {

    public static function check($json_array){
    	$data = [];
        // http://php.net/manual/en/function.openssl-public-decrypt.php
        $valid = false;
        $titolare = null;

		if (self::isArrayValid($json_array)){
			$api = $json_array['api']; // Versione dell'api
			$sig = $json_array['sig'];
			$doc = $json_array['doc'];
			$cf_lav = $doc['lav']['cf'];
			$anno = $doc['periodo']['anno'];
			$mese = $doc['periodo']['mese'];
			$netto = $doc['retrib']['netto'];
			$foglio_num = $doc['tit']['fg'];
			$cf_titolare = $doc['tit']['cf'];

			if ($foglio_num == null || $foglio_num == '' || $foglio_num == 'null'){
				$data['msg'] = 'Busta paga non definitiva';
			}

			$key = \Slim\Slim::getInstance()->config('aes.key');
			$aes = new AesCbcPkcs5Padding($key);
			$iv = $aes->getIvsFromSignature($sig);
			$text = $aes->getCryptedDataFromSignature($sig);
			$hash = $aes->decrypt($text, $iv);

			$str = $cf_lav . $anno . $mese . $netto . $foglio_num . $cf_titolare;
			$sha1 = sha1($str);

			if (substr($sha1, 0, 15) === $hash){
				$valid = true;
			}
		}

		$data['valid'] = $valid;

        return $data;
    }

    private static function isArrayValid($array){
    	$valid = false;

    	// controllo se sono presenti le chiavi di primo livello: 'sig' e 'doc'
    	if (isset($array['doc']) && isset($array['sig']) && isset($array['api'])){
    		$doc = $array['doc'];

    		// controllo se l'array 'doc' contiene le chiavi 'lav', 'periodo', 'retrib' e 'tit'
    		if (isset($doc['lav']) && isset($doc['periodo']) && isset($doc['retrib']) && isset($doc['tit'])){
    			// controllo se l'array 'lav' contiene la chiave 'cf'
    			if (isset($doc['lav']['cf'])){
    				// controllo se l'array 'periodo' contiene le chiavi 'anno' e 'mese'
    				if (isset($doc['periodo']['anno']) && isset($doc['periodo']['mese'])){
    					// controllo se l'array 'retrib' contiene la chiave 'netto'
    					if (isset($doc['retrib']['netto'])){
    						// controllo se l'array 'tit' contiene le chiavi 'fg' e 'cf'
    						if (isset($doc['tit']['fg']) && isset($doc['tit']['cf'])){
    							$valid = true;
    						} else {
    							throw new \RuntimeException('Foglio e/o codice fiscale del titolare mancante');
    						}
    					} else {
    						throw new \RuntimeException('Netto retribuzione mancante');
    					}
    				} else {
    					throw new \RuntimeException('Anno e/o mese del periodo mancante');
    				}
    			} else {
    				throw new \RuntimeException('Codice fiscale lavoratore mancante');
    			}
    		} else {
    			throw new \RuntimeException('Alcuni campi non sono stati trovati: lavoratore, periodo, retribuzione o titolare dati');
    		}
    	} else {
    		throw new \RuntimeException('I dati non sembrano appartenere ad un cedolino elaborato con PagheOpen');
    	}

    	return $valid;
    }

}