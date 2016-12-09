<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matching\Storage\DataClass\Matching;
use DOMXPath;

class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return \repository\content_object\survey_matching_question\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $template_configuration = TemplateConfiguration::get($dom_xpath);
        $template_translation = TemplateTranslation::get($dom_xpath);
        
        $content_object = new Matching();
        
        return new self($template_configuration, $content_object, $template_translation);
    }
}