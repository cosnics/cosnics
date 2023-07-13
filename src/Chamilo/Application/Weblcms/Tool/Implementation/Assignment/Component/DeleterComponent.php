<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentPublicationService;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package application.weblcms.tool.assignment.php.component Deleter for assignments.
 * @author  Joris Willems <joris.willems@gmail.com>
 * @author  Alexander Van Paemel
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        if ($this->getRequest()->query->has(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))
        {
            $publication_ids =
                $this->getRequest()->query->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }
        else
        {
            $publication_ids =
                $this->getRequest()->request->get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }

        if (!is_array($publication_ids))
        {
            $publication_ids = [$publication_ids];
        }

        $failures = 0;

        foreach ($publication_ids as $pid)
        {
            /** @var ContentObjectPublication $publication */
            $publication = DataManager::retrieve_by_id(
                ContentObjectPublication::class, $pid
            );

            if (!empty($publication) && $this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication))
            {
                try
                {
                    $this->getAssignmentPublicationService()->deletePublication($publication);
                }
                catch (Exception $ex)
                {
                    $failures ++;
                }
            }
            else
            {
                $failures ++;
            }
        }
        if ($failures == 0)
        {
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation::get('ContentObjectPublicationsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation::get('ContentObjectPublicationDeleted'));
            }
        }
        else
        {
            $message = htmlentities(Translation::get('ContentObjectPublicationsNotDeleted'));
        }

        $this->redirectWithMessage(
            $message, $failures > 0,
            [\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null, 'tool_action' => null]
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentPublicationService
     */
    protected function getAssignmentPublicationService()
    {
        return $this->getService(AssignmentPublicationService::class);
    }
}
