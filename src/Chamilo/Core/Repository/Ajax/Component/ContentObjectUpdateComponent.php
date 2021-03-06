<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

/**
 *
 * @package repository
 */
class ContentObjectUpdateComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    const PARAM_MODIFICATION_DATE = 'modification_date';
    const PROPERTY_ALLOW_UPDATE = 'allow_update';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(self::PARAM_CONTENT_OBJECT_ID, self::PARAM_MODIFICATION_DATE);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $content_object_id = $this->getPostDataValue(self::PARAM_CONTENT_OBJECT_ID);
        $content_object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), 
            $content_object_id);
        $modification_date = $this->getPostDataValue(self::PARAM_MODIFICATION_DATE);
        $allow_update = ($modification_date >= $content_object->get_modification_date());
        
        $result = new JsonAjaxResult(200);
        $result->set_property(self::PROPERTY_ALLOW_UPDATE, $allow_update);
        $result->display();
    }
}
