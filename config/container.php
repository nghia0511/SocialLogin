<?php
declare(strict_types=1);

use League\Container\Container;
use League\Container\ReflectionContainer;
use App\SocialAuth\SocialAuthServiceInterface;
use App\SocialAuth\FacebookAuthService;
use App\SocialAuth\GoogleAuthService;

$container = new Container();
$container->delegate(new ReflectionContainer());

// Register Container services
$container->add(SocialAuthServiceInterface::class, function ($container) {
    $request = $container->get('request');
    
    $serviceName = $request->getQuery('service', 'facebook');
    
    switch ($serviceName) {
        case 'facebook':
            return new FacebookAuthService();
        case 'google':
            return new GoogleAuthService();
        default:
            throw new InvalidArgumentException("Unsupported service: {$serviceName}");
    }
});

return $container;
