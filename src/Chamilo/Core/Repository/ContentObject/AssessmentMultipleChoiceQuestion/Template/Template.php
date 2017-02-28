<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Template;

use Chamilo\Core\Repository\Common\Template\TemplateConfiguration;
use Chamilo\Core\Repository\Common\Template\TemplateParser;
use Chamilo\Core\Repository\Common\Template\TemplateTranslation;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestion;
use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Storage\DataClass\AssessmentMultipleChoiceQuestionOption;
use Chamilo\Libraries\Platform\Translation;
use DOMXPath;

class Template extends \Chamilo\Core\Repository\Common\Template\Template implements TemplateParser
{

    /**
     *
     * @param DOMXPath $dom_xpath
     *
     * @return \core\repository\content_object\survey_multiple_choice_question\Template
     */
    public static function parse(DOMXPath $dom_xpath)
    {
        $language = Translation::getInstance()->getLanguageIsocode();

        $template_configuration = TemplateConfiguration::get($dom_xpath);
        $template_translation = TemplateTranslation::get($dom_xpath);

        $content_object = new AssessmentMultipleChoiceQuestion();

        $content_object->set_answer_type($dom_xpath->query('/template/properties/answer_type')->item(0)->nodeValue);

        $options = $dom_xpath->query('/template/properties/options/option');

        /** @var \DOMElement $option */
        foreach ($options as $option)
        {
            $value = $template_translation->translate($language, $option->nodeValue);

            $question_option = new AssessmentMultipleChoiceQuestionOption(
                $value, (bool) $option->getAttribute('correct'), $option->getAttribute('score'),
                $option->getAttribute('feedback')
            );

            $content_object->add_option($question_option);
        }

        return new self($template_configuration, $content_object, $template_translation);
    }
}