<?php
declare(strict_types=1);

namespace App\Controller;

use App\SocialAuth\FacebookAuthService;
use App\SocialAuth\GoogleAuthService;

class SocialLoginController extends AppController
{
    protected $googleAuthService;
    protected $facebookAuthService;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->googleAuthService = new GoogleAuthService();
        $this->facebookAuthService = new FacebookAuthService();
    }

    public function googleLogin()
    {
        try {
            $authorizationUrl = $this->googleAuthService->getAuthorizationUrl();
            $this->redirect($authorizationUrl);
        } catch (\Exception $e) {
            $this->Flash->error($e->getMessage());
        }
    }

    public function googleCallback()
    {
        try {
            $code = $this->request->getQuery('code','');
            $this->googleAuthService->handleCallback($code);
            return $this->redirect('/');
        } catch (\Exception $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect('/');
        }
    }

    public function facebookLogin()
    {
        try {
            $authorizationUrl = $this->facebookAuthService->getAuthorizationUrl();
            $this->redirect($authorizationUrl);
        } catch (\Exception $e) {
            $this->Flash->error($e->getMessage());
        }
    }

    public function facebookCallback()
    {
        try {
            $code = $this->request->getQuery('code','');
            $this->facebookAuthService->handleCallback($code);
            return $this->redirect('/');
        } catch (\Exception $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect('/');
        }
    }
}
