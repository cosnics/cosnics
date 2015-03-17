<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass\Order;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Order\Storage\DataClass\OrderOption;
use Chamilo\Libraries\Platform\Translation;
use DOMXPath;

class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return \repository\content_object\survey_order_question\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $language = Translation :: get_instance()->get_language();
        
        $template_configuration = TemplateConfiguration :: get($dom_xpath);
        $template_translation = TemplateTranslation :: get($dom_xpath);
        
        $content_object = new Order();
        
        $content_object->set_display_type($dom_xpath->query('/template/properties/display_type')->item(0)->nodeValue);
        $content_object->set_order_limit($dom_xpath->query('/template/properties/order_limit')->item(0)->nodeValue);
        $content_object->set_question(
            $template_translation->translate(
                $language, 
                $dom_xpath->query('/template/properties/question')->item(0)->nodeValue));
        $content_object->set_instruction(
            $template_translation->translate(
                $language, 
                $dom_xpath->query('/template/properties/instruction')->item(0)->nodeValue));
        
        $options = $dom_xpath->query('/template/properties/options/option');
        $template_options = array();
        
        foreach ($options as $option)
        {
            $question_option = new OrderOption();
            $question_option->set_value($template_translation->translate($language, $option->nodeValue));
            $question_option->set_display_order($option->getAttribute('display_order'));
            
            $content_object->add_option($question_option);
        }
        
        return new self($template_configuration, $content_object, $template_translation);
    }
}