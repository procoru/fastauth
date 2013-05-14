<?php
namespace FastAuth\Exception;

/**
 * Default Exception
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class Exception extends \Exception
{
    /**
     * Get HTTP code of "200 OK".
     *
     * @access public
     * @return int
     */
    public function getHttpCode()
    {
        return 200;
    }
}
