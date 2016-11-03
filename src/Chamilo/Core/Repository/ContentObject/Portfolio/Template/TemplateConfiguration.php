<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfigurationParser;
use DOMXPath;

/**
 * Portfolio template configuration parser
 * 
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class TemplateConfiguration extends \Chamilo\Core\Repository\Common\Template\TemplateConfiguration implements 
    TemplateConfigurationParser
{

    /**
     * Parse the template configuration definition to a valid TemplateConfiguration instance
     * 
     * @param DOMXPath $dom_xpath
     * @return TemplateConfiguration
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        return new self();
    }
}