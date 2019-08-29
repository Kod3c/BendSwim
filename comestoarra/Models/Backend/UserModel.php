<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\GlobalHelper;
use Helpers\Huge\Core\HugeRequest;
use Helpers\LanguageHelper;
use Illuminate\Support\Facades\DB;
use LiveControl\EloquentDataTable\DataTable;
use LiveControl\EloquentDataTable\VersionTransformers\Version109Transformer;
use Models\Entity\Profile;
use Models\Entity\Role;
use Slim\Slim;
use Models\Entity\Huge\User;
use stdClass;
use Helpers\Huge\Core\HugeAvatar;
use Helpers\Huge\Core\HugeConfig;
use Helpers\Huge\Core\HugeSession;

class UserModel
{

    public static function getPublicProfilesOfAllUsers()
    {
        // TODO : Need to implement eloquent relationship !
        // $query = User::all();
        $query = User::leftJoin('roles', 'users.role_id', '=', 'roles.role_id')->get();

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
            $all_users_profiles[$user->user_id]->user_provider_type       = $user->user_provider_type;
            $all_users_profiles[$user->user_id]->user_avatar_link   = ( HugeConfig::get( 'USE_GRAVATAR' ) ? HugeAvatar::getGravatarLinkByEmail( $user->user_email ) : HugeAvatar::getPublicAvatarFilePathOfUser( $user->user_has_avatar, $user->user_id ) );

        endforeach;

        return $all_users_profiles;
    }

    // TODO : Need FIX
    public static function getUserDatatable()
    {

        $user = new User();

        $dataTable = new DataTable(
            $user,
            ['user_id', 'user_name', 'user_email', 'user_account_type', 'user_active', 'user_deleted', 'user_suspension_timestamp', 'user_has_avatar']
        );

        $dataTable->setFormatRowFunction( function ( $user )
        {

            if ( $user->user_account_type == 7 ) :

                $type = '<span class="label label-success">Super Admin</span>';

            else :

                $type = '<span class="label label-primary">Normal User</span>';

            endif;

            if ( $user->user_active == 0 ) :

                $active = '<span class="label label-danger">Not Active</span>';

            else :

                $active = '<span class="label label-success">Active</span>';

            endif;

            if ( $user->user_deleted == 1 ) :

                $banned = '<span class="label label-danger">Banned</span>';

            endif;

            if ( $user->user_suspension_timestamp ) :

                $suspend = '<span class="label label-danger">Suspended for' .  round ( abs ( $user->user_suspension_timestamp - time() ) / 60 / 60, 2 ) . 'hours </span>';

            endif;

            if ( $user->user_id != HugeSession::get( 'user_id' ) AND HugeSession::get( 'user_account_type' ) == 7 ) :

                $adminFeatures = '
                                    <a href="' . Slim::getInstance()->urlFor( 'users' ) . $user->id . '" class="btn btn-primary" rel="tooltip" data-placement="top" title="View user profile"><i class="fa fa-search"></i></a>
                                    <a href="#" role="button" class="btn btn-warning" data-toggle="modal" data-target="#suspendUser' . $user->user_id . '" rel="tooltip" data-placement="top" title="Suspend / Un-suspend User"><i class="fa fa-exclamation-circle"></i></a>
                                    <a href="#" role="button" class="btn btn-danger" data-toggle="modal" data-target="#banUser' . $user->user_id . '" rel="tooltip" data-placement="top" title="Ban / Un-ban User"><i class="fa fa-ban"></i></a>';

            else :

                $adminFeatures = '<a href="' . Slim::getInstance()->urlFor( 'users' ) . $user->id . '" class="btn btn-primary" rel="tooltip" data-placement="top" title="View user profile"><i class="fa fa-search"></i></a>';

            endif;

            return [
                '<img src="' . HugeSession::get( 'user_avatar_file' ) . '" width="50" alt="">',
                $user->user_name,
                $user->user_email,
                $type,
                $active,
                $adminFeatures
            ];
        });

        $dataTable->setVersionTransformer( new Version109Transformer() );

        echo json_encode( $dataTable->make() );

    }

    public static function getPublicProfileOfUser( $user_id )
    {

        $user = User::where( 'user_id', '=', $user_id )->take( 1 )->select( 'user_id' )->first();

        if ( count( $user ) == 1 ) :

            self::setProfileDetailsOfUser( $user->user_id );
            
            // $user = $user->profile;
            
            $user = User::leftJoin('user_profile', 'users.user_id', '=', 'user_profile.user_id')->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')->where( 'users.user_id', '=', $user_id )->take( 1 )->select( 'users.user_id', 'users.session_id', 'users.user_name', 'users.user_email', 'users.user_active', 'users.user_has_avatar', 'users.user_deleted', 'users.user_account_type', 'users.role_id', 'users.user_failed_logins', 'users.user_last_failed_login', 'user_profile.first_name', 'user_profile.last_name', 'user_profile.birth_date', 'user_profile.phone', 'user_profile.bio', 'user_profile.address', 'roles.role_id', 'roles.role_name' )->first();

            if ( HugeConfig::get( 'USE_GRAVATAR' ) ) :

                $user->user_avatar_link = HugeAvatar::getGravatarLinkByEmail( $user->user_email );

            else :

                $user->user_avatar_link = HugeAvatar::getPublicAvatarFilePathOfUser( $user->user_has_avatar, $user->user_id );

            endif;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_DOES_NOT_EXIST' ) );

            Slim::getInstance()->response()->redirect( Slim::getInstance()->urlFor( 'users' ) );

        endif;

        return $user;
    }

    public static function setProfileDetailsOfUser( $user_id )
    {

        $profile = Profile::where( 'user_id', '=', $user_id )->take( 1 )->first();

        if ( ! $profile ) :

            $profile = new Profile();

            $profile->user_id = $user_id;

            $profile->save();

        endif;

        return true;

    }

    // TODO: NEED FIX !
    public static function updateProfileDetailsOfUser( $user_id, $first_name, $last_name, $birth_date, $phone, $bio, $address )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $user = User::where( 'user_id', '=', $user_id )->first()->profile;

        if ( ! $user ) :

            Slim::getInstance()->flash( 'error', 'Profile details cannot be save, your account might be illegal !' );
            return false;

        endif;

        Profile::where( 'user_id', '=', $user->user_id )->take( 1 )->update( [
            'first_name'   => $first_name,
            'last_name'    => $last_name,
            'birth_date'   => $birth_date,
            'phone'        => $phone,
            'bio'          => $bio,
            'address'      => $address
        ] );

        Slim::getInstance()->flash( 'success', 'Your profile details has been updated !' );
        return true;

    }

    public static function doesUsernameAlreadyExist( $user_name )
    {
        $user = User::where( 'user_name', '=', $user_name )->take( 1 )->select( 'user_id' )->first();

        if ( ! $user ) :

            return false;

        endif;

        return true;
    }

    public static function doesEmailAlreadyExist( $user_email )
    {
        $user = User::where( 'user_email', '=', $user_email )->take( 1 )->select( 'user_id' )->first();

        if ( ! $user ) :

            return false;

        endif;

        return true;
    }

    public static function saveNewUserName( $user_id, $new_user_name )
    {
        $user = User::where( 'user_id', '=', $user_id )->take( 1 )->update( array( 'user_name' => $new_user_name ) );

        if ( $user ) :

            return true;

        endif;

        return false;
    }

    public static function saveNewEmailAddress( $user_id, $new_user_email )
    {
        $user = User::where( 'user_id', '=', $user_id )->take( 1 )->update( array( 'user_email' => $new_user_email ) );

        if ( $user ) :

            return true;

        endif;

        return false;
    }

    public static function editUserName( $new_user_name )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // new username same as old one ?
        if ( $new_user_name == HugeSession::get( 'user_name' ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_SAME_AS_OLD_ONE' ) );
            return false;

        endif;

        // username cannot be empty and must be azAZ09 and 2-64 characters
        if ( ! preg_match ( "/^[a-zA-Z0-9]{2,64}$/", $new_user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN' ) );
            return false;

        endif;

        // clean the input, strip usernames longer than 64 chars (maybe fix this ?)
        $new_user_name = substr( strip_tags ( $new_user_name ), 0, 64 );

        // check if new username already exists
        if ( self::doesUsernameAlreadyExist( $new_user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USERNAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        $status_of_action = self::saveNewUserName( HugeSession::get( 'user_id' ), $new_user_name );

        if ( $status_of_action ) :

            HugeSession::set( 'user_name', $new_user_name );
            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_USERNAME_CHANGE_SUCCESSFUL' ) );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
            return false;

        endif;
    }

    public static function editUserEmail( $new_user_email )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // email provided ?
        if ( empty ( $new_user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_EMAIL_FIELD_EMPTY' ) );
            return false;

        endif;

        // check if new email is same like the old one
        if ( $new_user_email == HugeSession::get( 'user_email' ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_EMAIL_SAME_AS_OLD_ONE' ) );
            return false;

        endif;

        // user's email must be in valid email format, also checks the length
        // @see http://stackoverflow.com/questions/21631366/php-filter-validate-email-max-length
        // @see http://stackoverflow.com/questions/386294/what-is-the-maximum-length-of-a-valid-email-address
        if ( ! filter_var ( $new_user_email, FILTER_VALIDATE_EMAIL ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN' ) );
            return false;

        endif;

        // strip tags, just to be sure
        $new_user_email = substr( strip_tags( $new_user_email ), 0, 254 );

        // check if user's email already exists
        if ( self::doesEmailAlreadyExist ( $new_user_email ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_USER_EMAIL_ALREADY_TAKEN' ) );
            return false;

        endif;

        // write to database, if successful ...
        // ... then write new email to session, Gravatar too (as this relies to the user's email address)
        if ( self::saveNewEmailAddress ( HugeSession::get( 'user_id' ), $new_user_email ) ) :

            HugeSession::set( 'user_email', $new_user_email );
            HugeSession::set( 'user_gravatar_image_url', HugeAvatar::getGravatarLinkByEmail( $new_user_email ) );
            Slim::getInstance()->flash( 'success', LanguageHelper::get( 'FEEDBACK_EMAIL_CHANGE_SUCCESSFUL' ) );
            return true;

        endif;

        Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_UNKNOWN_ERROR' ) );
        return false;
    }

    public static function getUserIdByUsername( $user_name )
    {

        $where      = ['user_name' => $user_name];

        $query = User::where( $where )->take( 1 )->select( 'user_id' )->first();

        // return one row (we only have one result or nothing)
        return $query->user_id;
    }

    public static function getInstructorUserIdByUsername( $user_name )
    {

        $where      = ['user_name' => $user_name, 'user_provider_type' => 'BSSInstructor Registration'];

        $query = User::where( $where )->take( 1 )->select( 'user_id' )->first();

        // return one row (we only have one result or nothing)
        return $query->user_id;
    }

    public static function getSwimmerUserIdByUsername( $user_name )
    {

        $where      = ['user_name' => $user_name, 'user_provider_type' => 'BSSSwimmer Registration'];

        $query = User::where( $where )->take( 1 )->select( 'user_id' )->first();

        // return one row (we only have one result or nothing)
        return $query->user_id;
    }

    public static function getUserDataByUsername( $user_name )
    {

        $where      = ['user_name' => $user_name];
        $orWhere    = ['user_email' => $user_name];

        $query =  User::where( $where )->orWhere( $orWhere )->take( 1 )->select( 'user_id', 'user_name', 'user_email', 'user_password_hash', 'user_active', 'user_deleted', 'user_suspension_timestamp', 'user_account_type', 'role_id', 'user_failed_logins', 'user_last_failed_login', 'user_provider_type' )->first();

        return $query;

    }

    public static function getUserDataByUserNameOrEmail( $user_name_or_email )
    {

        $where      = ['user_name' => $user_name_or_email];
        $orWhere    = ['user_email' => $user_name_or_email];

        $query = User::where( $where )->orWhere( $orWhere )->take( 1 )->select( 'user_id', 'user_name', 'user_email', 'user_password_hash', 'user_active', 'user_deleted', 'user_suspension_timestamp', 'user_account_type', 'role_id', 'user_failed_logins', 'user_last_failed_login', 'user_provider_type' )->first();

        return $query;
    }

    public static function getUserDataByUserIdAndToken( $user_id, $token )
    {

        $where      = ['user_id' => $user_id, 'user_remember_me_token' => $token];

        $query = User::where( $where )->take( 1 )->select( 'user_id', 'user_name', 'user_email', 'user_password_hash', 'user_active', 'user_deleted', 'user_suspension_timestamp', 'user_account_type', 'role_id', 'user_failed_logins', 'user_last_failed_login', 'user_provider_type' )->first();

        return $query;
    }

    public static function setNewPassword( $user_name, $user_password_new, $user_password_repeat )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // validate the password
        if ( ! self::validateNewPassword ( $user_name, $user_password_new, $user_password_repeat ) ) :

            return false;

        endif;

        // crypt the password (with the PHP 5.5+'s password_hash() function, result is a 60 character hash string)
        $user_password_hash = password_hash ( $user_password_new, PASSWORD_DEFAULT );

        if ( self::saveNewUserPassword ( $user_name, $user_password_hash ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL') );
            return true;

        else :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_PASSWORD_CHANGE_FAILED') );
            return false;

        endif;
    }

    public static function saveNewUserPassword( $user_name, $user_password_hash )
    {
        $where      = ['user_name' => $user_name];

        $query = User::where( $where )->take(1)->update( array(
            'user_password_hash' => $user_password_hash,
            'user_password_reset_hash'   => NULL,
            'user_password_reset_timestamp'   => NULL
        ));

        // if one result exists, return true, else false. Could be written even shorter btw.
        return ( count( $query ) == 1 ? true : false );
    }

    public static function validateNewPassword( $user_name, $user_password_new, $user_password_repeat )
    {
        if ( empty ( $user_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_USERNAME_FIELD_EMPTY') );
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

    public static function setAccountSuspensionStatus( $suspensionInDays, $userId )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( $suspensionInDays > 0 ) :

            $suspensionTime = time() + ( $suspensionInDays * 60 * 60 * 24 );

        else :

            $suspensionTime = null;

        endif;

        $query = User::where( 'user_id', '=', $userId )->take( 1 )->update( array(
            'user_suspension_timestamp' => $suspensionTime
        ));

        if ( count( $query ) == 1 ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS') );
            return true;

        endif;

        return false;

    }

    public static function setAccountBanStatus( $softDelete, $userId )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        // FYI "on" is what a checkbox delivers by default when submitted. Didn't know that for a long time :)
        if ( $softDelete == "on" ) :

            $delete = 1;

        else :

            $delete = 0;

        endif;

        $query = User::where( 'user_id', '=', $userId )->take( 1 )->update( array(
            'user_deleted'   => $delete
        ));

        if ( count( $query ) == 1 ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS') );
            return true;

        endif;

        return false;

    }

    public static function setAccountRootStatus( $user_account_type, $userId )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        if ( $user_account_type == "on" ) :

            $root = "7";

        else :

            $root = "1";

        endif;

        $query = User::where( 'user_id', '=', $userId )->take( 1 )->update( array(
            'session_id'   => NULL, // FORCE USER TO LOG OUT
            'user_account_type'   => $root
        ));

        if ( count( $query ) == 1 ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_ROOT_STATUS') );
//            HugeSession::set( 'user_account_type', $root );
            return true;

        endif;

        return false;

    }

    public static function setAccountRole( $role_id, $userId )
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $query = User::where( 'user_id', '=', $userId )->take( 1 )->update( array(
            'session_id'   => NULL, // FORCE USER TO LOG OUT
            'role_id'   => $role_id
        ));

        if ( count( $query ) == 1 ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_ROLE_STATUS') );
//            HugeSession::set( 'user_role_id', $role_id );
            return true;

        endif;

        return false;

    }

    public static function setAccountSuspensionAndDeletionStatus( $suspensionInDays, $softDelete, $userId )
    {

        if ( $suspensionInDays > 0 ) :

            $suspensionTime = time() + ( $suspensionInDays * 60 * 60 * 24 );

        else :

            $suspensionTime = null;

        endif;

        // FYI "on" is what a checkbox delivers by default when submitted. Didn't know that for a long time :)
        if ( $softDelete == "on" ) :

            $delete = 1;

        else :

            $delete = 0;

        endif;

        $query = User::where( 'user_id', '=', $userId )->take( 1 )->update(array(
            'user_suspension_timestamp' => $suspensionTime,
            'user_deleted'   => $delete
        ));

        if ( count( $query ) == 1 ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS') );
            return true;

        endif;

        return false;
    }

    public static function getUserRolePermission()
    {
        $where      = ['role_id' => HugeSession::get( 'user_role_id' )];

        $query      = Role::where( $where )->take( 1 )->select( 'role_permission' )->first();

        return $query->role_permission;
    }

    /**
     * @param $permission_name
     * @return bool
     */
    public static function checkUserRolePermission( $permission_name )
    {

        if ( ! LoginModel::isUserLoggedIn() ) :

            Slim::getInstance()->response()->redirect(  Slim::getInstance()->urlFor( 'login' ) );

            return false;

        else :

            $user_permission = json_decode( self::getUserRolePermission() );

            /** @var bool $permission_name */
            if ( $user_permission->$permission_name ) :

                return true;

            endif;

            return false;

        endif;

    }

    public static function getAllUserAdminRoles()
    {

        $query = Role::where( 'Instructor_default', '!=', 1 )->where( 'Swimmer_default', '!=', 1 )->get();

        $all_users_roles = [];

        foreach ( $query as $role) :

            $all_users_roles[$role->role_id] = new stdClass();
            $all_users_roles[$role->role_id]->role_id            = $role->role_id;
            $all_users_roles[$role->role_id]->role_name          = $role->role_name;
            $all_users_roles[$role->role_id]->role_desc          = $role->role_desc;
            $all_users_roles[$role->role_id]->role_permission    = $role->role_permission;
            $all_users_roles[$role->role_id]->role_permission_dashboard             = json_decode( $role->role_permission )->dashboard;
            $all_users_roles[$role->role_id]->role_permission_Swimmer               = json_decode( $role->role_permission )->Swimmer;
            $all_users_roles[$role->role_id]->role_permission_Instructor               = json_decode( $role->role_permission )->Instructor;
            $all_users_roles[$role->role_id]->role_permission_appointment           = json_decode( $role->role_permission )->appointment;
            $all_users_roles[$role->role_id]->role_permission_global_settings       = json_decode( $role->role_permission )->global_settings;
            $all_users_roles[$role->role_id]->role_permission_user_managers         = json_decode( $role->role_permission )->user_managers;
            $all_users_roles[$role->role_id]->role_permission_audit_trails          = json_decode( $role->role_permission )->audit_trails;
            $all_users_roles[$role->role_id]->register_default   = $role->register_default;
            $all_users_roles[$role->role_id]->admin_default   = $role->admin_default;
            $all_users_roles[$role->role_id]->Instructor_default   = $role->Instructor_default;
            $all_users_roles[$role->role_id]->Swimmer_default   = $role->Swimmer_default;

        endforeach;

        return $all_users_roles;
    }

    public static function getAllUserRoles()
    {

        $query = Role::all();

        $all_users_roles = [];

        foreach ( $query as $role) :

            $all_users_roles[$role->role_id] = new stdClass();
            $all_users_roles[$role->role_id]->role_id            = $role->role_id;
            $all_users_roles[$role->role_id]->role_name          = $role->role_name;
            $all_users_roles[$role->role_id]->role_desc          = $role->role_desc;
            $all_users_roles[$role->role_id]->role_permission    = $role->role_permission;
            $all_users_roles[$role->role_id]->role_permission_dashboard             = json_decode( $role->role_permission )->dashboard;
            $all_users_roles[$role->role_id]->role_permission_Swimmer               = json_decode( $role->role_permission )->Swimmer;
            $all_users_roles[$role->role_id]->role_permission_Instructor               = json_decode( $role->role_permission )->Instructor;
            $all_users_roles[$role->role_id]->role_permission_appointment           = json_decode( $role->role_permission )->appointment;
            $all_users_roles[$role->role_id]->role_permission_global_settings       = json_decode( $role->role_permission )->global_settings;
            $all_users_roles[$role->role_id]->role_permission_user_managers         = json_decode( $role->role_permission )->user_managers;
            $all_users_roles[$role->role_id]->role_permission_audit_trails          = json_decode( $role->role_permission )->audit_trails;
            $all_users_roles[$role->role_id]->register_default   = $role->register_default;
            $all_users_roles[$role->role_id]->admin_default   = $role->admin_default;
            $all_users_roles[$role->role_id]->Instructor_default   = $role->Instructor_default;
            $all_users_roles[$role->role_id]->Swimmer_default   = $role->Swimmer_default;

        endforeach;

        return $all_users_roles;
    }

    public static function getUserRoleName( $role_id )
    {

        $where      = ['role_id' => $role_id];

        $query      = Role::where( $where )->take( 1 )->select( 'role_name' )->first();

        return $query->role_name;
    }

    public static function registerNewRole()
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $role_name = strip_tags ( HugeRequest::post( 'role_name' ) );
        $role_desc = strip_tags ( HugeRequest::post( 'role_desc' ) );

        $role_permission_dashboard = HugeRequest::post( 'role_permission_dashboard' );
        if ( $role_permission_dashboard == "on" ) :
            $dashboard = "1";
        else :
            $dashboard = "0";
        endif;

        $role_permission_Swimmer = HugeRequest::post( 'role_permission_Swimmer' );
        if ( $role_permission_Swimmer == "on" ) :
            $Swimmer = "1";
        else :
            $Swimmer = "0";
        endif;

        $role_permission_Instructor = HugeRequest::post( 'role_permission_Instructor' );
        if ( $role_permission_Instructor == "on" ) :
            $Instructor = "1";
        else :
            $Instructor = "0";
        endif;

        $role_permission_appointment = HugeRequest::post( 'role_permission_appointment' );
        if ( $role_permission_appointment == "on" ) :
            $appointment = "1";
        else :
            $appointment = "0";
        endif;

        $role_permission_global_settings = HugeRequest::post( 'role_permission_global_settings' );
        if ( $role_permission_global_settings == "on" ) :
            $global_settings = "1";
        else :
            $global_settings = "0";
        endif;

        $role_permission_user_managers = HugeRequest::post( 'role_permission_user_managers' );
        if ( $role_permission_user_managers == "on" ) :
            $user_managers = "1";
        else :
            $user_managers = "0";
        endif;

        $role_permission_audit_trails = HugeRequest::post( 'role_permission_audit_trails' );
        if ( $role_permission_audit_trails == "on" ) :
            $audit_trails = "1";
        else :
            $audit_trails = "0";
        endif;

        if ( UserModel::doesRoleAlreadyExist ( $role_name ) ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'FEEDBACK_ROLE_NAME_ALREADY_TAKEN' ) );
            return false;

        endif;

        $array_permission = array(
            'dashboard'         => $dashboard,
            'Swimmer'           => $Swimmer,
            'appointment'       => $appointment,
            'Instructor'           => $Instructor,
            'global_settings'   => $global_settings,
            'user_managers'     => $user_managers,
            'audit_trails'      => $audit_trails
        );

//        $role_permission = json_encode( $array_permission, JSON_PRETTY_PRINT );
        $role_permission = json_encode( $array_permission );

        if ( self::writeNewRoleToDatabase( $role_name, $role_desc, $role_permission ) ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ROLE_SUCCESSFULLY_CREATED') );
            return true;

        endif;

        return false;
    }

    public static function doesRoleAlreadyExist( $role_name )
    {
        $role = Role::where( 'role_name', '=', $role_name )->take( 1 )->select( 'role_name' )->first();

        if ( ! $role ) :

            return false;

        endif;

        return true;
    }

    public static function checkRoleUserAssociation( $role_id )
    {
        $assoc = User::where( 'role_id', '=', $role_id )->take( 1 )->select( 'role_id' )->first();

        if ( count( $assoc ) > 0 ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get('FEEDBACK_ROLE_USER_HAS_ASSOC') );
            return false;

        endif;

        return true;
    }

    public static function deleteRole( $role_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $assoc = self::checkRoleUserAssociation( $role_id );

        if ( $assoc ) :

            $delete = Role::where( 'role_id', '=', $role_id )->take( 1 )->delete();

            if ( $delete ) :

                Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ROLE_SUCCESS_DELETED') );
                return true;

            endif;

        endif;

        return false;
    }

    public static function updateRole( $role_id )
    {
        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $role_desc = strip_tags ( HugeRequest::post( 'role_desc' ) );

        $role_permission_dashboard = HugeRequest::post( 'role_permission_dashboard' );
        if ( $role_permission_dashboard == "on" ) :
            $dashboard = "1";
        else :
            $dashboard = "0";
        endif;

        $role_permission_Swimmer = HugeRequest::post( 'role_permission_Swimmer' );
        if ( $role_permission_Swimmer == "on" ) :
            $Swimmer = "1";
        else :
            $Swimmer = "0";
        endif;

        $role_permission_appointment = HugeRequest::post( 'role_permission_appointment' );
        if ( $role_permission_appointment == "on" ) :
            $appointment = "1";
        else :
            $appointment = "0";
        endif;

        $role_permission_Instructor = HugeRequest::post( 'role_permission_Instructor' );
        if ( $role_permission_Instructor == "on" ) :
            $Instructor = "1";
        else :
            $Instructor = "0";
        endif;

        $role_permission_global_settings = HugeRequest::post( 'role_permission_global_settings' );
        if ( $role_permission_global_settings == "on" ) :
            $global_settings = "1";
        else :
            $global_settings = "0";
        endif;

        $role_permission_user_managers = HugeRequest::post( 'role_permission_user_managers' );
        if ( $role_permission_user_managers == "on" ) :
            $user_managers = "1";
        else :
            $user_managers = "0";
        endif;

        $role_permission_audit_trails = HugeRequest::post( 'role_permission_audit_trails' );
        if ( $role_permission_audit_trails == "on" ) :
            $audit_trails = "1";
        else :
            $audit_trails = "0";
        endif;

        $array_permission = array(
            'dashboard'         => $dashboard,
            'Swimmer'           => $Swimmer,
            'Instructor'           => $Instructor,
            'appointment'       => $appointment,
            'global_settings'   => $global_settings,
            'user_managers'     => $user_managers,
            'audit_trails'      => $audit_trails
        );

        $role_permission = json_encode( $array_permission );

        $update = Role::where( 'role_id', '=', $role_id )->take( 1 )->update(
            array(
                "role_desc" =>  $role_desc,
                "role_permission" =>  $role_permission,
            )
        );

        if ( $update ) :

            Slim::getInstance()->flash( 'success', LanguageHelper::get('FEEDBACK_ROLE_SUCCESS_UPDATED') );
            return true;

        endif;


        return false;
    }

    public static function writeNewRoleToDatabase( $role_name, $role_desc, $role_permission )
    {
        $data = new Role();

        $data->role_name                = GlobalHelper::valueSafe( $role_name );
        $data->role_desc                = GlobalHelper::valueSafe( $role_desc );
        $data->role_permission          = $role_permission;

        $data->save();

        if ( count( $data ) == 1 ) :

            return true;

        endif;

        return false;
    }

}
