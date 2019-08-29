<?php

/**
 * Configuration for DEVELOPMENT environment
 * To create another configuration set just copy this file to config.production.php etc. You get the idea :)
 */

/**
 * Configuration for: Error reporting
 * Useful to show every little problem during development, but only show hard / no errors in production.
 * It's a little bit dirty to put this here, but who cares. For development purposes it's totally okay.
 */
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

/**
 * Returns the full configuration.
 * This is used by the core/Config class.
 */
return [

    /**
     * Configuration for: Base URL
     * This detects your URL/IP incl. sub-folder automatically. You can also deactivate auto-detection and provide the
     * URL manually. This should then look like 'http://192.168.33.44/' ! Note the slash in the end.
     */
//    'URL' => 'http://' . $_SERVER['HTTP_HOST'] . str_replace('public', '', dirname($_SERVER['SCRIPT_NAME'])),
    /**
     * Configuration for: Avatar paths
     * Internal path to save avatars. Make sure this folder is writable. The slash at the end is VERY important!
     */
    'PATH_AVATARS'                  => HUGE_PATH_AVATARS,
    'PATH_AVATARS_PUBLIC'           => HUGE_PATH_AVATARS_PUBLIC,

    /**
     * Configuration for: Captcha size
     * The currently used Captcha generator (https://github.com/Gregwar/Captcha) also runs without giving a size,
     * so feel free to use ->build(); inside CaptchaModel.
     */
    'CAPTCHA_WIDTH'                 => HUGE_CAPTCHA_WIDTH,
    'CAPTCHA_HEIGHT'                => HUGE_CAPTCHA_HEIGHT,
    /**
     * Configuration for: Cookies
     * 1209600 seconds = 2 weeks
     * COOKIE_PATH is the path the cookie is valid on, usually "/" to make it valid on the whole domain.
     * @see http://stackoverflow.com/q/9618217/1114320
     * @see php.net/manual/en/function.setcookie.php
     */
    'COOKIE_RUNTIME'                => HUGE_COOKIE_RUNTIME,
    'COOKIE_PATH'                   => HUGE_COOKIE_PATH,
    'COOKIE_DOMAIN'                 => HUGE_COOKIE_DOMAIN,
    'COOKIE_SECURE'                 => HUGE_COOKIE_SECURE,
    'COOKIE_HTTP'                   => HUGE_COOKIE_HTTP,
    'SESSION_RUNTIME'               => HUGE_SESSION_RUNTIME,

    'ENCRYPTION_KEY'                => HUGE_ENCRYPTION_KEY,
    'HMAC_SALT'                     => HUGE_HMAC_SALT,
    /**
     * Configuration for: Avatars/Gravatar support
     * Set to true if you want to use "Gravatar(s)", a service that automatically gets avatars pictures via using email
     * addresses of users by requesting images from the gravatar.com API. Set to false to use own locally saved avatars.
     * AVATAR_SIZE set the pixel size of avatars/gravatars (will be 44x44 by default). Avatars are always squares.
     * AVATAR_DEFAULT_IMAGE is the default image in public/avatars/
     */
    'USE_GRAVATAR'                      => HUGE_USE_GRAVATAR,
    'GRAVATAR_DEFAULT_IMAGESET'         => HUGE_GRAVATAR_DEFAULT_IMAGESET,
    'GRAVATAR_RATING'                   => HUGE_GRAVATAR_RATING,
    'AVATAR_SIZE'                       => HUGE_AVATAR_SIZE,
    'AVATAR_JPEG_QUALITY'               => HUGE_AVATAR_JPEG_QUALITY,
    'AVATAR_DEFAULT_IMAGE'              => HUGE_AVATAR_DEFAULT_IMAGE,
    /**
     * Configuration for: Email server credentials
     *
     * Here you can define how you want to send emails.
     * If you have successfully set up a mail server on your linux server and you know
     * what you do, then you can skip this section. Otherwise please set EMAIL_USE_SMTP to true
     * and fill in your SMTP provider account data.
     *
     * EMAIL_USED_MAILER: Check Mail class for alternatives
     * EMAIL_USE_SMTP: Use SMTP or not
     * EMAIL_SMTP_AUTH: leave this true unless your SMTP service does not need authentication
     */
    'EMAIL_USED_MAILER'                 => HUGE_EMAIL_USED_MAILER,
    'EMAIL_USE_SMTP'                    => HUGE_EMAIL_USE_SMTP,
    'EMAIL_SMTP_HOST'                   => HUGE_EMAIL_SMTP_HOST,
    'EMAIL_SMTP_AUTH'                   => HUGE_EMAIL_SMTP_AUTH,
    'EMAIL_SMTP_USERNAME'               => HUGE_EMAIL_SMTP_USERNAME,
    'EMAIL_SMTP_PASSWORD'               => HUGE_EMAIL_SMTP_PASSWORD,
    'EMAIL_SMTP_PORT'                   => HUGE_EMAIL_SMTP_PORT,
    'EMAIL_SMTP_ENCRYPTION'             => HUGE_EMAIL_SMTP_ENCRYPTION,
    /**
     * Configuration for: Email content data
     */
    'EMAIL_PASSWORD_RESET_URL'          => HUGE_EMAIL_PASSWORD_RESET_URL,
    'EMAIL_PASSWORD_RESET_FROM_EMAIL'   => HUGE_EMAIL_PASSWORD_RESET_FROM_EMAIL,
    'EMAIL_PASSWORD_RESET_FROM_NAME'    => HUGE_EMAIL_PASSWORD_RESET_FROM_NAME,
    'EMAIL_PASSWORD_RESET_SUBJECT'      => HUGE_EMAIL_PASSWORD_RESET_SUBJECT,
    'EMAIL_PASSWORD_RESET_CONTENT'      => HUGE_EMAIL_PASSWORD_RESET_CONTENT,
    'EMAIL_VERIFICATION_URL'            => HUGE_EMAIL_VERIFICATION_URL,
    'EMAIL_VERIFICATION_FROM_EMAIL'     => HUGE_EMAIL_VERIFICATION_FROM_EMAIL,
    'EMAIL_VERIFICATION_FROM_NAME'      => HUGE_EMAIL_VERIFICATION_FROM_NAME,
    'EMAIL_VERIFICATION_SUBJECT'        => HUGE_EMAIL_VERIFICATION_SUBJECT,
    'EMAIL_VERIFICATION_CONTENT'        => HUGE_EMAIL_VERIFICATION_CONTENT,
];
