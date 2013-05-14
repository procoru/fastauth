<?php
namespace FastAuth\Provider;

/**
 * Instagram OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Instagram extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://api.instagram.com/oauth/access_token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'redirect_uri' => $this->options['redirect_uri'],
            'code' => $code,
            'grant_type' => 'authorization_code',
        ));
        // {"access_token":"32579135.8e4ce36.eefee2209f9c4531b782928cfc339832","user":{"username":"pro_co_ru","bio":"","website":"","profile_picture":"http:\/\/images.instagram.com\/profiles\/anonymousUser.jpg","full_name":"Vyacheslav","id":"32579135"}}
        // {"code": 400, "error_type": "OAuthException", "error_message": "Invalid Client ID"}
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error_message')) {
            throw new \FastAuth\Exception\BadRequest($data->error_message);
        }
        return new \FastAuth\Token($data->access_token, null, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://api.instagram.com/v1/users/self?access_token=' . $token->getAccessToken());
        //{"meta":{"code":200},"data":{"username":"pro_co_ru","bio":"","website":"","profile_picture":"http:\/\/images.instagram.com\/profiles\/anonymousUser.jpg","full_name":"Vyacheslav","counts":{"media":0,"followed_by":0,"follows":3},"id":"32579135"}}
        //{"meta":{"error_type":"OAuthAccessTokenException","code":400,"error_message":"The \"access_token\" provided is invalid."}}
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'meta') && property_exists($data->meta, 'error_message')) {
            throw new \FastAuth\Exception\BadRequest($data->meta->error_message);
        }
        return new \FastAuth\Profile(
            $data->data->id,
            'instagram',
            $data->data->full_name ? $data->data->full_name : $data->data->username,
            null,
            (array)$data
        );
    }
}
