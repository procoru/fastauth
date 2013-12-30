<?php
namespace FastAuth\Provider;

/**
 * LinkedIn OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2013 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Linkedin extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        /* // NOTICE используется при генерации URL и получении параметра code
        $state = md5(uniqid(rand(), true));
        setcookie($this->name.'_authorize_state', $state);
        */
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://www.linkedin.com/uas/oauth2/accessToken', array(
            'client_id' => $this->options['client_id'],
            'redirect_uri' => $this->options['redirect_uri'],
            'client_secret' => $this->options['secret'],
            'code' => $code
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error_message')) {
            throw new \FastAuth\Exception\BadRequest($data->error_message);
        }
        return new \FastAuth\Token($data->access_token, null, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://api.linkedin.com/v1/people/~:(id,email-address,first-name,last-name)?format=json&oauth2_access_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'message')) {
            throw new \FastAuth\Exception\BadRequest($data->message);
        }
        return new \FastAuth\Profile(
            $data->id,
            'linkedin',
            $data->firstName . ' ' . $data->lastName,
            isset($data->emailAddress) ? $data->emailAddress : null,
            (array)$data
        );
    }
}
