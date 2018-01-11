<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Deleter for assignments.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        if (Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID))
        {
            $publication_ids = Request::get(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID);
        }
        else
        {
            $publication_ids = $_POST[\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID];
        }

        if (!is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }

        $failures = 0;

        foreach ($publication_ids as $pid)
        {
            /** @var ContentObjectPublication $publication */
            $publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class_name(),
                $pid
            );

            if (!empty($publication) && $this->is_allowed(WeblcmsRights::DELETE_RIGHT, $publication))
            {
                try
                {
                    $this->getAssignmentPublicationService()->deletePublication($publication);
                }
                catch (\Exception $ex)
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

        $this->redirect(
            $message,
            $failures > 0,
            array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_PUBLICATION_ID => null, 'tool_action' => null)
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\AssignmentPublicationService
     */
    protected function getAssignmentPublicationService()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.assignment_publication_service'
        );
    }
}
