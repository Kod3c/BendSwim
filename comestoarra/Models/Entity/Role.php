<?php namespace Models\Entity;

use Models\GlobalModel;

class Role extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_ROLE;

    public $timestamps = false;

    protected $guarded = array( 'role_id' );

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'role_id', 'role_permission' ];

    public function user()
    {
        return $this->belongsTo( 'Models\Entity\Huge\User' );
    }

}