<?php
namespace FastAuth\Provider;

/**
 * Wordpress OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Wordpress extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://public-api.wordpress.com/oauth2/token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'redirect_uri' => $this->options['redirect_uri'],
            'code' => $code,
            'grant_type' => 'authorization_code',
        ));
        //{"access_token":"bFg12%omDAX2ar6sIbNNg)@&t!ss24pLv4iDc7Bu!tXoI9AEt@tGY*zB%6L%RWGD","token_type":"bearer","blog_id":"0","blog_url":"public-api.wordpress.com","scope":""}
        //{"error":"invalid_client","error_description":"Unknown client_id."}
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error_description);
        }
        return new \FastAuth\Token($data->access_token, null, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        // Способ 1: работает, но не подходит т.к. использует системный вызов
        /*$response = `curl  -H 'authorization: Bearer {$token->getAccessToken()}'  'https://public-api.wordpress.com/rest/v1/me/?pretty=1'`;
        print_r($response);exit;*/

        // Способ 2: тот который нужно использовать, но он почему-то не работает
        /*$options  = array (
            'http' => array (
                'header' => array(
                    'authorization: Bearer ' . $token->getAccessToken(),
                ),
            ),
        );
        $context  = stream_context_create($options);
        $response = file_get_contents(
            'https://public-api.wordpress.com/rest/v1/me/?pretty=1',
            false,
            $context
        );
        print_r($response);exit;*/

        // Способ 3: с помощью cURL функций
        $url = 'https://public-api.wordpress.com/rest/v1/me/?pretty=1';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_VERBOSE, 0); 
        curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'authorization: Bearer ' . $token->getAccessToken(),
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        //{ "ID": 15482972, "display_name": "procoru", "username": "procoru", "email": "russian.procoder@gmail.com", "primary_blog": 0, "avatar_URL": "http:\/\/2.gravatar.com\/avatar\/8c3a91013b0e6681c79b01dcfa73dd75?s=96&d=identicon", "profile_URL": "http:\/\/en.gravatar.com\/procoru", "verified": true, "meta": { "links": { "self": "https:\/\/public-api.wordpress.com\/rest\/v1\/me", "help": "https:\/\/public-api.wordpress.com\/rest\/v1\/me\/help", "site": "https:\/\/public-api.wordpress.com\/rest\/v1\/sites\/5836086" } } }
        //{ "error": "authorization_required", "message": "An active access token must be used to query information about the current user." }
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->message);
        }
        return new \FastAuth\Profile(
            $data->ID,
            'wordpress',
            property_exists($data, 'display_name') ? $data->display_name : $data->username,
            property_exists($data, 'email') ? $data->email : null,
            (array)$data
        );
    }
}
