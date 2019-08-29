<?php namespace Models\Entity\Huge;

use Models\GlobalModel;

class User extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_USERS;

    protected $guarded = array( 'user_id' );

    public $incrementing  = false;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'user_id', 'user_password_hash', 'user_deleted', 'user_remember_me_token' ];

    public function profile()
    {
        return $this->hasOne( 'Models\Entity\Profile', 'user_id', 'user_id' );
    }

    public function role()
    {
        // TODO : Rethinking about hasMany ?
        return $this->hasOne( 'Models\Entity\Role', 'role_id', 'role_id' );
    }

}