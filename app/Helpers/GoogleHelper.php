<?php

if (!function_exists('create_google_client')) {
    /**
     * Create Google Client with SSL fix
     * 
     * @param array $config
     * @return \Google\Client
     */
    function create_google_client(array $config): \Google\Client
    {
        $client = new \Google\Client();
        $client->setClientId($config['clientId']);
        $client->setClientSecret($config['clientSecret']);
        $client->refreshToken($config['refreshToken']);
        
        // Fix SSL certificate issue untuk Windows development
        $httpClient = new \GuzzleHttp\Client([
            'verify' => false, // Disable SSL verification
            // Untuk production: 'verify' => base_path('cacert.pem')
        ]);
        $client->setHttpClient($httpClient);
        
        return $client;
    }
}
