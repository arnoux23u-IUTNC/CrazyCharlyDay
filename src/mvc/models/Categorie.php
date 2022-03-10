<?php

namespace customBox\models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{

    protected $table = 'ccd_categorie';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function produits()
    {
        return $this->hasMany('src\mvc\models\Produit', 'id_categorie');
    }

}
