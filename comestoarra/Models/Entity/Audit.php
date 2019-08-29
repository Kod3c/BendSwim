<?php namespace Models\Entity;

use Models\GlobalModel;

class Audit extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_AUDIT;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'audit_id', 'created_user' ];

}