<?php


namespace App\Http;

use Spatie\WebhookClient\SignatureValidator\SignatureValidator;


class SignatureValidatorMati implements Spatie\WebhookClient\SignatureValidator\SignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        return true;
    }
}
