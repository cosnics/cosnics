<?php
namespace Chamilo\Core\Repository\ContentObject\Assessment\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;

/**
 *
 * @package repository.content_object.assessment;
 */
class HintComponent extends \Chamilo\Core\Repository\ContentObject\Assessment\Ajax\Manager
{
    const PARAM_HINT_IDENTIFIER = 'hint_identifier';
    const PROPERTY_HINT = 'hint';
    const PROPERTY_ELEMENT_NAME = 'element_name';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_HINT_IDENTIFIER);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $identifiers = explode('_', $this->getPostDataValue(self::PARAM_HINT_IDENTIFIER));
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(),
            $identifiers[0]);

        $context = $complex_content_object_item->get_ref_object()->package();

        $this->getRequest()->query->set(self::PARAM_ACTION, 'Hint');

        $component = $this->getApplicationFactory()->getApplication(
            $context . '\Ajax',
            new ApplicationConfiguration($this->getRequest(), $this->getUser(), $this));
        $component->set_complex_content_object_item($complex_content_object_item);
        $component->run();
    }
}
