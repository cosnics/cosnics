<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Translation\Translation;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CreatorComponent extends Manager
{
    /**
     * Runs the component
     */
    public function run()
    {
        $this->validateAccess();

        $requestManager = $this->getRequestManager();
        $contentObjectIds = $this->getContentObjectIds();
        $failures = $requestManager->handInDocumentsByIds($contentObjectIds, $this->getUser(), $this->get_course_id());

        if ($failures > 0)
        {
            $is_error_message = true;
        }
        else
        {
            $is_error_message = false;
        }

        $message = $this->get_result(
            $failures,
            count($contentObjectIds),
            'SelectedRequestNotCreated',
            'SelectedRequestsNotCreated',
            'SelectedRequestCreated',
            'SelectedRequestsCreated'
        );

        $parameters = array(\Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_BROWSE);
        $this->redirectWithMessage($message, $is_error_message, $parameters);
    }

    /**
     * Returns base requests containing the author ids
     *
     * @return array
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function getContentObjectIds()
    {
        $content_object_translation = Translation::get(
            'ContentObject',
            null,
            \Chamilo\Core\Repository\Manager::context()
        );

        $ids = $this->getRequest()->getFromPostOrUrl(self::PARAM_CONTENT_OBJECT_IDS);

        if (empty($ids))
        {
            throw new NoObjectSelectedException($content_object_translation);
        }

        if(!is_array($ids))
        {
            $ids = (array) $ids;
        }

        return $ids;
    }

}
