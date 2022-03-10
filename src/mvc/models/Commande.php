<?php

namespace custombox\mvc\models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{

    protected $table = 'ccd_commande';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('custombox\mvc\models\User', 'id_user');
    }

    public function boite(){
        return $this->belongsTo('custombox\mvc\models\Boite', 'id_boite');
    }
    public function produits(){
        return $this->belongsToMany('custombox\mvc\models\Produit', 'ccd_contenucommande', 'id_commande', 'id_produit');
    }
}
