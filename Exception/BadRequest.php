<?php
namespace FastAuth\Exception;

/**
 * Bad Request Exception
 *
 * @package FastAuth
 * @copyright (c) 2012 RSSystems
 * @author Vyacheslav Shindin <shindin@rssystems.ru>
 */
class BadRequest extends \FastAuth\Exception\Exception
{
    /**
     * Get HTTP code of "400 Bad Request".
     *
     * @access public
     * @return int
     */
    public function getHttpCode()
    {
        return 400;
    }
}
