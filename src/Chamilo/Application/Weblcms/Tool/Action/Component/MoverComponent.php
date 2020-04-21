<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.lib.weblcms.tool.component
 */
class MoverComponent extends Manager implements DelegateComponent
{

    /**
     * Executes this controller
     *
     * @throws \libraries\architecture\exceptions\NotAllowedException
     */
    public function run()
    {
        $publication_id = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class_name(),
            $publication_id);

        if (! $this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $move = $this->get_parent()->get_move_direction();

        if ($publication->move($move))
        {
            $message = htmlentities(Translation::get('ContentObjectPublicationMoved'));
        }

        $this->redirect(
            $message,
            false,
            array(
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE => Request::get(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE),
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => Request::get(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE)));
    }
}
