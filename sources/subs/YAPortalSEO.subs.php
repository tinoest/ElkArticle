<?php
/**
 * @package "YAPortal" Addon for Elkarte
 * @author tinoest
 * @license BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 1.0.0
 *
 */
class YAPortalSEO {

    private static $latinCharMap = array(
        'A'  => array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ā', 'Ă', 'Ą', 'Ǎ', 'Ǻ'),
        'AE' => array('Æ', 'Ǽ'),
        'C'  => array('Ç', 'Ć', 'Ċ', 'Ĉ', 'Č'),
        'D'  => array('Ð', 'Ď', 'Đ'),
        'E'  => array('È', 'É', 'Ê', 'Ë', 'Ē', 'Ĕ', 'Ė', 'Ę', 'Ě'),
        'F'  => array('ƒ'),
        'G'  => array('Ĝ', 'Ğ', 'Ġ', 'Ģ'),
        'H'  => array('Ĥ', 'Ħ'),
        'I'  => array('Ì', 'Í', 'Î', 'Ï', 'Ĩ', 'Ī', 'Ĭ', 'Į', 'İ', 'Ǐ'),
        'IJ' => array('Ĳ'),
        'J'  => array('Ĵ'),
        'K'  => array('Ķ'),
        'L'  => array('Ĺ', 'Ļ', 'Ľ', 'Ŀ', 'Ł'),
        'N'  => array('Ñ', 'Ń', 'Ņ', 'Ň', 'ŉ'),
        'O'  => array('Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ō', 'Ŏ', 'Ő', 'Ơ', 'Ǒ', 'Ǿ', 'Ø'),
        'OE' => array('Œ'),
        'R'  => array('Ŕ', 'Ŗ', 'Ř'),
        'S'  => array('Ś', 'Ŝ', 'Ş', 'Š', 'ſ'),
        'SS' => array('ß'),
        'T'  => array('Ţ', 'Ť', 'Ŧ'),
        'U'  => array('Ù', 'Ú', 'Û', 'Ü', 'Ũ', 'Ū', 'Ŭ', 'Ů', 'Ű', 'Ų', 'Ư', 'Ǔ', 'Ǖ', 'Ǘ', 'Ǚ', 'Ǜ'),
        'W'  => array('Ŵ'),
        'Y'  => array('Ý', 'Ŷ', 'Ÿ'),
        'Z'  => array('Ź', 'Ż', 'Ž'),
    );

    public static function getUrlString($string, $convertToLatin = true, $lowerCase = true)
    {

        global $boardurl, $scripturl, $modSettings;

        if($convertToLatin == true) {
            foreach (YAPortalSEO::$latinCharMap as $to => $from) {
                $string = preg_replace("/(" . implode('|', $from) . ")/u", $to, $string);

                foreach ($from as &$value) {
                    $value = mb_strtolower($value, 'UTF-8');
                }

                $string = preg_replace("/(" . implode('|', $from) . ")/u", strtolower($to), $string);
            }
        }

        if ($lowerCase == true) {
            $string = strtolower($string);
        }

        // replace non-latin letters to "-"
        $string = preg_replace('/[^A-Za-z0-9]+/', '-', $string);
        // remove "-" from the beginning and end
        $string = preg_replace('/(^[-]+)|([.]*[-]$)/', '', $string);

        if(!empty($string)) {
            return $string;
        }
        else {
            return false;
        }

    }

}
