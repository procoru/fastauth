<?php
namespace FastAuth\Provider;

/**
 * GitHub OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Github extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://github.com/login/oauth/access_token', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'redirect_uri' => $this->options['redirect_uri'],
            'code' => $code,
        ));
        @parse_str($response, $data);
        if (is_array($data) && array_key_exists('error', $data)) {
            throw new \FastAuth\Exception\BadRequest($data['error']);
        }
        if (empty($response) || !(is_array($data) && array_key_exists('access_token', $data))) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        return new \FastAuth\Token($data['access_token'], null, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://api.github.com/user?access_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'message')) {
            throw new \FastAuth\Exception\BadRequest($data->message);
        }
        return new \FastAuth\Profile(
            $data->id,
            'github',
            $data->name ? $data->name : ($data->login ? $data->login : null),
            $data->email,
            (array)$data
        );
    }
}
