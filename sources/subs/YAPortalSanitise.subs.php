<?php
/**
 * @package "YAPortal" Addon for Elkarte
 * @author tinoest
 * @license BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * @version 1.0.0
 *
 */
class YAPortalSanitise {

    // Based on https://github.com/symphonycms/xssfilter/blob/master/extension.driver.php    
    public static function xssClean($string, $stripWhiteSpace = false ) {

        // URL decode
        $string = urldecode($string);
        // Convert Hexadecimals
        $string = preg_replace_callback('!(&#|\\\)[xX]([0-9a-fA-F]+);?!', function($m) {
            return chr(hexdec($m[2]));
        }, $string);
        // Clean up entities
        $string = preg_replace('!(&#0+[0-9]+)!','$1;',$string);
        // Decode entities
        $string = html_entity_decode($string, ENT_NOQUOTES, 'UTF-8');
        // Strip whitespace characters
        if($stripWhiteSpace) {
            $string = preg_replace('!\s!','',$string);
        }
        // Set the patterns we'll test against
        $patterns = array(
            // Match any attribute starting with "on" or xmlns
            '#(<[^>]+[\x00-\x20\"\'\/])(on|xmlns)[^>]*>?#iUu',
            // Match javascript:, livescript:, vbscript: and mocha: protocols
            '!((java|live|vb)script|mocha|feed|data):(\w)*!iUu',
            '#-moz-binding[\x00-\x20]*:#u',
            // Match style attributes
            '#(<[^>]+[\x00-\x20\"\'\/])style=[^>]*>?#iUu',
            // Match unneeded tags
            '#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>?#i'
        );
        foreach($patterns as $pattern) {
            $string = preg_replace($pattern, '', $string);
        }

        if(!empty($string)) {
            return $string;
        }
        else {
            return false;
        }
    }


    public static function filter($key, $type = 'get', $filterType = 'string', $options = array()) {
        
        switch($type) {
            case 'get':
                $data = $_GET;        
                break;
            case 'post':
                $data = $_POST;        
                break;
            default:
                return false;
                break;
        }

        if(!array_key_exists($key, $data)) {
            return false;
        }

        return filter_var($data[$key], self::filterType($filterType), $options);
    }

    private static function filterType($type) {
        switch (strtolower($type)) {
            case 'string':
                $filter = FILTER_SANITIZE_STRING;
                break;
            case 'int':
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'float' || 'decimal':
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                break;
            case 'encoded':
                $filter = FILTER_SANITIZE_ENCODED;
                break;
            case 'url':
                $filter = FILTER_SANITIZE_URL;
                break;
            case 'email':
                $filter = FILTER_SANITIZE_EMAIL;
                break;
            default:
                $filter = FILTER_SANITIZE_STRING;
        }
        return $filter;
    }
}
