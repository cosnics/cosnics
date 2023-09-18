<?php
namespace Chamilo\Application\Weblcms\Tool\Action\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Action\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package application.lib.weblcms.tool.component
 */
class MoverComponent extends Manager implements BreadcrumbLessComponentInterface
{

    /**
     * Executes this controller
     *
     * @throws \libraries\architecture\exceptions\NotAllowedException
     */
    public function run()
    {
        $publication_id =
            $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        $publication = DataManager::retrieve_by_id(
            ContentObjectPublication::class, $publication_id
        );

        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT, $publication))
        {
            throw new NotAllowedException();
        }

        $move = $this->get_parent()->get_move_direction();

        if ($publication->move($move))
        {
            $message = htmlentities(Translation::get('ContentObjectPublicationMoved'));
        }

        $this->redirectWithMessage(
            $message, false, [
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => null,
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE => $this->getRequest()->query->get(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSER_TYPE
                ),
                \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE => $this->getRequest(
                )->query->get(
                    \Chamilo\Application\Weblcms\Tool\Manager::PARAM_BROWSE_PUBLICATION_TYPE
                )
            ]
        );
    }
}
