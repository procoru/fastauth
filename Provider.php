<?php
namespace FastAuth;

/**
 * Base class OAuth2 Provider
 *
 * @abstract
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
abstract class Provider
{
    /**
     * Array of options.
     *
     * @var array
     * @access protected
     */
    protected $options = array();

    /**
     * String with proxy settings.
     *  Example: "127.0.0.1:8081"
     *
     * @var string
     * @access protected
     */
    protected $proxy = null;

    /**
     * Create and init provider.
     *
     * @param array $options
     * @param string $proxy
     * @access public
     * @return void
     */
    public function __construct($options = array(), $proxy = null)
    {
        $this->options = $options;
        $this->proxy = $proxy;
    }

    /**
     * Retrieve access token.
     *
     * @param string $code
     * @abstract
     * @access public
     * @return \FastAuth\Token
     */
    abstract public function retrieveToken(/*string*/ $code);

    /**
     * Retrieve User Profile.
     *
     * @param \FastAuth\Token $token
     * @abstract
     * @access public
     * @return \FastAuth\Profile
     */
    abstract public function retrieveProfile(\FastAuth\Token $token);

    /**
     * Set options.
     *
     * @param array $options
     * @access public
     * @return void
     */
    public function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Perform HTTP request.
     *
     * @param string $url
     * @param array $params
     * @access protected
     * @return string
     */
    protected function request($url, array $params)
    {
        $params['proxy'] = $this->proxy;
        $params['timeout'] = array_key_exists('timeout', $this->options) ? $this->options['timeout'] : 3; // seconds
        $context = stream_context_create(array('http' => $params));
        return @file_get_contents($url, false, $context);
    }

    /**
     * Perform HTTP request using the GET method.
     *
     * @param string $url
     * @access protected
     * @return string
     */
    protected function httpGet($url)
    {
        return $this->request($url, array('method' => 'GET'));
    }

    /**
     * Perform HTTP request using the POST method.
     *
     * @param string $url
     * @param array $params
     * @access protected
     * @return string
     */
    protected function httpPost($url, $params)
    {
        return $this->request($url, array(
            'method' => 'POST',
            'content' => http_build_query($params),
        ));
    }
}
