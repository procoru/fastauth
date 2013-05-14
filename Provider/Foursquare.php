<?php
namespace FastAuth\Provider;

/**
 * Foursquare OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Foursquare extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://foursquare.com/oauth2/access_token', array(
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
        return new \FastAuth\Token($data->access_token, null, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://api.foursquare.com/v2/users/self?oauth_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if ($data->meta->code != 200) {
            throw new \FastAuth\Exception\BadRequest($data->meta->errorDetail);
        }
        $data = $data->response->user;
        return new \FastAuth\Profile(
            $data->id,
            'foursquare',
            $data->firstName . ' ' . $data->lastName,
            $data->contact->email ? $data->contact->email : null,
            (array)$data
        );
    }
}
