<?php

namespace custombox\mvc\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $table = 'ccd_users';
    protected $primaryKey = 'user_id';
    public $incrementing = true;
    public $timestamps = false;
    protected $guarded = ['user_id', 'created_at'];

    public function __construct()
    {
        $this['user_id'] = -1;
        parent::__construct();
    }

    public function authenticate()
    {
        session_regenerate_id();
        //Mise a jour des variables utilisateur
        $this->update(['last_ip' => $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'], "last_login" => date("Y-m-d H:i:s")]);
        $_SESSION['LOGGED_IN'] = true;
        $_SESSION['USER_ID'] = $this['user_id'];
        $_SESSION['USER_NAME'] = $this['username'];
    }

    public function isAdmin(): bool
    {
        return $this['is_admin'] == "1";
    }

    public function name(): string
    {
        return $this['lastname'] . " " . $this['firstname'];
    }

    public static function logout()
    {
        session_destroy();
        $l = $_SESSION['lang'];
        unset($_SESSION);
        session_start();
        $_SESSION['lang'] = $l;
    }



    //Relations

    public function commandes()
    {
        return $this->hasMany('custombox\models\Commande', 'id_user');
    }
}