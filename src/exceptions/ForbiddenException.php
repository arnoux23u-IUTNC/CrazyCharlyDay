<?php

namespace custombox\exceptions;

use Exception;

class ForbiddenException extends Exception
{

    private static string $title;

    public function __construct(string $title = "Forbidden", string $message = "Forbidden")
    {
        self::$title = $message;
        parent::__construct($title);
    }

    public function getTitle(): string
    {
        return self::$title;
    }
}