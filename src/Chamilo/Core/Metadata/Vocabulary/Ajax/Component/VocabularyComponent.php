<?php
namespace Chamilo\Core\Metadata\Vocabulary\Ajax\Component;

use Chamilo\Core\Metadata\Service\EntityService;
use Chamilo\Core\Metadata\Storage\DataClass\Element;
use Chamilo\Libraries\Storage\DataManager\DataManager;

/**
 *
 * @package Chamilo\Core\User\Ajax
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class VocabularyComponent extends \Chamilo\Core\Metadata\Vocabulary\Ajax\Manager
{
    const PARAM_ELEMENT_ID = 'elementId';
    const PARAM_SCHEMA_ID = 'schemaId';
    const PARAM_SCHEMA_INSTANCE_ID = 'schemaInstanceId';

    public function run()
    {
        $elementId = $this->getPostDataValue(self :: PARAM_ELEMENT_ID);
        $schemaId = $this->getPostDataValue(self :: PARAM_SCHEMA_ID);
        $schemaInstanceId = $this->getPostDataValue(self :: PARAM_SCHEMA_INSTANCE_ID);

        $element = DataManager :: retrieve_by_id(Element :: class_name(), $elementId);

        $options = array();
        $entityService = new EntityService();
        $vocabularyItems = $entityService->getVocabularyByElementIdAndUserId($element, $this->get_user());

        while ($vocabularyItem = $vocabularyItems->next_result())
        {
            $item = new \stdClass();
            $item->id = $vocabularyItem->get_id();
            $item->value = $vocabularyItem->get_value();

            $options[] = $item;
        }

        header('Content-type: application/json');
        echo json_encode($options);
    }

    /**
     * Get an array of parameters which should be set for this call to work
     *
     * @return array
     */
    public function getRequiredPostParameters()
    {
        return array(self :: PARAM_ELEMENT_ID, self :: PARAM_SCHEMA_ID, self :: PARAM_SCHEMA_INSTANCE_ID);
    }
}