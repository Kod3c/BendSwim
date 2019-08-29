<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\Huge\Core\HugeAvatar;
use Helpers\Huge\Core\HugeConfig;
use Helpers\LanguageHelper;
use LiveControl\EloquentDataTable\DataTable;
use LiveControl\EloquentDataTable\VersionTransformers\Version109Transformer;
use Models\Entity\Huge\User;
use Models\Entity\Mailbox;
use Slim\Slim;
use stdClass;

class MailboxModel
{

    public static function getAllMail()
    {

        $mailbox = Mailbox::all();

        return $mailbox;
    }

    public static function getRecipientLists( $sender_id )
    {

        $query = User::leftJoin( 'roles', 'users.role_id', '=', 'roles.role_id' )->where( 'users.user_id', '!=', $sender_id )->get();

        $all_users_profiles = [];

        foreach ( $query as $user) :

            $all_users_profiles[$user->user_id] = new stdClass();
            $all_users_profiles[$user->user_id]->user_id            = $user->user_id;
            $all_users_profiles[$user->user_id]->user_name          = $user->user_name;
            $all_users_profiles[$user->user_id]->user_email         = $user->user_email;
            $all_users_profiles[$user->user_id]->user_account_type  = $user->user_account_type;
            $all_users_profiles[$user->user_id]->role_id            = $user->role_id;
            $all_users_profiles[$user->user_id]->role_name          = $user->role_name;
            $all_users_profiles[$user->user_id]->user_active        = $user->user_active;
            $all_users_profiles[$user->user_id]->user_deleted       = $user->user_deleted;
            $all_users_profiles[$user->user_id]->user_suspension_timestamp       = $user->user_suspension_timestamp;
            $all_users_profiles[$user->user_id]->user_avatar_link   = ( HugeConfig::get( 'USE_GRAVATAR' ) ? HugeAvatar::getGravatarLinkByEmail( $user->user_email ) : HugeAvatar::getPublicAvatarFilePathOfUser( $user->user_has_avatar, $user->user_id ) );

        endforeach;

        return $all_users_profiles;
    }

    public static function getCountUnreadMailUser( $user_id )
    {

        $where      = ['receiver_id' => $user_id, 'is_read' => '0'];

        $query = Mailbox::where( $where )->take( 1 )->count();

        return $query;
    }

    public static function getAllUnreadMailUser( $user_id )
    {

        $where      = ['mailbox.receiver_id' => $user_id, 'mailbox.is_read' => '0', 'mailbox.is_archived' => '0', 'mailbox.is_deleted' => '0'];

        $query = Mailbox::leftJoin('users', 'users.user_id', '=', 'mailbox.sender_id')->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')->where( $where )->select( 'mailbox.mailbox_id', 'mailbox.sender_id', 'mailbox.receiver_id', 'mailbox.mail_title', 'mailbox.mail_content', 'mailbox.sent_date', 'mailbox.is_read', 'mailbox.is_archived', 'mailbox.is_deleted', 'mailbox.deleted_date', 'mailbox.last_updated_date', 'users.user_id', 'users.user_name', 'users.user_email', 'users.user_has_avatar', 'roles.role_name' )->get();

        return $query;
    }

    public static function getAllInboxMailUser( $user_id )
    {

        $where      = ['mailbox.receiver_id' => $user_id, 'mailbox.is_archived' => '0', 'mailbox.is_deleted' => '0'];

        $query = Mailbox::leftJoin('users', 'users.user_id', '=', 'mailbox.sender_id')->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')->where( $where )->select( 'mailbox.mailbox_id', 'mailbox.sender_id', 'mailbox.receiver_id', 'mailbox.mail_title', 'mailbox.mail_content', 'mailbox.sent_date', 'mailbox.is_read', 'mailbox.is_archived', 'mailbox.is_deleted', 'mailbox.deleted_date', 'mailbox.last_updated_date', 'users.user_id', 'users.user_name', 'users.user_email', 'users.user_has_avatar', 'roles.role_name' )->get();

        return $query;
    }

    public static function getAllOutboxMailUser( $user_id )
    {

        $where      = ['mailbox.sender_id' => $user_id];

        $query = Mailbox::leftJoin('users', 'users.user_id', '=', 'mailbox.receiver_id')->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')->where( $where )->select( 'mailbox.mailbox_id', 'mailbox.sender_id', 'mailbox.receiver_id', 'mailbox.mail_title', 'mailbox.mail_content', 'mailbox.sent_date', 'mailbox.is_read', 'mailbox.is_archived', 'mailbox.is_deleted', 'mailbox.deleted_date', 'mailbox.last_updated_date', 'users.user_id', 'users.user_name', 'users.user_email', 'users.user_has_avatar', 'roles.role_name' )->get();

        return $query;
    }

    public static function getAllArchivedMailUser( $user_id )
    {

        $where      = ['mailbox.receiver_id' => $user_id, 'mailbox.is_archived' => '1', 'mailbox.is_deleted' => '0'];

        $query = Mailbox::leftJoin('users', 'users.user_id', '=', 'mailbox.sender_id')->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')->where( $where )->select( 'mailbox.mailbox_id', 'mailbox.sender_id', 'mailbox.receiver_id', 'mailbox.mail_title', 'mailbox.mail_content', 'mailbox.sent_date', 'mailbox.is_read', 'mailbox.is_archived', 'mailbox.is_deleted', 'mailbox.deleted_date', 'mailbox.last_updated_date', 'users.user_id', 'users.user_name', 'users.user_email', 'users.user_has_avatar', 'roles.role_name' )->get();

        return $query;
    }

    public static function writeNewSingleMailToDatabase( $sender_id, $receiver_id, $mail_title, $mail_content )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $data = new Mailbox();

        $data->sender_id                = $sender_id;
        $data->receiver_id              = $receiver_id;
        $data->mail_title               = $mail_title;
        $data->mail_content             = $mail_content;
        $data->sent_date                = date( 'Y-m-d H:i:s' );

        $data->save();

        if ( count( $data ) == 1 ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_COMPOSE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_COMPOSE') );
        return false;
    }

    public static function writeNewMailToDatabase( $sender_id, $receiver_id, $mail_title, $mail_content )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( $receiver_id ) :

            foreach ( $receiver_id as $recipient ) :

                $data = new Mailbox();

                $data->sender_id                = $sender_id;
                $data->receiver_id              = $recipient;
                $data->mail_title               = $mail_title;
                $data->mail_content             = $mail_content;
                $data->sent_date                = date( 'Y-m-d H:i:s' );

                $data->save();

            endforeach;

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_COMPOSE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_COMPOSE') );
        return false;
    }

    public static function deleteMailBySender( $mailbox_id )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Mailbox::where( 'mailbox_id', '=', $mailbox_id )->take( 1 )->delete();

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_DELETE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_DELETE') );
        return false;
    }

    public static function deleteMailByReceiver( $mailbox_id )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Mailbox::where( 'mailbox_id', '=', $mailbox_id )->take( 1 )->update( array(
            'is_read'   => 1,
            'is_deleted'   => 1,
            'deleted_date'   => date( 'Y-m-d H:i:s' ),
            'last_updated_date'   => date( 'Y-m-d H:i:s' )
        ));

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_DELETE') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_DELETE') );
        return false;
    }

    public static function markAsRead( $mailbox_id )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Mailbox::where( 'mailbox_id', '=', $mailbox_id )->take( 1 )->update( array(
            'is_read'   => 1,
            'last_updated_date'   => date( 'Y-m-d H:i:s' )
        ));

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_READ') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_READ') );
        return false;
    }

    public static function markAsUnread( $mailbox_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Mailbox::where( 'mailbox_id', '=', $mailbox_id )->take( 1 )->update( array(
            'is_read'   => 0,
            'last_updated_date'   => date( 'Y-m-d H:i:s' )
        ));

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_UNREAD') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_UNREAD') );
        return false;
    }

    public static function markAsArchived( $mailbox_id )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Mailbox::where( 'mailbox_id', '=', $mailbox_id )->take( 1 )->update( array(
            'is_archived'   => 1,
            'last_updated_date'   => date( 'Y-m-d H:i:s' )
        ));

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_ARCHIVED') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_ARCHIVED') );
        return false;
    }

    public static function markAsUnarchived( $mailbox_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $delete = Mailbox::where( 'mailbox_id', '=', $mailbox_id )->take( 1 )->update( array(
            'is_archived'   => 0,
            'last_updated_date'   => date( 'Y-m-d H:i:s' )
        ));

        if ( $delete ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_MAIL_SUCCESS_UNARCHIVED') );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_MAIL_ERROR_UNARCHIVED') );
        return false;
    }


}
