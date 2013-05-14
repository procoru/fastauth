<?php
namespace FastAuth;

/**
 * OAuth2 Token
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Token
{
    /**
     * Access token.
     *
     * @var string
     * @access protected
     */
    protected $accessToken;

    /**
     * Expires in
     *
     * @var int
     * @access protected
     */
    protected $expiresIn;

    /**
     * Raw data
     *
     * @var mixed
     * @access protected
     */
    protected $raw;

    /**
     * Create and init token.
     *
     * @param string $accessToken
     * @param int $expiresIn
     * @param mixed $raw
     * @access public
     * @return void
     */
    public function __construct($accessToken, $expiresIn, $raw = null)
    {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
        $this->raw = $raw;
    }

    /**
     * Get string with access token.
     *
     * @access public
     * @throws \FastAuth\Exception\Exception
     * @return string
     */
    public function getAccessToken()
    {
        if ($this->expiresIn && ($this->expiresIn < time())) {
            throw new \FastAuth\Exception\Exception('Access token is expired');
        }
        return $this->accessToken;
    }

    /**
     * Get raw data.
     *
     * @access public
     * @return mixed
     */
    public function getRawData()
    {
        return $this->raw;
    }
}
