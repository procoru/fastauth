<?php
namespace FastAuth\Provider;

/**
 * Soundcloud OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Soundcloud extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://api.soundcloud.com/oauth2/token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'redirect_uri' => $this->options['redirect_uri'],
            'code' => $code,
            'grant_type' => 'authorization_code',
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error);
        }
        $data->expires_in += time();
        return new \FastAuth\Token($data->access_token, $data->expires_in, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://api.soundcloud.com/me.json?oauth_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
/*        if (property_exists($data, 'status_code') && $data->status_code != 200) {
            throw new \FastAuth\Exception\BadRequest($data->status_txt);
        }*/
        return new \FastAuth\Profile(
            $data->id,
            'soundcloud',
            $data->full_name ? $data->full_name : $data->username,
            null,
            (array)$data
        );
    }
}
