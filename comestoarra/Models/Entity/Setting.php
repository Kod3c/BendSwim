<?php namespace Models\Entity;

use Models\GlobalModel;

class Setting extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_SETTING;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'id', 'last_update_date', 'last_update_user', 'mail_smtp_host', 'mail_smtp_auth', 'mail_smtp_username', 'mail_smtp_password', 'mail_smtp_port', 'mail_smtp_encryption' ];

}