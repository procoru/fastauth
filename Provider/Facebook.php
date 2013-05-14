<?php
namespace FastAuth\Provider;

/**
 * Facebook OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Facebook extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://graph.facebook.com/oauth/access_token', array(
            'client_id' => $this->options['client_id'],
            'redirect_uri' => $this->options['redirect_uri'],
            'client_secret' => $this->options['secret'],
            'code' => $code
        ));
        if (empty($response)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        parse_str($response, $data);
        $data = (object)$data;
        if (!property_exists($data, 'access_token')) {
            $js = json_decode($response);
            throw new \FastAuth\Exception\BadRequest($js->error->message);
        }
        $data->expires += time(); // seconds left to expired timestamp
        return new \FastAuth\Token($data->access_token, $data->expires, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://graph.facebook.com/me?access_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest($data->error->message);
        }
        return new \FastAuth\Profile(
            $data->id,
            'facebook',
            $data->name,
            property_exists($data, 'email') ? $data->email : null,
            (array)$data
        );
    }
}
