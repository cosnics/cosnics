<?php
namespace Chamilo\Core\Repository\Ajax\Component;

use Chamilo\Core\Repository\Ajax\Manager;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\JsonAjaxResult;
use Exception;

class RenditionImplementationComponent extends Manager
{
    const PARAM_CONTENT_OBJECT_ID = 'content_object_id';

    const PARAM_FORMAT = 'format';

    const PARAM_PARAMETERS = 'parameters';

    const PARAM_SECURITY_CODE = 'security_code';

    const PARAM_VIEW = 'view';

    const PROPERTY_RENDITION = 'rendition';

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::required_parameters()
     */

    public function run()
    {
        try
        {
            /**
             *
             * @var ContentObject $object
             */
            $object = DataManager::retrieve_by_id(
                ContentObject::class, $this->getPostDataValue(self::PARAM_CONTENT_OBJECT_ID)
            );

            $security_code = $this->getPostDataValue(self::PARAM_SECURITY_CODE);
            if ($security_code != $object->calculate_security_code())
            {
                throw new NotAllowedException();
            }

            $display = ContentObjectRenditionImplementation::factory(
                $object, $this->getPostDataValue(self::PARAM_FORMAT), $this->getPostDataValue(self::PARAM_VIEW), $this
            );

            $rendition = $display->render($this->getPostDataValue(self::PARAM_PARAMETERS));
        }
        catch (NotAllowedException $ex)
        {
            $result = new JsonAjaxResult(401);
            $result->display(); // contains exit.
        }
        catch (Exception $ex)
        {
            $rendition = array('url' => null);
        }

        $result = new JsonAjaxResult(200);
        $result->set_property(self::PROPERTY_RENDITION, $rendition);
        $result->display();
    }

    /*
     * (non-PHPdoc) @see common\libraries.AjaxManager::run()
     */

    public function getRequiredPostParameters(): array
    {
        return array(
            self::PARAM_CONTENT_OBJECT_ID, self::PARAM_FORMAT, self::PARAM_VIEW, self::PARAM_SECURITY_CODE,
            self::PARAM_PARAMETERS
        );
    }
}
