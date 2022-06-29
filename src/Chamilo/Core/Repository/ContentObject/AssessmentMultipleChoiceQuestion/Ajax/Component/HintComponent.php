<?php
namespace Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\AssessmentMultipleChoiceQuestion\Ajax\Manager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @author Hans De Bisschop
 * @package repository.content_object.assessment_multiple_choice_question;
 */
class HintComponent extends Manager
{
    const PARAM_HINT_IDENTIFIER = 'hint_identifier';
    const PROPERTY_HINT = 'hint';
    const PROPERTY_ELEMENT_NAME = 'element_name';

    /**
     *
     * @var ComplexContentObjectItem
     */
    private $complex_content_object_item;

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters(): array
    {
        return array(self::PARAM_HINT_IDENTIFIER);
    }

    /**
     * Set a ComplexContentObjectItem
     * 
     * @param ComplexContentObjectItem $complex_content_object_item
     */
    public function set_complex_content_object_item($complex_content_object_item)
    {
        $this->complex_content_object_item = $complex_content_object_item;
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $result = new JsonAjaxResult(200);
        $result->set_property(self::PROPERTY_HINT, $this->complex_content_object_item->get_ref_object()->get_hint());
        $result->display();
    }
}
