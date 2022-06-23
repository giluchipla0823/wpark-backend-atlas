<?php

namespace App\Helpers;

class XmlHelper
{
    /**
     * @param string $response
     * @return array
     */
    public static function parseToArray($response): array {

        // CONVERTIR RESPONSE EN ARRAY
        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);

        $xml = new \SimpleXMLElement($response);
        $body = $xml->xpath('//Response');
        $json = json_encode((array)$body);
        return json_decode($json);
    }

    /**
     * @param string $wsdl
     * @param string $xml_entry
     * @return array
     */
    public static function execCurl($wsdl, $xml_entry){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $wsdl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $xml_entry,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: text/xml'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
