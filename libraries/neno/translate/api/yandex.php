<?php
/**
 * @package     Neno
 * @subpackage  TranslateApi
 *
 * @copyright   Copyright (c) 2014 Jensen Technologies S.L. All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_NENO') or die;
jimport('joomla.application.component.helper');

/**
 * Class NenoTranslateApiYandex
 *
 * @since  1.0
 */
class NenoTranslateApiYandex extends NenoTranslateApi
{
    /**
     * @var string
     */
    protected $apiKey;

    /**
     * Translate text using yandex api
     *
     * @param   string $apiKey  the key provided by user
     * @param   string $text    text to translate
     * @param   string $source  source language
     * @param   string $target  target language default french
     *
     * @return string
     */
    public function translate($text,$source="en-US",$target="fr-FR")
    {
        // get the key configured by user
        $this->apiKey = JComponentHelper::getParams('com_neno')->get('yandexApiKey');

        // convert from JISO to ISO codes
        $target = $this->convertFromJisoToIso($target);

        // language parameter for url
        $source = $this->convertFromJisoToIso($source);
        $lang = $source."-".$target;

        // check availability of langauage pair for translation
        $isAvailable = $this->isTranslationAvailable($lang);

        if(!$isAvailable)
        {
            return null;
        }


        if($this->apiKey == "")
        {
            // Use default key if not provided
            $this->apiKey = 'trnsl.1.1.20150213T133918Z.49d67bfc65b3ee2a.b4ccfa0eaee0addb2adcaf91c8a38d55764e50c0';
        }

        // For POST requests, the maximum size of the text being passed is 10000 characters.
        $textString = str_split($text, 10000);
        $textStrings='';
        foreach($textString as $str)
        {
            $textStrings .= '&text=' . rawurlencode($str);
        }

        $url    = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' . $this->apiKey . '&lang=' . $lang . $textStrings;

        // Invoke the GET request.
        $response = $this->get($url);

        $text = null;

        // Log it if server response is not OK.
        if ($response->code != 200)
        {
            NenoLog::log('Yandex api failed with response: ' . $response->code, 1);
        }
        else
        {
            $reponseBody=json_decode($response->body);
            $text = $reponseBody->text[0];
        }

        return $text;

    }

    /**
     * Method to make supplied language codes equivalent to yandex api codes
     *
     * @param   string $jiso Joomla ISO language code
     *
     * @return string
     */
    public function convertFromJisoToIso($jiso)
    {
        // split the language code parts using hypen
        $jisoParts = (explode("-",$jiso));
        $iso2Tag = strtolower($jisoParts[0]);

        switch($iso2Tag)
        {
            case "nb":
                $iso2 = "no";
                break;

            default:
                $iso2 = $iso2Tag;
                break;
        }

        return $iso2;
    }

    /**
     * Method to check if language pair is available or not in yandex api
     *
     * @param   string $iso2Pair ISO2 language code pair
     *
     * @return boolen
     */
    public function isTranslationAvailable($isoPair)
    {
        // split the language pair using hypen
        $isoParts = (explode("-",$isoPair));

        // array of iso language codes not available in yandex api
        $languages = array("gl", "ja", "af", "ko", "fa", "sy", "ta", "ug", "hi", "sw", "srp", "cy", "si");
        $available = 1;

        foreach($isoParts as $part)
        {
            if (in_array($part , $languages))
            {
                $available = 0;
            }
        }

        return $available;
    }

    /**
     * Method to get supported language pairs for translation from yandex api
     *
     * @return json
     */
    public function getApiSupportedLanguagePairs()
    {
        // get the key configured by user
        $this->apiKey = JComponentHelper::getParams('com_neno')->get('yandexApiKey');

        if($this->apiKey == "")
        {
            // Use default key if not provided
            $this->apiKey = 'trnsl.1.1.20150213T133918Z.49d67bfc65b3ee2a.b4ccfa0eaee0addb2adcaf91c8a38d55764e50c0';
        }

        $url    = 'https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=' . $this->apiKey . '&ui=uk';

        // Invoke the GET request.
        $response = $this->get($url);

        $json = null;

        // Log it if server response is not OK.
        if ($response->code != 200)
        {
            NenoLog::log('Yandex api failed with response: ' . $response->code, 1);
        }
        else
        {
            $json = $response->body;
        }

        return $json;
    }

}

