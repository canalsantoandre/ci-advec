<?php

namespace App\Controllers;

use League\OAuth2\Client\Provider\Instagram;

class LInstagram extends BaseController
{

    private function session()
    {
        /* ------------------------------------------------ */
        /* RECUPERA ACESSOS E DADOS DE ACAO DAS TELAS */
        /* ------------------------------------------------ */
        $data = [];
        $session = new \App\Models\SessionModel();
        $data = $session->retornaSessao($data, 'dashboard/');

        return $data;
    }

    public function auth()
    {


        $provider = new Instagram([
            'clientId'          => INSTAGRAM['clientId'],
            'clientSecret'      => INSTAGRAM['clientSecret'],
            'redirectUri'       => INSTAGRAM['redirectUri'],
            'host'              => INSTAGRAM['host'],  // Optional, defaults to https://api.instagram.com
            'graphHost'         => INSTAGRAM['graphHost'] // Optional, defaults to https://graph.instagram.com
        ]);

        $session = session();
        
        if (!isset($_GET['code'])) {

            $options = [
                'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
                'scope' => ['user_profile', 'user_media'] // array or string
            ];
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl($options);

            
            $session->set('oauth2state', $provider->getState());
            //$_SESSION['oauth2state'] = $provider->getState();

            header('Location: ' . $authUrl);
            exit;

            // Check given state against previously stored one to mitigate CSRF attack

            // } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
        } elseif (empty($_GET['state']) || ($_GET['state'] !== $session->get('oauth2state'))) {

            //unset($_SESSION['oauth2state']);
            $session = session();
            $session->destroy();
            exit('Invalid state');
        } else {

            // Try to get an access token (using the authorization code grant)
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            // Optional: Now you have a token you can look up a users profile data
            try {

                
                // We got an access token, let's now get the user's details
                $user = $provider->getResourceOwner($token);
                // Use these details to create a new profile
                $data['userId'] = $user->getId();
                $data['userName'] = $user->getNickname();
                //$data['user'] = $user->toArray();
            } catch (\Exception $e) {

                print($e);
                // Failed to get user details
                exit('Oh dear...');
            }

            // Use this to interact with an API on the users behalf
            $data['token'] = $token->getToken();
        }

        
        //$data['content_view'] = 
        return view('_main/instagram', $data);
        
        //return view('_layout', $data);
    }
}
