<?php

namespace custombox\models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{

    protected $table = 'ccd_produit';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function categorie()
    {
        return $this->belongsTo('custombox\models\Categorie', 'id_categorie');
    }
    public function commandes(){
        return $this->belongsToMany('custombox\models\Commande', 'ccd_contenucommande', 'id_produit', 'id_commande');
    }
}