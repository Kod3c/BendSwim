<?php namespace Models\Entity;

use Models\GlobalModel;

class Profile extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_PROFILE;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'user_id' ];

    public function user()
    {
        return $this->belongsTo( 'Models\Entity\Huge\User' );
    }

}