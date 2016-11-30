<?php
namespace Chamilo\Libraries\Format\Response;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Response extends \Symfony\Component\HttpFoundation\Response
{

    /**
     * Constructor
     * 
     * @param string $content The response content
     * @param int $status The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $headers['Content-Type'] = 'text/html';
        $headers['X-Powered-By'] = 'Chamilo Connect ' .
             \Chamilo\Configuration\Configuration::get('Chamilo\Core\Admin', 'version');
        
        parent::__construct($content, $status, $headers);
    }
}