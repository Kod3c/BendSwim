<?php namespace Helpers\Huge\Core;

use Helpers\CsrfHelper;
use Models\Entity\Huge\User;
use Slim\Slim;

class HugeAvatar
{
    /**
     * Gets a gravatar image link from given email address
     *
     * Gravatar is the #1 (free) provider for email address based global avatar hosting.
     * The URL (or image) returns always a .jpg file ! For deeper info on the different parameter possibilities:
     * @see http://gravatar.com/site/implement/images/
     * @source http://gravatar.com/site/implement/images/php/
     *
     * This method will return something like http://www.gravatar.com/avatar/79e2e5b48aec07710c08d50?s=80&d=mm&r=g
     * Note: the url does NOT have something like ".jpg" ! It works without.
     *
     * Set the configs inside the application/config/ files.
     *
     * @param string $email The email address
     * @return string
     */
    public static function getGravatarLinkByEmail( $email )
    {
        return 'http://www.gravatar.com/avatar/' .
        md5( strtolower( trim( $email ) ) ) .
        '?s=' . HugeConfig::get('AVATAR_SIZE') . '&d=' . HugeConfig::get('GRAVATAR_DEFAULT_IMAGESET') . '&r=' . HugeConfig::get('GRAVATAR_RATING');
    }

    /**
     * Gets the user's avatar file path
     * @param int $user_has_avatar Marker from database
     * @param int $user_id User's id
     * @return string Avatar file path
     */
    public static function getPublicAvatarFilePathOfUser( $user_has_avatar, $user_id )
    {
        if ( $user_has_avatar ) :

            return HugeConfig::get('PATH_AVATARS_PUBLIC') . $user_id . '.jpg';

        endif;

        return HugeConfig::get('PATH_AVATARS_PUBLIC') . HugeConfig::get('AVATAR_DEFAULT_IMAGE');
    }

    /**
     * Gets the user's avatar file path
     * @param $user_id integer The user's id
     * @return string avatar picture path
     */
    public static function getPublicUserAvatarFilePathByUserId( $user_id )
    {
        $query = User::where( 'user_id', '=', $user_id )->take( 1 )->select( 'user_has_avatar' )->first();

        if ( $query->user_has_avatar == 1 ) :

            return  HugeConfig::get('PATH_AVATARS_PUBLIC') . $user_id . '.jpg';

        endif;

        return  HugeConfig::get('PATH_AVATARS_PUBLIC') . HugeConfig::get('AVATAR_DEFAULT_IMAGE');
    }

    /**
     * Create an avatar picture (and checks all necessary things too)
     * TODO decouple
     * TODO total rebuild
     */
    public static function createAvatar()
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // check avatar folder writing rights, check if upload fits all rules
        if ( self::isAvatarFolderWritable() AND self::validateImageFile() ) :

            // create a jpg file in the avatar folder, write marker to database
            $target_file_path = DIR_AVATARS . HugeSession::get('user_id');


            self::resizeAvatarImage( $_FILES['avatar_file']['tmp_name'], $target_file_path, HugeConfig::get('AVATAR_SIZE'), HugeConfig::get('AVATAR_SIZE'), HugeConfig::get('AVATAR_JPEG_QUALITY') );

            self::writeAvatarToDatabase( HugeSession::get('user_id') );

            HugeSession::set('user_avatar_file', self::getPublicUserAvatarFilePathByUserId( HugeSession::get('user_id') ));

            Slim::getInstance()->flash( 'success', HugeText::get( 'FEEDBACK_AVATAR_UPLOAD_SUCCESSFUL' ) );

            return true;

        endif;

        return false;
    }

    /**
     * Checks if the avatar folder exists and is writable
     *
     * @return bool success status
     */
    public static function isAvatarFolderWritable()
    {
        if ( is_dir ( DIR_AVATARS ) AND is_writable ( DIR_AVATARS ) ) :

            return true;

        endif;

        Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_FOLDER_DOES_NOT_EXIST_OR_NOT_WRITABLE' ) );
        return false;
    }

    /**
     * Validates the image
     * Only accepts gif, jpg, png types
     * @see http://php.net/manual/en/function.image-type-to-mime-type.php
     *
     * @return bool
     */
    public static function validateImageFile()
    {
        if ( ! isset( $_FILES['avatar_file'] ) ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_IMAGE_UPLOAD_FAILED' ) );
            return false;

        endif;

        // if input file too big (>5MB)
        if ( $_FILES['avatar_file']['size'] > HUGE_AVATAR_MAX_SIZE ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_UPLOAD_TOO_BIG' ) );
            return false;

        endif;

        // get the image width, height and mime type
        $image_proportions = getimagesize ( $_FILES['avatar_file']['tmp_name'] );

        // if input file too small, [0] is the width, [1] is the height
        if ( $image_proportions[0] < HugeConfig::get('AVATAR_SIZE') OR $image_proportions[1] < HugeConfig::get('AVATAR_SIZE') ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_UPLOAD_TOO_SMALL' ) );
            return false;

        endif;

        // if file type is not jpg, gif or png
        if ( ! in_array ( $image_proportions['mime'], array('image/jpeg', 'image/gif', 'image/png') ) ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_UPLOAD_WRONG_TYPE' ) );
            return false;

        endif;

        return true;
    }

    /**
     * Writes marker to database, saying user has an avatar now
     *
     * @param $user_id
     */
    public static function writeAvatarToDatabase( $user_id )
    {
//        User::where( 'user_id', '=', $user_id )->update( array( 'user_has_avatar' => TRUE ) )->first();
        User::where( 'user_id', '=', $user_id )->update( array( 'user_has_avatar' => TRUE ) );

    }

    /**
     * Resize avatar image (while keeping aspect ratio and cropping it off in a clean way).
     * Only works with gif, jpg and png file types. If you want to change this also have a look into
     * method validateImageFile() inside this model.
     *
     * TROUBLESHOOTING: You don't see the new image ? Press F5 or CTRL-F5 to refresh browser cache.
     *
     * @param string $source_image The location to the original raw image.
     * @param string $destination The location to save the new image.
     * @param int $final_width The desired width of the new image
     * @param int $final_height The desired height of the new image.
     * @param int $quality The quality of the JPG to produce 1 - 100
     *
     * @return bool success state
     */
    public static function resizeAvatarImage( $source_image, $destination, $final_width = 300, $final_height = 300, $quality = 100 )
    {
        // fetch the image's meta data
        // @see php.net/manual/en/function.getimagesize.php
        $imageData = getimagesize ( $source_image );
        $width = $imageData[0];
        $height = $imageData[1];
        $mimeType = $imageData['mime'];

        if ( ! $width || ! $height ) :

            return false;

        endif;

        //saving the image into memory (for manipulation with GD Library)
        if ( $mimeType == 'image/jpeg' ) :

            $myImage = imagecreatefromjpeg ( $source_image );

        elseif ( $mimeType == 'image/png' ) :

            $myImage = imagecreatefrompng ( $source_image );

        elseif ( $mimeType == 'image/gif' ) :

            $myImage = imagecreatefromgif ( $source_image );

        else :

            return false;

        endif;

        // calculating the part of the image to use for thumbnail
        if ( $width > $height ) :

            $y              = 0;
            $x              = ( $width - $height)  / 2;
            $smallestSide   = $height;

        else :

            $x              = 0;
            $y              = ( $height - $width ) / 2;
            $smallestSide   = $width;

        endif;

        // copying the part into thumbnail, maybe edit this for square avatars
        $thumb = imagecreatetruecolor ( $final_width, $final_height );
        imagecopyresampled ( $thumb, $myImage, 0, 0, $x, $y, $final_width, $final_height, $smallestSide, $smallestSide );

        // add '.jpg' to file path, save it as a .jpg file with our $destination_filename parameter
        $destination .= '.jpg';
        imagejpeg ( $thumb, $destination, $quality );

        // delete "working copy"
        imagedestroy ( $thumb );

        if ( file_exists ( $destination ) ) :

            return true;

        endif;
        // default return
        return false;
    }

    /**
     * Delete a user's avatar
     *
     * @param int $userId
     * @return bool success
     */
    public static function deleteAvatar( $userId )
    {
        if ( ! ctype_digit ( $userId ) ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_IMAGE_DELETE_FAILED' ) );
            return false;

        endif;

        // try to delete image, but still go on regardless of file deletion result
        self::deleteAvatarImageFile( $userId );

        $id = (int)abs ( $userId );

        $user_has_avatar = User::where( 'user_id', '=', $id )->update( array( 'user_has_avatar' => FALSE ) );

        if ( $user_has_avatar ) :

            HugeSession::set( 'user_avatar_file', self::getPublicUserAvatarFilePathByUserId( $userId ) );
            Slim::getInstance()->flash( 'success', HugeText::get( 'FEEDBACK_AVATAR_IMAGE_DELETE_SUCCESSFUL' ) );
            return true;

        else :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_IMAGE_DELETE_FAILED' ) );
            return false;

        endif;
    }

    /**
     * Removes the avatar image file from the filesystem
     *
     * @param $userId
     * @return bool
     */
    public static function deleteAvatarImageFile( $userId )
    {
        // Check if file exists
        if ( ! file_exists ( DIR_AVATARS . $userId . ".jpg" ) ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_IMAGE_DELETE_NO_FILE' ) );
            return false;

        endif;

        // Delete avatar file
        if ( ! unlink ( DIR_AVATARS . $userId . ".jpg" ) ) :

            Slim::getInstance()->flash( 'error', HugeText::get( 'FEEDBACK_AVATAR_IMAGE_DELETE_FAILED' ) );
            return false;

        endif;

        return true;
    }
}
