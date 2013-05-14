<?php
namespace FastAuth;

/**
 * Client with Provider factory.
 *
 * @package FastAuth
 * @version $id$
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Client
{
    /**
     * @static
     * @param string $providerName
     * @param array $options
     * @return Provider
     * @throws \FastAuth\Exception\ServerError
     */
    public static function factory($providerName, $options = array())
    {
        if (!$providerName) {
            throw new \FastAuth\Exception\ServerError('Provider is not defined');
        }

        $provider = 'FastAuth\\Provider\\' . $providerName;
        if (class_exists($provider)) {
            return new $provider($options);
        }
        return null;
    }
}
