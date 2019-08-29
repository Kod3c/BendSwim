<?php namespace Controllers\Backend;

/**
 * This file may not be redistributed in whole or significant part.
 * ---------------- THIS IS NOT FREE SOFTWARE ----------------
 *
 *
 * @file       	Controllers/Backend/MailboxController.php
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

use Controllers\GlobalController;
use Helpers\CsrfHelper;
use Helpers\Huge\Core\HugeAuth;
use Helpers\Huge\Core\HugeSession;
use Models\Backend\MailboxModel;

class MailboxController extends GlobalController
{

    public function __construct()
    {

        parent::__construct();

        HugeAuth::checkAuthentication();

    }

    public function backendMailbox()
    {

        $this->data['CSRF_TOKEN_NAME']            = CsrfHelper::TOKEN_NAME;
        $this->data['CSRF_TOKEN_VALUE']           = CsrfHelper::getToken();
        $this->data['CONTENT']                    = "Success Login !";
        $this->data['MAILBOX_ACTIVE']             = "active";
        $this->data['STICKY_NAV_LOWER']           = FALSE;
        $this->data['CONTENT_FLUID']              = TRUE;

        $this->data['ALL_USERS']                  = MailboxModel::getRecipientLists( HugeSession::get( 'user_id' ) );

        $this->data['LOGOUT']                     = $this->app->urlFor( 'logout' );

        $this->app->render('Backend/Content/Mailbox/Index.twig', $this->data);

    }

    public function backendComposeMailFromProfile()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::writeNewSingleMailToDatabase( $this->app->request()->post( 'sender_id'  ), $this->app->request()->post( 'receiver_id'  ), $this->app->request()->post( 'mail_title'  ), $this->app->request()->post( 'mail_content'  ) );
            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

    public function backendComposeMail()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::writeNewMailToDatabase( $this->app->request()->post( 'sender_id'  ),  $this->app->request()->post( 'receiver_id'  ), $this->app->request()->post( 'mail_title'  ), $this->app->request()->post( 'mail_content'  ) );

            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

    public function backendDeleteMailBySender()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::deleteMailBySender( $this->app->request()->post( 'mailbox_id'  ) );

            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

    public function backendDeleteMailByReceiver()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::deleteMailByReceiver( $this->app->request()->post( 'mailbox_id'  ) );

            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

    public function backendMarkAsRead()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::markAsRead( $this->app->request()->post( 'mailbox_id'  ) );

            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

    public function backendMarkAsUnread()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::markAsUnread( $this->app->request()->post( 'mailbox_id'  ) );

            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

    public function backendMarkAsArchived()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::markAsArchived( $this->app->request()->post( 'mailbox_id'  ) );

            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

    public function backendMarkAsUnarchived()
    {

        if ( $this->app->request()->isPost() OR $this->app->request()->isFormData() ) :

            MailboxModel::markAsUnarchived( $this->app->request()->post( 'mailbox_id'  ) );

            $this->app->response()->redirect( $this->app->urlFor( 'mailbox' ) );

        endif;

    }

}