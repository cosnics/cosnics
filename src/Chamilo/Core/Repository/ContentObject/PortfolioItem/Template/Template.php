<?php
namespace Chamilo\Core\Repository\ContentObject\PortfolioItem\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\PortfolioItem\Storage\DataClass\PortfolioItem;
use DOMXPath;

class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return \core\repository\content_object\portfolio_item\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $template_configuration = TemplateConfiguration::get($dom_xpath);
        $template_translation = TemplateTranslation::get($dom_xpath);
        
        $content_object = new PortfolioItem();
        
        return new self($template_configuration, $content_object, $template_translation);
    }
}