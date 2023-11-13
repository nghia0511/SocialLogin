<?php
declare(strict_types=1);

namespace App\SocialAuth;

use Cake\Core\Configure;
use Cake\Log\Log;
use League\OAuth2\Client\Provider\Facebook;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class FacebookAuthService implements SocialAuthServiceInterface
{
    const FACEBOOK = 'facebook';
    protected $provider;

    protected $usersTable;

    protected $userProvidersTable;

    public function __construct()
    {
        $appId = Configure::read('Facebook.appId');
        $appSecret = Configure::read('Facebook.appSecret');
        $redirectUri = Configure::read('Facebook.redirectUri');
        $graphApiVersion = Configure::read('Facebook.graphApiVersion');

        if (!$appId || !$appSecret || !$redirectUri || !$graphApiVersion) {
            throw new \RuntimeException('Facebook configuration is incomplete. Please check your configuration.');
        }

        $this->provider = new Facebook([
            'clientId'         => $appId,
            'clientSecret'     => $appSecret,
            'redirectUri'      => $redirectUri,
            'graphApiVersion'  => $graphApiVersion,
        ]);

        $this->usersTable = TableRegistry::getTableLocator()->get('Users');
        $this->userProvidersTable = TableRegistry::getTableLocator()->get('UserProviders');
    }

    public function getAuthorizationUrl()
    {
        return $this->provider->getAuthorizationUrl();
    }

    public function handleCallback(string $code)
    {
        try {
            $token = $this->provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $data = $this->provider->getResourceOwner($token);

            // Process the user data here
            if($data){
                $this->processUserData($data);
            }

        } catch (IdentityProviderException $e) {
            Log::error('OAuth Error: ' . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function processUserData($data){
        $email = $data->getEmail() ?? $data->getId()."@test.com";
        $user = $this->usersTable->find()
                ->where(['email' => $email])
                ->first();

        if (empty($user)) {
            // User doesn't exist, create a new user
            $user = $this->usersTable->newEntity([
                'email' => $email,
                'username' => $data->getName(),
                'password' => Security::getSalt(),
            ]);
            $this->usersTable->save($user);
            
            // Link the user to the authentication provider
            if($user->id){
                $userProvider = $this->userProvidersTable->newEntity([
                    'user_id' => $user->id,
                    'provider_name' => self::FACEBOOK,
                    'provider_id' => $data->getId(),
                ]);
                $this->userProvidersTable->save($userProvider);
            }
        }
    }
}
