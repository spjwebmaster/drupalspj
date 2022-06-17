<?php

/**
* Ability to authorize and communicate with the Impexium REST API.
*/
class Impexium {

    public $response;

    /**
     * Constructor which initializes the consumer to impexium.
     *
     * @param string $app_name
     * @param string $app_key
     */
    public function __construct($app_name, $app_key) {
        $this->app_name = $app_name;
        $this->app_key = $app_key;
        $this->login_url = variable_get('web_api_url');
    }

    /**
     * Function to setup the curl and response it on requested method.
     *
     * @param string $url
     * @param string $data
     * @param string $headers
     * @param string $method
     */
    public function httpRequest($url, $data, $headers = array(), $method = 'GET') {
        if ($method === 'GET') {
            $data = NULL;
        }

        $options = array(
            'method' => $method,
            'headers' => $headers,
            'data' => $data,
        );
        return drupal_http_request($url, $options);
    }
}
