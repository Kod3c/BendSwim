<?php namespace Models\Entity;

use Models\GlobalModel;

class Instructor extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_Instructor;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'Instructor_id', 'user_name', 'password', 'address', 'phone', 'cellphone' ];

}