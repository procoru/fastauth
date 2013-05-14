<?php
namespace FastAuth\Provider;

/**
 * Yandex OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Yandex extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://oauth.yandex.ru/token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
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
        $response = $this->httpPost('https://login.yandex.ru/info', array(
            'oauth_token' => $token->getAccessToken(),
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        /*
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error->message);
        }
        */
        return new \FastAuth\Profile(
            $data->id,
            'yandex',
            $data->real_name ? $data->real_name : $data->display_name,
            property_exists($data, 'default_email') ? $data->default_email : null,
            (array)$data
        );
    }
}
