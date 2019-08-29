<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\Huge\Core\HugeConfig;
use Helpers\LanguageHelper;
use Helpers\MailHelper;
use Helpers\RainCaptchaHelper;
use Models\Entity\Huge\User;
use Slim\Slim;

class PasswordResetModel
{

    public static function requestPasswordReset( $captcha, $user_name_or_email )
    {

        $raincaptcha = new RainCaptchaHelper();

        if ( ! $raincaptcha->checkAnswer( $captcha ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_CAPTCHA_WRONG' ) );
            return false;

        endif;

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( empty ( $user_name_or_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_EMAIL_FIELD_EMPTY' ) );
            return false;

        endif;

        // check if that username exists
        $result = UserModel::getUserDataByUserNameOrEmail ( $user_name_or_email );

        if ( ! $result ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_DOES_NOT_EXIST' ) );
            return false;

        endif;

        // generate integer-timestamp (to see when exactly the user (or an attacker) requested the password reset mail)
        // generate random hash for email password reset verification (40 char string)
        $temporary_timestamp = time();
        $user_password_reset_hash = sha1 ( uniqid ( mt_rand(), true ) );

        // set token (= a random hash string and a timestamp) into database ...
        $token_set = self::setPasswordResetDatabaseToken ( $result->user_name, $user_password_reset_hash, $temporary_timestamp );

        if ( ! $token_set ) :

            return false;

        endif;

        // ... and send a mail to the user, containing a link with username and token hash string
        $mail_sent = self::sendPasswordResetMail ( $result->user_name, $user_password_reset_hash, $result->user_email );

        if ( $mail_sent ) :

            return true;

        endif;

        // default return
        return false;
    }

    public static function setPasswordResetDatabaseToken( $user_name, $user_password_reset_hash, $temporary_timestamp )
    {
        $where      = ['user_name' => $user_name, 'user_provider_type' => 'DEFAULT'];

        $query = User::where( $where )->update( array(
            'user_password_reset_hash' => $user_password_reset_hash,
            'user_password_reset_timestamp'   => $temporary_timestamp
        ));

        // check if exactly one row was successfully changed
        if ( count( $query ) == 1 ) :

            return true;

        endif;

        // fallback
        Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_RESET_TOKEN_FAIL' ) );
        return false;
    }

    public static function sendPasswordResetMail( $user_name, $user_password_reset_hash, $user_email )
    {
        // create email body
        $body = HugeConfig::get( 'EMAIL_PASSWORD_RESET_CONTENT' ) . ' ' . BASE_DIR .
            HugeConfig::get( 'EMAIL_PASSWORD_RESET_URL' ) . '/' . urlencode ( $user_name ) . '/' . urlencode ( $user_password_reset_hash );

        // create instance of Mail class, try sending and check
        $mail = new MailHelper();
        $mail_sent = $mail->sendMail ( $user_email, HugeConfig::get( 'EMAIL_PASSWORD_RESET_FROM_EMAIL' ),
            HugeConfig::get( 'EMAIL_PASSWORD_RESET_FROM_NAME' ), HugeConfig::get( 'EMAIL_PASSWORD_RESET_SUBJECT' ), $body
        );

        if ( $mail_sent ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_PASSWORD_RESET_MAIL_SENDING_SUCCESSFUL' ) );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_RESET_MAIL_SENDING_ERROR' ) . $mail->getError() );
        return false;
    }

    public static function verifyPasswordReset( $user_name, $verification_code )
    {
        $where      = ['user_name' => $user_name,  'user_password_reset_hash' => $verification_code ,'user_provider_type' => 'DEFAULT'];

        $query = User::where( $where )->take( 1 )->select( 'user_id', 'user_password_reset_timestamp' )->first();

        // if this user with exactly this verification hash code does NOT exist
        if ( count( $query ) != 1) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_RESET_COMBINATION_DOES_NOT_EXIST' ) );
            return false;

        endif;

        // 3600 seconds are 1 hour
        $timestamp_one_hour_ago = time() - 3600;

        // if password reset request was sent within the last hour (this timeout is for security reasons)
        if ( $query->user_password_reset_timestamp > $timestamp_one_hour_ago ) :
            // verification was successful
            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_PASSWORD_RESET_LINK_VALID' ) );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_RESET_LINK_EXPIRED' ) );
            return false;

        endif;
    }

    public static function saveNewUserPassword( $user_name, $user_password_hash, $user_password_reset_hash )
    {
        $where      = ['user_name' => $user_name, 'user_password_reset_hash' => $user_password_reset_hash ,'user_provider_type' => 'DEFAULT'];

        $query = User::where( $where )->take(1)->update( array(
            'user_password_hash' => $user_password_hash,
            'user_password_reset_hash'   => NULL,
            'user_password_reset_timestamp'   => NULL
        ));

        // if one result exists, return true, else false. Could be written even shorter btw.
        return ( count( $query ) == 1 ? true : false );
    }

    public static function setNewPassword( $captcha, $user_name, $user_password_reset_hash, $user_password_new, $user_password_repeat )
    {
        $raincaptcha = new RainCaptchaHelper();

        if ( ! $raincaptcha->checkAnswer( $captcha ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_CAPTCHA_WRONG') );
            return false;

        endif;

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // validate the password
        if ( ! self::validateNewPassword ( $user_name, $user_password_reset_hash, $user_password_new, $user_password_repeat ) ) :
            
            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_FAILED') );
            return false;

        endif;

        // crypt the password (with the PHP 5.5+'s password_hash() function, result is a 60 character hash string)
        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        // write the password to database (as hashed and salted string), reset user_password_reset_hash
        if ( self::saveNewUserPassword ( $user_name, $user_password_hash, $user_password_reset_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL') );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_FAILED') );
            return false;

        endif;

    }

    public static function validateNewPassword( $user_name, $user_password_reset_hash, $user_password_new, $user_password_repeat )
    {
        if ( empty ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_USERNAME_FIELD_EMPTY') );
            return false;

        elseif ( empty ( $user_password_reset_hash ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_RESET_TOKEN_MISSING') );
            return false;

        elseif ( empty ( $user_password_new ) || empty ( $user_password_repeat ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_FIELD_EMPTY') );
            return false;

        elseif ( $user_password_new !== $user_password_repeat ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_REPEAT_WRONG') );
            return false;

        elseif ( strlen( $user_password_new ) < 6 ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_TOO_SHORT') );
            return false;

        endif;

        return true;
    }

    /**
     * Writes the new password to the database
     *
     * @param string $user_name
     * @param string $user_password_hash
     *
     * @return bool
     */
    public static function saveChangedPassword( $user_name, $user_password_hash )
    {
        
        $where      = ['user_name' => $user_name, 'user_provider_type' => 'DEFAULT'];

        $query = User::where( $where )->take( 1 )->update( array(
            'user_password_hash' => $user_password_hash
        ));

        // if one result exists, return true, else false. Could be written even shorter btw.
        return ( count( $query ) == 1 ? true : false );
    }


    /**
     * Validates fields, hashes new password, saves new password
     *
     * @param string $user_name
     * @param string $user_password_current
     * @param string $user_password_new
     * @param string $user_password_repeat
     *
     * @return bool
     */
    public static function changePassword($user_name, $user_password_current, $user_password_new, $user_password_repeat)
    {

        // validate the passwords
        if ( ! self::validatePasswordChange( $user_name, $user_password_current, $user_password_new, $user_password_repeat ) ) :

            return false;

        endif;

        // crypt the password (with the PHP 5.5+'s password_hash() function, result is a 60 character hash string)
        $user_password_hash = password_hash( $user_password_new, PASSWORD_DEFAULT );

        // write the password to database (as hashed and salted string)
        if ( self::saveChangedPassword( $user_name, $user_password_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL' ) );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_CHANGE_FAILED' ) );
            return false;

        endif;

    }


    /**
     * Validates current and new passwords
     *
     * @param string $user_name
     * @param string $user_password_current
     * @param string $user_password_new
     * @param string $user_password_repeat
     *
     * @return bool
     */
    public static function validatePasswordChange( $user_name, $user_password_current, $user_password_new, $user_password_repeat )
    {
        
        $user = User::where( 'user_name', '=', $user_name )->take( 1 )->select( 'user_password_hash', 'user_failed_logins' )->first();

        if ( count( $user ) == 1 ) :

            $user_password_hash = $user->user_password_hash;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_DOES_NOT_EXIST' ) );
            return false;

        endif;

        if ( ! password_verify( $user_password_current, $user_password_hash ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_CURRENT_INCORRECT' ) );
            return false;

        elseif ( empty ( $user_password_new ) || empty ( $user_password_repeat ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_FIELD_EMPTY' ) );
            return false;
        
        elseif ( $user_password_new !== $user_password_repeat ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_REPEAT_WRONG' ) );
            return false;

        elseif ( strlen ( $user_password_new ) < 6 ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_TOO_SHORT' ) );
            return false;

        elseif ( $user_password_current == $user_password_new ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_PASSWORD_NEW_SAME_AS_CURRENT' ) );
            return false;

        endif;

        return true;
    }

}
