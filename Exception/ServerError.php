<?php
namespace FastAuth\Exception;

/**
 * Server Error Exception
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class ServerError extends Exception
{
    /**
     * Get HTTP code of "500 Internal Server Error".
     *
     * @access public
     * @return int
     */
    public function getHttpCode()
    {
        return 500;
    }
}
