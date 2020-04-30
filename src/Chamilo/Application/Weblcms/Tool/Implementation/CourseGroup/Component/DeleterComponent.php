<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application.lib.weblcms.tool.course_group.component
 */
class DeleterComponent extends Manager
{

    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::DELETE_RIGHT))
        {
            throw new NotAllowedException();
        }

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

        foreach ($publication_ids as $pid)
        {
            if ($publication = \Chamilo\Application\Weblcms\Storage\DataManager::retrieve_by_id(
                ContentObjectPublication::class,
                $pid
            ))
            {
                $publication->delete();
            }
        }

        $ids = $this->getCourseGroupFromRequest();

        if ($ids)
        {
            if (!is_array($ids))
            {
                $ids = array($ids);
            }

            // Make the course group deletable
            foreach ($ids as $group_id)
            {
                $cg = DataManager::retrieve_by_id(CourseGroup::class, $group_id);

                if (!$cg instanceof CourseGroup)
                {
                    continue;
                }

                $this->getCourseGroupDecoratorsManager()->deleteGroup($cg, $this->getUser());

                $cg->delete();
            }

            $message = Translation::get(
                'ObjectDeleted',
                array('OBJECT' => Translation::get('CourseGroup')),
                Utilities::COMMON_LIBRARIES
            );
            $this->redirect($message, '', array('course_group' => null, self::PARAM_ACTION => null));
        }

        $this->redirect($message, '', array('course_group' => null, self::PARAM_ACTION => null));
    }

    /**
     * Retrieves the course group from the requests.
     * First checks the POST parameter (for table actions) and then
     * fall back to the GET parameter
     *
     * @return int
     */
    protected function getCourseGroupFromRequest()
    {
        $key = self::PARAM_COURSE_GROUP;
        $request = $this->getRequest();

        if (false !== $result = $request->request->get($key, false))
        {
            return $result;
        }

        if (false !== $result = $request->query->get($key, false))
        {
            return $result;
        }

        return null;
    }
}
