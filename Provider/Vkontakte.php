<?php
namespace FastAuth\Provider;

/**
 * Vkontakte OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Vkontakte extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://oauth.vk.com/access_token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'code' => $code,
            'redirect_uri' => $this->options['redirect_uri'],
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error_description);
        }
        $data->expires_in += time(); // seconds left to expired timestamp
        return new \FastAuth\Token($data->access_token, $data->expires_in, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $raw = $token->getRawData();
        $response = $this->httpPost('https://api.vk.com/method/users.get', array(
            'uids' => $raw['user_id'],
            'fields' => 'first_name,last_name,bdate,sex,photo_max_orig',
            'name_case' => 'nom',
            'access_token' => $token->getAccessToken(),
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error->error_msg);
        }
        $data = $data->response[0];
        return new \FastAuth\Profile(
            $data->uid,
            'vkontakte',
            $data->first_name . ' ' . $data->last_name,
            null, // email
            (array)$data
        );
    }
}
