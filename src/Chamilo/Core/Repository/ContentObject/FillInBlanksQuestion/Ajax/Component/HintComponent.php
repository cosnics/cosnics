<?php
namespace Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Ajax\Component;

use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Ajax\Manager;
use Chamilo\Core\Repository\ContentObject\FillInBlanksQuestion\Storage\DataClass\FillInBlanksQuestion;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @author Hans De Bisschop
 * @package repository.content_object.fill_in_blanks_question;
 */
class HintComponent extends Manager
{
    const PARAM_HINT_TYPE = 'hint_type';
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
        return array(self::PARAM_HINT_IDENTIFIER, self::PARAM_HINT_TYPE);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $identifiers = explode('_', $this->getPostDataValue(self::PARAM_HINT_IDENTIFIER));
        $type = $this->getPostDataValue(self::PARAM_HINT_TYPE);
        
        $complex_content_object_item = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class,
            $identifiers[0]);
        
        switch ($type)
        {
            case FillInBlanksQuestion::HINT_CHARACTER :
                $answer = $complex_content_object_item->get_ref_object()->get_hint_for_question($type, $identifiers[1]);
                break;
            case FillInBlanksQuestion::HINT_ANSWER :
                $answer = $complex_content_object_item->get_ref_object()->get_hint_for_question($type, $identifiers[1]);
                break;
        }
        
        $result = new JsonAjaxResult(200);
        $result->set_property('hint', $answer);
        $result->display();
    }
}
