<?php

namespace custombox;

use Exception;
use Slim\Container;
use Slim\Http\UploadedFile;

/**
 * Class Validator
 * Validates data
 * @author Guillaume ARNOUX
 * @package mywishlist
 */
class Validator
{

    public static function validateStrings(array $strings): bool
    {
        foreach ($strings as $string) {
            if (empty(str_replace(" ", "", $string))) {
                return false;
            }
        }
        return true;
    }

    public static function validatePassword(string $password, string $password_confirm): bool
    {
        if (empty($password) || empty($password_confirm))
            return false;
        $validPassword = preg_match('@[0-9]@', $password) && preg_match('@[A-Z]@', $password) && preg_match('@[a-z]@', $password) && (preg_match('@[^\w]@', $password) || preg_match('@_@', $password));
        return $validPassword && strlen($password) > 13 && $password === $password_confirm;
    }
}