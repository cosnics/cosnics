<?php
namespace Chamilo\Libraries\Format\Response;

use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Page;

/**
 *
 * @package Chamilo\Libraries\Format\Response
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExceptionResponse extends Response
{

    /**
     * Constructor
     *
     * @param \Exception $exception
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct($exception, $application)
    {
        $page = Page::getInstance();
        $page->setApplication($application);

        $html = array();
        $html[] = $page->getHeader()->render();
        $html[] = Display::error_message($exception->getMessage());
        $html[] = $page->getFooter()->render();

        parent::__construct('', implode(PHP_EOL, $html));
    }
}