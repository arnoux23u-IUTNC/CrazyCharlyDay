<?php

namespace customBox\models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{

    protected $table = 'ccd_commande';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('customBox\models\User', 'id_user');
    }

    public function boite(){
        return $this->belongsTo('customBox\models\Boite', 'id_boite');
    }
    public function produits(){
        return $this->belongsToMany('customBox\models\Produit', 'ccd_contenucommande', 'id_commande', 'id_produit');
    }
}
