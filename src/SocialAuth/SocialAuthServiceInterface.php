<?php
declare(strict_types=1);

namespace App\SocialAuth;
interface SocialAuthServiceInterface
{
    public function getAuthorizationUrl();
    public function handleCallback(string $code);
}