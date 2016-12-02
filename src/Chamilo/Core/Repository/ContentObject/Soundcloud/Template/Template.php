<?php
namespace Chamilo\Core\Repository\ContentObject\Soundcloud\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\Soundcloud\Storage\DataClass\Soundcloud;
use DOMXPath;

class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return \core\repository\content_object\soundcloud\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $template_configuration = TemplateConfiguration::get($dom_xpath);
        $template_translation = TemplateTranslation::get($dom_xpath);
        
        $content_object = new Soundcloud();
        
        return new self($template_configuration, $content_object, $template_translation);
    }
}