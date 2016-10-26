<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\JsonAjaxResult;

class RenditionImplementationComponent extends \Chamilo\Core\Repository\Ajax\Manager
{
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';
    const PARAM_FORMAT = 'format';
    const PARAM_VIEW = 'view';
    const PARAM_PARAMETERS = 'parameters';
    const PROPERTY_RENDITION = 'rendition';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */
    public function getRequiredPostParameters()
    {
        return array(
            self :: PARAM_CONTENT_OBJECT_ID,
            self :: PARAM_FORMAT,
            self :: PARAM_VIEW,
            self :: PARAM_PARAMETERS);
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */
    public function run()
    {
        $object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_by_id(
            ContentObject :: class_name(),
            $this->getPostDataValue(self :: PARAM_CONTENT_OBJECT_ID));
        $display = ContentObjectRenditionImplementation :: factory(
            $object,
            $this->getPostDataValue(self :: PARAM_FORMAT),
            $this->getPostDataValue(self :: PARAM_VIEW),
            $this);

        $result = new JsonAjaxResult(200);
        $result->set_property(
            self :: PROPERTY_RENDITION,
            $display->render($this->getPostDataValue(self :: PARAM_PARAMETERS)));
        $result->display();
    }
}
