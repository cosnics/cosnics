<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Storage\DataClass\FrequentlyAskedQuestions;
use DOMXPath;

/**
 * Portfolio template parser
 *
 * @package repository\content_object\portfolio
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     * Parse the template definition to a valid Template instance
     *
     * @param DOMXPath $dom_xpath
     * @return \core\repository\content_object\portfolio\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $template_configuration = TemplateConfiguration :: get($dom_xpath);
        $template_translation = TemplateTranslation :: get($dom_xpath);

        $content_object = new FrequentlyAskedQuestions();

        return new self($template_configuration, $content_object, $template_translation);
    }
}