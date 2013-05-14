<?php
namespace FastAuth\Provider;

/**
 * Google OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Google extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://accounts.google.com/o/oauth2/token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'code' => $code,
            'redirect_uri' => $this->options['redirect_uri'],
            'grant_type' => 'authorization_code',
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error);
        }
        $data->expires_in += time(); // seconds left to expired timestamp
        return new \FastAuth\Token($data->access_token, $data->expires_in, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error->message);
        }
        return new \FastAuth\Profile(
            $data->id,
            'google',
            $data->name,
            property_exists($data, 'email') ? $data->email : null,
            (array)$data
        );
    }
}
