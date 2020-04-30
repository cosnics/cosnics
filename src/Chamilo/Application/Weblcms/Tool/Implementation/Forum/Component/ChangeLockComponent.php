<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Forum\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Forum\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package repository.lib.complex_builder.forum.component
 */
class ChangeLockComponent extends Manager
{

    public function run()
    {
        $this->publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);

        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class,
            $this->publication_id);

        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $object = $publication->get_content_object();
        if ($object->invert_locked())
        {
            $succes = true;
            $message = Translation::get('LockChanged');
        }
        else
        {
            $message = Translation::get('LockNotChanged');
        }

        $params = array();
        $params[self::PARAM_ACTION] = self::ACTION_BROWSE_FORUMS;

        $this->redirect($message, ! $succes, $params);
    }
}
