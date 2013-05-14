<?php
namespace FastAuth;

/**
 * Profile
 *
 * @package FastAuth
 * @version $id$
 * @copyright 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Profile
{
    protected $id;
    protected $providerName;
    protected $name;
    protected $email;
    protected $raw;

    /**
     * Create and init user profile.
     *
     * @param mixed $id
     * @param string $providerName
     * @param string $name
     * @param string $email
     * @param mixed $raw
     * @access public
     * @return void
     */
    public function __construct($id, $providerName, $name = null, $email = null, $raw = null)
    {
        $this->id = $id;
        $this->providerName = $providerName;
        $this->name = $name;
        $this->email = $email;
        $this->raw = $raw;
    }

    /**
     * Get a unique user ID.
     *
     * @access public
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get name of provider.
     *
     * @access public
     * @return string
     */
    public function getProviderName()
    {
        return $this->providerName; // vkontakte, facebook, google, etc...
    }

    /**
     * Get user name.
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get user email.
     *
     * @access public
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
