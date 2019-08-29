<?php namespace Models\Entity;

use Models\GlobalModel;

class Mailbox extends GlobalModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = self::DB_MAILBOX;

    public $timestamps = false;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [ 'mailbox_id', 'sender_id', 'receiver_id', 'mail_title', 'mail_content' ];

}