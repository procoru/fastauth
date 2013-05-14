<?php
namespace FastAuth\Provider;

/**
 * Mail.ru OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Mailru extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://connect.mail.ru/oauth/token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->options['redirect_uri'],
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
        $raw = $token->getRawData();
        $uid = $raw['x_mailru_vid'];

        $params = "app_id={$this->options['client_id']}method=users.getInfosession_key={$token->getAccessToken()}uids={$uid}";
        $sig = md5($uid . $params . $this->options['private']);

        $response = $this->httpPost('http://www.appsmail.ru/platform/api', array(
            'method' => 'users.getInfo',
            'app_id' => $this->options['client_id'],
            'session_key' => $token->getAccessToken(),
            'uids' => $uid,
            'sig' => $sig,
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (is_object($data) && property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error->error_msg);
        }
        $data = $data[0];
        return new \FastAuth\Profile(
            $data->uid,
            'mailru',
            $data->first_name . ' ' . $data->last_name,
            property_exists($data, 'email') ? $data->email : null,
            (array)$data
        );
    }
}
