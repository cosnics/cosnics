<?php
namespace Chamilo\Libraries\Format\Response;

/**
 *
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Response extends \Symfony\Component\HttpFoundation\Response
{

    /**
     *
     * @param integer $version
     * @param string $content
     * @param integer $status
     * @param string[] $headers
     */
    public function __construct($version, $content = '', $status = 200, $headers = array())
    {
        $headers['Content-Type'] = 'text/html';
        $headers['X-Powered-By'] = 'Chamilo Connect ' . $version;

        parent::__construct($content, $status, $headers);
    }
}