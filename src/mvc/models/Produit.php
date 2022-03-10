<?php

namespace customBox\models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{

    protected $table = 'ccd_produit';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function categorie()
    {
        return $this->belongsTo('src\mvc\models\Categorie', 'id_categorie');
    }
}