<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfigurationParser;
use DOMXPath;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TemplateConfiguration extends \Chamilo\Core\Repository\Common\Template\TemplateConfiguration implements 
    TemplateConfigurationParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return TemplateConfiguration
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        return new self();
    }
}