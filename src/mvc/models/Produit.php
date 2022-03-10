<?php

    namespace custombox\mvc\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, string $string1, mixed $filter_var)
 */
class Produit extends Model
{

    protected $table = 'ccd_produit';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

    public function categorie()
    {
        return $this->belongsTo('custombox\mvc\models\Categorie', 'id_categorie');
    }
    public function commandes(){
        return $this->belongsToMany('custombox\models\Commande', 'ccd_contenucommande', 'id_produit', 'id_commande');
    }
}