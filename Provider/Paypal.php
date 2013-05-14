<?php
namespace FastAuth\Provider;

/**
 * PayPal OAuth2 Provider
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Paypal extends \FastAuth\Provider
{
    /**
     * @see \FastAuth\Provider
     */
    public function retrieveToken($code)
    {
        if (empty($code)) {
            throw new \FastAuth\Exception\BadRequest('Code is empty.');
        }
        $response = $this->httpPost('https://identity.x.com/xidentity/oauthtokenservice', array(
            'client_id' => $this->options['client_id'],
            'client_secret' => $this->options['secret'],
            'redirect_uri' => $this->options['redirect_uri'],
            'grant_type' => 'authorization_code',
            'code' => $code,
        ));
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
        if (property_exists($data, 'error')) {
            throw new \FastAuth\Exception\BadRequest(property_exists($data, 'error_description') ? $data->error_description : $data->error);
        }
        $data->expires_in += time();
        return new \FastAuth\Token($data->access_token, $data->expires_in, (array)$data);
    }

    /**
     * @see \FastAuth\Provider
     */
    public function retrieveProfile(\FastAuth\Token $token)
    {
        $response = $this->httpGet('https://identity.x.com/xidentity/resources/profile/me?oauth_token=' . $token->getAccessToken());
        $data = @json_decode($response);
        if (empty($data)) {
            throw new \FastAuth\Exception\ServerError('Unknown error occurred.');
        }
/*        if ((property_exists($data, 'status') && $data->status != 'SUCCESS') || !property_exists($data, 'identity')) {
            throw new \FastAuth\Exception\BadRequest('Error retrieve profile.');
        }*/
        $data = $data->identity;
        return new \FastAuth\Profile(
            $data->userId,
            'paypal',
            $data->fullName,
            isset($data->emails[0]) ? $data->emails[0] : null,
            (array)$data
        );
    }
}
