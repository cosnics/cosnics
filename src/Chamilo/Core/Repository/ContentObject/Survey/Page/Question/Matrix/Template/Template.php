<?php
namespace Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\Matrix;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\MatrixMatch;
use Chamilo\Core\Repository\ContentObject\Survey\Page\Question\Matrix\Storage\DataClass\MatrixOption;
use Chamilo\Libraries\Platform\Translation;
use DOMXPath;

class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     * @return \repository\content_object\survey_matrix_question\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $language = Translation :: getInstance()->getLanguageIsocode();
        
        $template_configuration = TemplateConfiguration :: get($dom_xpath);
        $template_translation = TemplateTranslation :: get($dom_xpath);
        
        $content_object = new Matrix();
        
        $content_object->set_matrix_type($dom_xpath->query('/template/properties/matrix_type')->item(0)->nodeValue);
        $content_object->set_question(
            $template_translation->translate(
                $language,
                $dom_xpath->query('/template/properties/question')->item(0)->nodeValue));
        $content_object->set_instruction(
            $template_translation->translate(
                $language,
                $dom_xpath->query('/template/properties/instruction')->item(0)->nodeValue));
        
        $options = $dom_xpath->query('/template/properties/options/option');
        
        foreach ($options as $option)
        {
            $question_option = new MatrixOption();
            $question_option->set_value($template_translation->translate($language, $option->nodeValue));
            $question_option->set_display_order($option->getAttribute('display_order'));
        
            $content_object->add_option($question_option);
        }
        
        $matches = $dom_xpath->query('/template/properties/matches/match');
        
        foreach ($matches as $match)
        {
            $question_match = new MatrixMatch();
            $question_match->set_value($template_translation->translate($language, $option->nodeValue));
            $question_match->set_display_order($option->getAttribute('display_order'));
        
            $content_object->add_match($question_match);
        }
        
        return new self($template_configuration, $content_object, $template_translation);
    }
}