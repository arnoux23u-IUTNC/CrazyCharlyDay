<?php

namespace custombox\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'ccd_user';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function commandes()
    {
        return $this->hasMany('custombox\models\Commande', 'id_user');
    }
}