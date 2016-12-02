<?php
namespace Chamilo\Libraries\Format\Response;

use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Page;

/**
 *
 * @package Chamilo\Libraries\Format\Structure
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExceptionResponse extends Response
{

    /**
     * Constructor
     * 
     * @param string $content The response content
     * @param int $status The response status code
     * @param array $headers An array of response headers
     */
    public function __construct($exception, $application)
    {
        $page = Page::getInstance();
        $page->setApplication($application);
        
        $html = array();
        $html[] = $page->getHeader()->toHtml();
        $html[] = Display::error_message($exception->getMessage());
        $html[] = $page->getFooter()->toHtml();
        
        parent::__construct(implode(PHP_EOL, $html));
    }
}