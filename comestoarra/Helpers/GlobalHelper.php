<?php namespace Helpers;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Helpers/GlobalHelper.php
 * @package     Bootstrap Codecanyon Products
 * @company     Comestoarra Labs <labs@comestoarra.com>
 * @programmer  Rizki Wisnuaji, drg., M.Kom. <rizkiwisnuaji@comestoarra.com>
 * @copyright   2016 Comestoarra Labs. All Rights Reserved.
 * @license     http://codecanyon.net/licenses
 * @version     Release: @1.1@
 * @framework   http://slimframework.com
 *
 *
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 * This file may not be redistributed in whole or significant part.
**/

use Slim\Slim;
use SplStack;

if ( ! defined( 'PASSWORD_BCRYPT' ) ) :

    define( 'PASSWORD_BCRYPT', 1 );
    define( 'PASSWORD_DEFAULT', PASSWORD_BCRYPT );

endif;

class GlobalHelper
{

    public static function valueEncrypt( $value )
    {
        // The encodeKey MUST match the decodeKey
        $encodeKey = '051a9ff66b3c4e78f18dffa05c56a4a6bbc856ffdS+D.f^Li1KUqJ4tgX14`Jy,A9ejk?uwnXaNSD@fQ+dCZIF!';

        $encoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($encodeKey), $value, MCRYPT_MODE_CBC, md5(md5($encodeKey))));

        return($encoded);
    }

    public static function valueDecrypt( $value )
    {
        // The decodeKey MUST match the encodeKey
        $decodeKey = '051a9ff66b3c4e78f18dffa05c56a4a6bbc856ffdS+D.f^Li1KUqJ4tgX14`Jy,A9ejk?uwnXaNSD@fQ+dCZIF!';

        $decoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($decodeKey), base64_decode($value), MCRYPT_MODE_CBC, md5(md5($decodeKey))), "\0");

        return($decoded);
    }

    public static function valueSafe( $value )
    {
        return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
    }
    
    public static function parseWordMonth( $target )
    {
        switch ( $target ) :
            case "01":
                return "Jan";
            case "02":
                return "Feb";
            case "03":
                return "Mar";
            case "04":
                return "Apr";
            case "05":
                return "May";
            case "06":
                return "Jun";
            case "07":
                return "Jul";
            case "08":
                return "Aug";
            case "09":
                return "Sep";
            case "10":
                return "Oct";
            case "11":
                return "Nov";
            case "12":
                return "Dec";
            default:
                return "";
        endswitch;
    }

    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if ( preg_match ( '/linux/i', $u_agent ) ) :

            $platform = 'linux';

        elseif ( preg_match ( '/macintosh|mac os x/i', $u_agent ) ) :

            $platform = 'mac';

        elseif ( preg_match ( '/windows|win32/i', $u_agent ) ) :

            $platform = 'windows';

        endif;

        // Next get the name of the useragent yes seperately and for good reason
        if ( preg_match ( '/MSIE/i',$u_agent ) && ! preg_match ( '/Opera/i',$u_agent ) ) :

            $bname = 'Internet Explorer';
            $ub = "MSIE";

        elseif ( preg_match ( '/Edge/i',$u_agent ) ) : // Must be place before chrome !!!

            $bname = 'Microsoft Edge';
            $ub = "Edge";

        elseif ( preg_match ( '/Firefox/i',$u_agent ) ) :

            $bname = 'Mozilla Firefox';
            $ub = "Firefox";

        elseif ( preg_match ( '/Chrome/i',$u_agent ) ) :

            $bname = 'Google Chrome';
            $ub = "Chrome";

        elseif ( preg_match ( '/Safari/i',$u_agent ) ) :

            $bname = 'Apple Safari';
            $ub = "Safari";

        elseif ( preg_match ( '/Opera/i',$u_agent ) ) :

            $bname = 'Opera';
            $ub = "Opera";

        elseif ( preg_match ( '/Netscape/i',$u_agent ) ) :

            $bname = 'Netscape';
            $ub = "Netscape";

        else :

            $bname = 'Unknown Browser';
            $ub = "Unknown";

        endif;

        // finally get the correct version number
        $known = array ( 'Version', $ub, 'other' );
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        if ( ! preg_match_all ( $pattern, $u_agent, $matches ) ) :
            // we have no matching number just continue
        endif;

        // see how many we have
        $i = count ( $matches['browser'] );

        if ( $i != 1 ) :

            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if ( strripos ( $u_agent,"Version" ) < strripos ( $u_agent,$ub ) ) :

                $version= $matches['version'][0];

            else :

                $version= $matches['version'][1];

            endif;

        else :

            $version= $matches['version'][0];

        endif;

        // check if we have a number
        if ( $version == null || $version == "" ) : $version = "?"; endif;

        return array(
            'userAgent'  => $u_agent,
            'name'       => $bname,
            'version'    => $version,
            'platform'   => $platform,
            'pattern'    => $pattern
        );
    }

    public static function checkValidEmail( $email )
    {

        //from https://gist.github.com/adamloving/4401361
        $disposable_list = array('drdrb.net', 'upliftnow.com', 'uplipht.com', 'venompen.com', 'veryrealemail.com', 'viditag.com', 'viewcastmedia.com', 'viewcastmedia.net', 'viewcastmedia.org', 'gustr.com', 'webm4il.in',
            'wegwerfadresse.de', 'wegwerfemail.de', 'wetrainbayarea.com', 'wetrainbayarea.org', 'wh4f.org',
            'whyspam.me', 'willselfdestruct.com', 'winemaven.in', 'wronghead.com', 'wuzup.net',
            'wuzupmail.net', 'www.e4ward.com', 'www.gishpuppy.com', 'www.mailinator.com', 'wwwnew.eu',
            'xagloo.com', 'xemaps.com', 'xents.com', 'xmaily.com', 'xoxy.net',
            'yep.it', 'yogamaven.com', 'yopmail.fr', 'yopmail.net', 'ypmail.webarnak.fr.eu.org',
            'yuurok.com', 'zehnminutenmail.de', 'zippymail.in', 'zoaxe.com', 'zoemail.org',
            'inboxalias.com', 'koszmail.pl', 'tagyourself.com', 'whatpaas.com', 'emeil.in',
            'azmeil.tk', 'mailfa.tk', 'inbax.tk', 'emeil.ir', 'crazymailing.com',
            'mailimate.com', 'asdf.com', 'mail.com', 'me.com');

        $domain = substr(strrchr($email, "@"), 1); //extract domain name from email

        if( in_array( $domain, $disposable_list ) ) :

            return false;

        endif;

        return true;

    }

    /**
     * password class that uses a compatibility library with PHP 5.5's simplified password hashing API. (located in the vendor directory)
     * passes data to password compatibility library, this will add compatability up for php 5.5 at which point the built in functions will be used instead.
     *
     * @author David Carr - dave@simplemvcframework.com
     * @version 2.2
     * @date May 18, 2015
     */
    public static function makePassword( $password, $algo = PASSWORD_BCRYPT, array $options = array() )
    {
        return password_hash( $password, $algo, $options );
    }

    public static function verifyPassword( $password, $hash )
    {
        return password_verify( $password, $hash );
    }

    public static function convertWordToMonth( $target )
    {
        switch ( $target ) :
            case "01":
                return "Jan";
            case "02":
                return "Feb";
            case "03":
                return "Mar";
            case "04":
                return "Apr";
            case "05":
                return "May";
            case "06":
                return "Jun";
            case "07":
                return "Jul";
            case "08":
                return "Aug";
            case "09":
                return "Sep";
            case "10":
                return "Oct";
            case "11":
                return "Nov";
            case "12":
                return "Dec";
            default:
                return "";
        endswitch;
    }

    public static function regexString( $email )
    {
        $regex = "/^\S+@\S+\.\S+$/";

        if ( preg_match( $regex, $email ) ) :

            return true;

        else :

            return false;

        endif;
    }

    //GET RANDOM PASSWORD
    public static function generatePassword()
    {
        $base = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = [];
        $baseLength = strlen($base) - 1;
        for ($i = 0; $i < 16; $i++) {
            $n = rand(0, $baseLength);
            $pass[] = $base[$n];
        }
        return implode($pass);
    }

    //GET RANDOM Number
    public static function generateNumber()
    {
        $base = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = [];
        $baseLength = strlen($base) - 1;
        for ($i = 0; $i < 5; $i++) {
            $n = rand(0, $baseLength);
            $pass[] = $base[$n];
        }
        return implode($pass);
    }

    //GET RANDOM COOKIES
    public static function generateCookies()
    {
        $base = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = [];
        $baseLength = strlen($base) - 1;
        for ($i = 0; $i < 99; $i++) {
            $n = rand(0, $baseLength);
            $pass[] = $base[$n];
        }
        return implode($pass);
    }

    public static function detectCityFromIp( $ip )
    {

        $default = 'UNKNOWN';

        if ( ! is_string( $ip ) || strlen( $ip ) < 1 || $ip == '127.0.0.1' || $ip == 'localhost' || $ip == '::1' ) :

            $ip = '8.8.8.8';

        endif;

        $curlopt_useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)';

        $url = 'http://ipinfodb.com/ip_locator.php?ip=' . urlencode( $ip );
        $ch = curl_init();

        $curl_opt = array(
            CURLOPT_FOLLOWLOCATION      => 1,
            CURLOPT_HEADER              => 0,
            CURLOPT_RETURNTRANSFER      => 1,
            CURLOPT_USERAGENT           => $curlopt_useragent,
            CURLOPT_URL                 => $url,
            CURLOPT_TIMEOUT             => 1,
            CURLOPT_REFERER             => 'http://' . $_SERVER['HTTP_HOST'],
        );

        curl_setopt_array( $ch, $curl_opt );

        $content = curl_exec( $ch );

        $city = isset( $city ) ? $city : '';
        $state = isset( $state ) ? $state : '';
        $curl_info = isset( $curl_info ) ? $curl_info : '';

        if ( ! is_null( $curl_info ) ) :

            $curl_info = curl_getinfo( $ch );

        endif;

        curl_close( $ch );

        if ( preg_match( '{<li>City : ([^<]*)</li>}i', $content, $regs ) )  :

            $city = $regs[1];

        endif;

        if ( preg_match( '{<li>State/Province : ([^<]*)</li>}i', $content, $regs ) )  :

            $state = $regs[1];

        endif;

        if ( $city != '' && $state != '' ) :

            $location = $city . ', ' . $state;

            return $location;

        else :

            return $default;

        endif;

    }

    public static function getMemoryUsage( $render = false )
    {

        if ( $render != false ) :

            echo "Initial: ".memory_get_usage()." bytes \n"; //prints Initial: 361400 bytes

            // let's use up some memory
            for ( $i = 0; $i < 100000; $i++ ) :

                $array []= md5($i);

            endfor;

            // let's remove half of the array
            for ( $i = 0; $i < 100000; $i++ ) :

                if ( ! empty( $array ) ) :

                    unset( $array[$i] );

                endif;

            endfor;

            echo "Final: ".memory_get_usage()." bytes \n"; // prints  Final: 885912 bytes

            echo "Peak: ".memory_get_peak_usage()." bytes \n";
            //prints  Peak: 13687072 bytes

        endif;

        return false;

    }

    public static function valueSanitize( $string, $trim = false, $int = false, $str = false )
    {

        $string = filter_var( $string, FILTER_SANITIZE_STRING );
        $string = trim( $string );
        $string = stripslashes( $string );
        $string = strip_tags( $string );
        $string = str_replace( array( '‘', '’', '“', '”' ), array( "'", "'", '"', '"' ), $string );

        if ( $trim )
            $string = substr( $string, 0, $trim );
        if ( $int )
            $string = preg_replace( "/[^0-9\s]/", "", $string );
        if ( $str )
            $string = preg_replace( "/[^a-zA-Z\s]/", "", $string );

        return $string;

    }

    public static function valueCleanOut( $text )
    {

        $text =  strtr( $text, array( '\r\n' => "", '\r' => "", '\n' => "" ) );
        $text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
        $text = str_replace('<br>', '<br />', $text);
        return stripslashes( $text );

    }

    public static function valueCleanSanitize( $string, $trim = false,  $end_char = '&#8230;' )
    {

        $string = self::valueCleanOut( $string );
        $string = filter_var( $string, FILTER_SANITIZE_STRING );
        $string = trim( $string );
        $string = stripslashes( $string );
        $string = strip_tags( $string );
        $string = str_replace( array( '‘', '’', '“', '”' ), array( "'", "'", '"', '"' ), $string );

        if ( $trim ) :

            if ( strlen($string ) < $trim ) :

                return $string;

            endif;

            $string = preg_replace( "/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $string ) );

            if ( strlen( $string ) <= $trim ) :

                return $string;

            endif;

            $out = "";
            foreach ( explode( ' ', trim( $string ) ) as $val ) :

                $out .= $val.' ';

                if ( strlen( $out ) >= $trim ) :

                    $out = trim( $out );
                    return ( strlen( $out ) == strlen( $string ) ) ? $out : $out.$end_char;

                endif;

            endforeach;

        endif;

        return $string;

    }

    public static function valueEllipsis( $string )
    {

        $string = substr( $string, 0, strlen( $string ) - 3 );
        return trim( preg_replace( '/ .{1,3}$/', '', $string ) ) . '...';

    }

    public static function valueTruncate( $string, $length, $ellipsis = true )
    {

        $wide = strlen( preg_replace( '/[^A-Z0-9_@#%$&]/', '', $string ) );
        $length = round( $length - $wide * 0.2 );
        $clean_string = preg_replace( '/&[^;]+;/', '-', $string );
        if ( strlen( $clean_string ) <= $length ) :

            return $string;

        endif;

        $difference = $length - strlen( $clean_string );
        $result = substr( $string, 0, $difference );

        if ( $result != $string and $ellipsis ) :

            $result = self::valueEllipsis( $result );

        endif;

        return $result;

    }

    public static function phpSelf()
    {

        return htmlspecialchars( $_SERVER['PHP_SELF'] );

    }

    public static function getSize( $size, $precision = 2, $long_name = false, $real_size = true )
    {

        if ( $size == 0 ) :

            return '-/-';

        else :

            $base = $real_size ? 1024 : 1000;
            $pos = 0;

            while ( $size > $base ) :

                $size /= $base;
                $pos++;

            endwhile;

            $prefix = self::_getSizePrefix( $pos );

            $size_name = $long_name ? $prefix . "bytes" : $prefix[0] . 'B';

            return round( $size, $precision ) . ' ' . ucfirst( $size_name );

        endif;

    }

    public static function compareFloatNumbers( $float1, $float2, $operator='=' )
    {
        // Check numbers to 5 digits of precision
        $epsilon = 0.00001;

        $float1 = (float)$float1;
        $float2 = (float)$float2;

        switch ( $operator )
        {
            // equal
            case "=":
            case "eq":
                if ( abs( $float1 - $float2 ) < $epsilon ) :

                    return true;

                endif;

                break;
            // less than
            case "<":
            case "lt":
                if ( abs( $float1 - $float2 ) < $epsilon ) :

                    return false;

                else :

                    if ( $float1 < $float2 ) :

                        return true;

                    endif;

                endif;

                break;
            // less than or equal
            case "<=":
            case "lte":
                if ( self::compareFloatNumbers( $float1, $float2, '<' ) || self::compareFloatNumbers( $float1, $float2, '=' ) ) :

                    return true;

                endif;

                break;
            // greater than
            case ">":
            case "gt":
                if ( abs( $float1 - $float2 ) < $epsilon ) :

                    return false;

                else :

                    if ( $float1 > $float2 ) :

                        return true;

                    endif;

                endif;

                break;
            // greater than or equal
            case ">=":
            case "gte":
                if ( self::compareFloatNumbers( $float1, $float2, '>' ) || self::compareFloatNumbers( $float1, $float2, '=' ) ) :

                    return true;

                endif;

                break;

            case "<>":
            case "!=":
            case "ne":
                if ( abs( $float1 - $float2 ) > $epsilon ) :

                    return true;

                endif;

                break;
            default:
                die( "Unknown operator '".$operator."' in compareFloatNumbers()" );
        }

        return false;

    }

    public static function numberToWords( $number )
    {

        $words = array(
            'zero',
            'one',
            'two',
            'three',
            'four',
            'five',
            'six',
            'seven',
            'eight',
            'nine',
            'ten',
            'eleven',
            'twelve',
            'thirteen',
            'fourteen',
            'fifteen',
            'sixteen',
            'seventeen',
            'eighteen',
            'nineteen',
            'twenty',
            30 => 'thirty',
            40 => 'fourty',
            50 => 'fifty',
            60 => 'sixty',
            70 => 'seventy',
            80 => 'eighty',
            90 => 'ninety',
            100 => 'hundred',
            1000 => 'thousand');

        $number_in_words = '';

        if ( is_numeric( $number ) ) :

            $number = (int)round( $number );

            if ( $number < 0 ) :

                $number = -$number;
                $number_in_words = 'minus ';

            endif;

            if ( $number > 1000 ) :

                $number_in_words = $number_in_words . self::numberToWords( floor( $number / 1000 ) ) . " " . $words[1000];

                $hundreds = $number % 1000;

                $tens = $hundreds % 100;

                if ( $hundreds > 100 ) :

                    $number_in_words = $number_in_words . ", " . self::numberToWords( $hundreds );

                elseif ( $tens ) :

                    $number_in_words = $number_in_words . " and " . self::numberToWords( $tens );

                endif;

            elseif ( $number > 100 ) :

                $number_in_words = $number_in_words . self::numberToWords( floor( $number / 100 ) ) . " " . $words[100];

                $tens = $number % 100;

                if ( $tens ) :

                    $number_in_words = $number_in_words . " and " . self::numberToWords( $tens );

                endif;

            elseif ( $number > 20 ) :

                $number_in_words = $number_in_words . " " . $words[ 10 * floor( $number / 10 ) ];

                $units = $number % 10;

                if ( $units ) :

                    $number_in_words = $number_in_words . self::numberToWords($units);

                endif;

            else :

                $number_in_words = $number_in_words . " " . $words[$number];

            endif;

            return $number_in_words;

        endif;

        return false;

    }

    public static function convertWordsToNumber( $data )
    {

        $data = strtr(
            $data,
            array(
                'zero'      => '0',
                'a'         => '1',
                'one'       => '1',
                'two'       => '2',
                'three'     => '3',
                'four'      => '4',
                'five'      => '5',
                'six'       => '6',
                'seven'     => '7',
                'eight'     => '8',
                'nine'      => '9',
                'ten'       => '10',
                'eleven'    => '11',
                'twelve'    => '12',
                'thirteen'  => '13',
                'fourteen'  => '14',
                'fifteen'   => '15',
                'sixteen'   => '16',
                'seventeen' => '17',
                'eighteen'  => '18',
                'nineteen'  => '19',
                'twenty'    => '20',
                'thirty'    => '30',
                'forty'     => '40',
                'fourty'    => '40',
                'fifty'     => '50',
                'sixty'     => '60',
                'seventy'   => '70',
                'eighty'    => '80',
                'ninety'    => '90',
                'hundred'   => '100',
                'thousand'  => '1000',
                'million'   => '1000000',
                'billion'   => '1000000000',
                'and'       => '',
            )
        );

        $parts = array_map(
            function ( $val )
            {
                return floatval( $val );
            },
            preg_split( '/[\s-]+/', $data )
        );

        $stack = new SplStack;
        $sum   = 0;
        $last  = null;

        foreach ( $parts as $part ) :

            if ( ! $stack->isEmpty() ) :

                if ( $stack->top() > $part ) :

                    if ( $last >= 1000 ) :

                        $sum += $stack->pop();
                        $stack->push($part);

                    else :

                        $stack->push($stack->pop() + $part);

                    endif;

                else :

                    $stack->push( $stack->pop() * $part );

                endif;

            else :

                $stack->push( $part );

            endif;

            $last = $part;

        endforeach;

        return $sum + $stack->pop();

    }

    public static function getTimeSince( $original )
    {

        // array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365, 'year'),
            array(60 * 60 * 24 * 30, 'month'),
            array(60 * 60 * 24 * 7, 'week'),
            array(60 * 60 * 24, 'day'),
            array(60 * 60, 'hour'),
            array(60, 'min'),
            array(1, 'sec'),
        );

        $today = time();
        /* Current unix time  */
        $since = $today - $original;

        // $j saves performing the count function each time around the loop
        for ( $i = 0, $j = count( $chunks ); $i < $j; $i++ ) :

            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];

            // finding the biggest chunk (if the chunk fits, break)
            if ( ( $count = floor( $since / $seconds ) ) != 0 ) :

                break;

            endif;

        endfor;

        if ( isset( $count ) ) :

            if (isset( $name ) ) :

                $print = ( $count == 1 ) ? '1 ' . $name : "$count {$name}s";

            endif;

        endif;

        if ( $i + 1 < $j ) :

            // now getting the second item
            $seconds2 = $chunks[$i + 1][0];
            $name2 = $chunks[$i + 1][1];

            // add second item if its greater than 0
            if ( isset( $seconds ) ) :

                if ( isset( $count ) ) :

                    if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) :

                        if ( isset( $print ) ) :

                            $print .= ( $count2 == 1 ) ? ', 1 ' . $name2 : " $count2 {$name2}s";

                        endif;

                    endif;

                endif;

            endif;

        endif;

        return isset( $print ) ? $print : '' . ' ago';

    }

    public static function getRandName( $char = 6 )
    {

        $code = '';

        for( $x = 0; $x < $char; $x++ ) :

            $code .= '-'.substr( strtoupper( sha1( rand( 0, 999999999999999 ) ) ), 2, $char );

        endfor;

        $code = substr( $code, 1 );

        return $code;

    }

    public static function grabRemotePic( $new_file_name, $local_dir_path, $remote_picture_url )
    {

        if( ! is_dir( $local_dir_path ) ) : //create new dir if doesn't exist

            mkdir( $local_dir_path );

        endif;

        $local_file_path = $local_dir_path .'/'.$new_file_name;

        if( copy( $remote_picture_url, $local_file_path ) ) :

            return true;
            //usage grab_remote_pic('new_file_name.gif', 'home/path/to/local/images/', 'http://'

        endif;

    }
    
    public static function checkDisposableEmail( $email )
    {

        //from https://gist.github.com/adamloving/4401361
        $disposable_list = array('drdrb.net', 'upliftnow.com', 'uplipht.com', 'venompen.com', 'veryrealemail.com', 'viditag.com', 'viewcastmedia.com', 'viewcastmedia.net', 'viewcastmedia.org', 'gustr.com', 'webm4il.in',
            'wegwerfadresse.de', 'wegwerfemail.de', 'wetrainbayarea.com', 'wetrainbayarea.org', 'wh4f.org',
            'whyspam.me', 'willselfdestruct.com', 'winemaven.in', 'wronghead.com', 'wuzup.net',
            'wuzupmail.net', 'www.e4ward.com', 'www.gishpuppy.com', 'www.mailinator.com', 'wwwnew.eu',
            'xagloo.com', 'xemaps.com', 'xents.com', 'xmaily.com', 'xoxy.net',
            'yep.it', 'yogamaven.com', 'yopmail.fr', 'yopmail.net', 'ypmail.webarnak.fr.eu.org',
            'yuurok.com', 'zehnminutenmail.de', 'zippymail.in', 'zoaxe.com', 'zoemail.org',
            'inboxalias.com', 'koszmail.pl', 'tagyourself.com', 'whatpaas.com', 'emeil.in',
            'azmeil.tk', 'mailfa.tk', 'inbax.tk', 'emeil.ir', 'crazymailing.com',
            'mailimate.com', 'asdf.com');

        $domain = substr(strrchr($email, "@"), 1); //extract domain name from email

        if( in_array( $domain, $disposable_list ) ) :

            echo "Disposable email not allowed";

        else :

            echo "You are good to go";

        endif;

    }

    public static function valueLineBreaks($text)
    {
        return strtr( $text, array( "\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />' ) );
    }

    public static function getFileName( $_path, $_filename )
    {
        $filename = preg_replace('/[^a-zA-Z0-9\s\-\.\_]/', ' ', $_filename);
        $filename = preg_replace('/(\s\s)+/', ' ', $filename);
        $filename = trim($filename);
        $filename = preg_replace('/\s+/', '-', $filename);
        $filename = preg_replace('/\-+/', '-', $filename);

        if (strlen($filename) == 0) $filename = "file";
        else if ($filename[0] == ".") $filename = "file".$filename;
        while (file_exists($_path.$filename)) {
            $pos = strrpos($filename, ".");
            if ($pos !== false) {
                $ext = substr($filename, $pos);
                $filename = substr($filename, 0, $pos);
            } else {
                $ext = "";
            }
            $pos = strrpos($filename, "-");
            if ($pos !== false) {
                $suffix = substr($filename, $pos+1);
                if (is_numeric($suffix)) {
                    $suffix++;
                    $filename = substr($filename, 0, $pos)."-".$suffix.$ext;
                } else {
                    $filename = $filename."-1".$ext;
                }
            } else {
                $filename = $filename."-1".$ext;
            }
        }
        return $filename;
    }
}