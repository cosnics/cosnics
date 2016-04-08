<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfigurationParser;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\MultipleChoice\Storage\DataClass\MultipleChoice;
use DOMXPath;

/**
 *
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class TemplateConfiguration extends \Chamilo\Core\Repository\Common\Template\TemplateConfiguration implements 
    TemplateConfigurationParser
{
    const ACTION_SORT = 'sort';

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return TemplateConfiguration
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $template_configuration = new self();
        
        $template_configuration->set_configuration(
            MultipleChoice :: PROPERTY_ANSWER_TYPE, 
            TemplateConfiguration :: ACTION_EDIT, 
            (boolean) $dom_xpath->query('/template/properties/answer_type')->item(0)->getAttribute(
                TemplateConfiguration :: ACTION_EDIT));
        $template_configuration->set_configuration(
            MultipleChoice :: PROPERTY_DISPLAY_TYPE, 
            TemplateConfiguration :: ACTION_EDIT, 
            (boolean) $dom_xpath->query('/template/properties/display_type')->item(0)->getAttribute(
                TemplateConfiguration :: ACTION_EDIT));
        $template_configuration->set_configuration(
            MultipleChoice :: PROPERTY_QUESTION, 
            TemplateConfiguration :: ACTION_EDIT, 
            (boolean) $dom_xpath->query('/template/properties/question')->item(0)->getAttribute(
                TemplateConfiguration :: ACTION_EDIT));
        $template_configuration->set_configuration(
            MultipleChoice :: PROPERTY_INSTRUCTION, 
            TemplateConfiguration :: ACTION_EDIT, 
            (boolean) $dom_xpath->query('/template/properties/instruction')->item(0)->getAttribute(
                TemplateConfiguration :: ACTION_EDIT));
        $template_configuration->set_configuration(
            MultipleChoice :: PROPERTY_OPTIONS, 
            TemplateConfiguration :: ACTION_EDIT, 
            (boolean) $dom_xpath->query('/template/properties/options')->item(0)->getAttribute(
                TemplateConfiguration :: ACTION_EDIT));
        $template_configuration->set_configuration(
            MultipleChoice :: PROPERTY_OPTIONS, 
            TemplateConfiguration :: ACTION_SORT, 
            (boolean) $dom_xpath->query('/template/properties/options')->item(0)->getAttribute(
                TemplateConfiguration :: ACTION_SORT));
        
        return $template_configuration;
    }
}