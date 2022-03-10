<?php

namespace customBox\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'ccd_user';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function commandes()
    {
        return $this->hasMany('src\mvc\models\Commande', 'id_user');
    }
}