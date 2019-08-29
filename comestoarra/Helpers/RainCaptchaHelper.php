<?php namespace Helpers;

/*
|--------------------------------------------------------------------------
| SMVC Helpers
|--------------------------------------------------------------------------
|
| http://simplemvcframework.com
|
*/

class RainCaptchaHelper
{

    /**
     * constant holding the API url
     */
    const HOST = 'http://raincaptcha.driversworld.us/api/v1';
    /**
     * hold the session id
     * @var string
     */
    private $sessionId;
    /**
     * when class is called sessionId is stored or sersver settings are used for reference
     * @param string $sessionId instance id
     */
    public function __construct( $sessionId = null )
    {
        if ( $sessionId === null ) :

            $this->sessionId = md5($_SERVER['SERVER_NAME'] . ':' . $_SERVER['REMOTE_ADDR']);

        else :

            $this->sessionId = $sessionId;

        endif;
    }

    public function getImage()
    {
        return self::HOST . '/image/' . $this->sessionId . '?rand' . rand( 100000, 999999 );
    }
    /**
     * compare given anser against the generated session
     * @param  string $answer
     * @return boolean
     */
    public function checkAnswer( $answer )
    {
        if ( empty ( $answer ) ) :

            return false;

        endif;

        $response = file_get_contents( self::HOST . '/check/' . $this->sessionId. '/' . $answer );

        if ( $response === false ) :

            return true;

        endif;

        return $response === 'true';
    }

}