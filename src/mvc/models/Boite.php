<?php

namespace custombox\models;

use Illuminate\Database\Eloquent\Model;

class Boite extends Model
{

    protected $table = 'ccd_boite';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = false;

}