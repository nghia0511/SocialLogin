<?php
declare(strict_types=1);

namespace App\SocialAuth;

use Cake\Core\Configure;
use Cake\Log\Log;
use League\OAuth2\Client\Provider\Google as GoogleProvider;
use Cake\ORM\TableRegistry;
use Cake\Utility\Security;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GoogleAuthService implements SocialAuthServiceInterface
{
    const GOOGLE = 'google';
    protected $provider;

    protected $usersTable;

    protected $userProvidersTable;

    public function __construct()
    {
        $clientId = Configure::read('Google.clientId');
        $clientSecret = Configure::read('Google.clientSecret');
        $redirectUri = Configure::read('Google.redirectUri');

        if (!$clientId || !$clientSecret || !$redirectUri) {
            throw new \RuntimeException('Google configuration is incomplete. Please check your configuration.');
        }

        $this->provider = new GoogleProvider([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri'  => $redirectUri,
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
            if ($data) {
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

    private function processUserData($data)
    {
        $email = $data->getEmail() ?? $data->getId() . "@google-test.com";
        $user = $this->usersTable->find()
            ->where(['email' => $email])
            ->first();

        if (empty($user)) {
            // User doesn't exist, create a new user
            $user = $this->usersTable->newEntity([
                'email'    => $email,
                'username' => $data->getName(),
                'password' => Security::getSalt(),
            ]);
            $this->usersTable->save($user);

            if($user->id){
                $userProvider = $this->userProvidersTable->newEntity([
                    'user_id' => $user->id,
                    'provider_name' => self::GOOGLE,
                    'provider_id' => $data->getId(),
                ]);
                $this->userProvidersTable->save($userProvider);
            }
        }
    }
}
