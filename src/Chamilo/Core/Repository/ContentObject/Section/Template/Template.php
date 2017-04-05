<?php
namespace Chamilo\Core\Repository\ContentObject\Section\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use DOMXPath;

class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return \core\repository\content_object\section\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $template_configuration = TemplateConfiguration::get($dom_xpath);
        $template_translation = TemplateTranslation::get($dom_xpath);
        
        $content_object = new Section();
        
        return new self($template_configuration, $content_object, $template_translation);
    }
}