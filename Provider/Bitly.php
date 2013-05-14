<?php
namespace FastAuth\Provider;

/**
 * Bitly OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Bitly extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://api-ssl.bitly.com/oauth/access_token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'redirect_uri' => $this->options['redirect_uri'],
            'code' => $code,
        ));
        if (empty($response)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (in_array($response, array('INVALID_CLIENT_ID', 'INVALID_CLIENT_SECRET', 'INVALID_CODE'))) {
            throw new \FastAuth\Exception\BadRequest($response);
        }
        parse_str($response, $data);
        return new \FastAuth\Token($data['access_token'], null, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://api-ssl.bitly.com/v3/user/info?access_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data) || !property_exists($data, 'status_code')) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'status_code') && $data->status_code != 200) {
            throw new \FastAuth\Exception\BadRequest($data->status_txt);
        }
        $data = $data->data;
        return new \FastAuth\Profile(
            $data->member_since,
            'bitly',
            $data->display_name ? $data->display_name : ($data->full_name ? $data->full_name : ($data->login ? $data->login : null)),
            null,
            (array)$data
        );
    }
}
