<?php namespace Models\Entity;

use Models\GlobalModel;

class Appointment extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_APPOINTMENT;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'Swimmer_id', 'Instructor_id', 'type', 'notes' ];

}