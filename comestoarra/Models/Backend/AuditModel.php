<?php namespace Models\Backend;

use Helpers\CsrfHelper;
use Helpers\LanguageHelper;
use LiveControl\EloquentDataTable\DataTable;
use LiveControl\EloquentDataTable\VersionTransformers\Version109Transformer;
use Models\Entity\Audit;
use Slim\Slim;

class AuditModel
{

    public static function getAllAudit()
    {

        $audit = Audit::all();

        return $audit;
    }

    public static function getAuditDatatable()
    {

        $audit = new Audit();
        $dataTable = new DataTable(
            $audit,
            ['created_date', 'content_audit']
        );

        $dataTable->setVersionTransformer(new Version109Transformer());

        echo json_encode($dataTable->make());

    }

    public static function saveAuditTrails( $user_id, $content_audit )
    {

        $audit = new Audit();

        $audit->created_date    = date( 'Y-m-d H:i:s' );

        $audit->created_user    = $user_id;

        $audit->content_audit   = $content_audit;

        $audit->save();

        return true;

    }

    public static function getUserProfileAudit( $user_id )
    {

        $audit = Audit::where( 'created_user', '=', $user_id )->take( 20 )->select( 'audit_id', 'created_date', 'content_audit' )->get();

        if ( ! $audit ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'AUDIT_USER_NOT_FOUND' ) );

        endif;

        return $audit;
    }

    public static function getUserAudit( $user_id )
    {

        $audit = Audit::where( 'created_user', '=', $user_id )->take( 20 )->select( 'audit_id', 'created_date', 'content_audit' )->get();

        if ( ! $audit ) :

            Slim::getInstance()->flash( 'error', LanguageHelper::get( 'AUDIT_USER_NOT_FOUND' ) );

        endif;

        return $audit;
    }

    public static function trimAllAudit()
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $count = count( Audit::all() );

        $audit = Audit::take( abs( $count / 2 ) )->delete();

        return $audit;
    }

    public static function deleteAllAudit()
    {

        if ( ! CsrfHelper::validate( Slim::getInstance()->request()->post() ) ) :

            Slim::getInstance()->flash( 'error', 'Token was not valid !' );
            return false;

        endif;

        $count = count( Audit::all() );

        $audit = Audit::take( abs( $count ) )->delete();

        return $audit;
    }

}
