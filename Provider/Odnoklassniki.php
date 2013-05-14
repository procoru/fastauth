<?php
namespace FastAuth\Provider;

/**
 * Odnoklassniki.ru OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Odnoklassniki extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('http://api.odnoklassniki.ru/oauth/token.do', array(
            'code' => $code,
            'redirect_uri' => $this->options['redirect_uri'],
            'grant_type' => 'authorization_code',
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error_description);
        }
        $data->expires_in = time() + 120; // 120sec = 2min
        return new \FastAuth\Token($data->access_token, $data->expires_in, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $sig = md5("application_key={$this->options['public']}" . md5($token->getAccessToken() . $this->options['secret']));
        $response = $this->httpPost('http://api.odnoklassniki.ru/api/users/getCurrentUser', array(
            'application_key' => $this->options['public'],
            'access_token' => $token->getAccessToken(),
            'sig' => $sig
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error_msg')) {
            throw new \FastAuth\Exception\BadRequest($data->error_msg);
        }
        return new \FastAuth\Profile(
            $data->uid,
            'odnoklassniki',
            $data->name,
            null, // email
            (array)$data
        );
    }
}
