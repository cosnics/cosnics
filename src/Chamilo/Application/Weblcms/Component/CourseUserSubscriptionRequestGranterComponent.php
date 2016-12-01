<?php
namespace Chamilo\Application\Weblcms\Component;

use Chamilo\Application\Weblcms\Course\Storage\DataManager as CourseDataManager;
use Chamilo\Application\Weblcms\Manager;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseRequest;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Class CourseUserSubscriptionRequestGranterComponent
 */
class CourseUserSubscriptionRequestGranterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(Manager::context(), 'ManageCourses');
        
        $requestIds = $this->getRequest()->get(Manager::PARAM_REQUEST);
        
        if (empty($requestIds))
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', 
                        array('OBJECT' => Translation::get('Request')), 
                        Utilities::COMMON_LIBRARIES)));
        }
        
        if (! is_array($requestIds))
        {
            $requestIds = array($requestIds);
        }
        
        $failures = 0;
        
        foreach ($requestIds as $requestId)
        {
            /**
             *
             * @var CourseRequest $request
             */
            $request = DataManager::retrieve_by_id(CourseRequest::class_name(), (int) $requestId);
            if (! CourseDataManager::subscribe_user_to_course($request->get_course_id(), '5', $request->get_user_id()))
            {
                $failures ++;
            }
            else
            {
                $request->set_decision(CourseRequest::ALLOWED_DECISION);
                $request->set_decision_date(time());
                
                if (! $request->update())
                {
                    $failures ++;
                }
            }
        }
        
        if ($failures)
        {
            $message = 'ObjectsNotGranted';
            $parameter = array('OBJECTS' => Translation::get('Requests'));
        }
        else
        {
            $message = 'ObjectsGranted';
            $parameter = array('OBJECTS' => Translation::get('Requests'));
        }
        
        $this->redirect(
            Translation::getInstance()->getTranslation($message, $parameter, Utilities::COMMON_LIBRARIES), 
            ($failures ? true : false), 
            array(self::PARAM_ACTION => self::ACTION_ADMIN_REQUEST_BROWSER));
    }
}
