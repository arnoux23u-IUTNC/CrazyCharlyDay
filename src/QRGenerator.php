<?php

namespace custombox;

class QRGenerator
{
    public static function generateQRCODE($url)
    {
        return <<<HTML
            <img src="https://api.qrserver.com/v1/create-qr-code/?data={$url}&size=300x300&ecc=">
            HTML;
    }
}