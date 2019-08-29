<?php namespace Models\Entity;

use Models\GlobalModel;

class Swimmer extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_Swimmer;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'Swimmer_id', 'user_name', 'password', 'address', 'phone', 'cellphone' ];

}