<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Core\Repository\ContentObject\Forum\Storage\DataManager as ForumDataManager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;

/**
 * Class responsible for the creation of forum subscribes at application level.
 *
 * @author Mattias De Pauw - Hogeschool Gent
 * @author Maarten Volckaert - Hogeschool Gent
 */
class ForumSubscribeComponent extends Manager
{

    public function run()
    {
        $this->publication_id =
            $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $this->set_parameter(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID, $this->publication_id);

        $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
            ContentObjectPublication::class, $this->publication_id
        );

        if (!$this->is_allowed(WeblcmsRights::VIEW_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $object = DataManager::retrieve_by_id(
            ContentObject::class, $this->getRequest()->query->get(self::PARAM_FORUM_ID)
        );

        $succes = ForumDataManager::create_subscribe($this->get_user_id(), $object->get_id());
        if ($succes)
        {
            $message = Translation::get(
                'SuccesSubscribe', null, ContentObject::get_content_object_type_namespace('Forum')
            );
        }
        else
        {
            $message = Translation::get(
                'UnSuccesSubscribe', null, ContentObject::get_content_object_type_namespace('Forum')
            );
        }
        $this->redirectWithMessage(
            $message, !$succes, [self::PARAM_ACTION => self::ACTION_BROWSE], [self::PARAM_PUBLICATION_ID]
        );
    }
}
