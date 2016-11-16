<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: restorer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to restore objects.
 * This means moving objects from the recycle bin to there original
 * location.
 */
class RestorerComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = $this->getRequest()->get(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $ids);
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            $failures = 0;
            foreach ($ids as $object_id)
            {
                $object = DataManager::retrieve_by_id(ContentObject::class_name(), $object_id);
                
                if (RightsService::getInstance()->canDestroyContentObject(
                    $this->get_user(), 
                    $object, 
                    $this->getWorkspace()))
                {
                    if ($object->get_state() == ContentObject::STATE_RECYCLED)
                    {
                        $versions = $object->get_content_object_versions();
                        foreach ($versions as $version)
                        {
                            $version->set_state(ContentObject::STATE_NORMAL);
                            
                            if (! $this->repository_category_exists($version->get_parent_id()))
                            {
                                $version->set_parent_id(0);
                            }
                            
                            if ($version->update())
                            {
                                Event::trigger(
                                    'Activity', 
                                    Manager::context(), 
                                    array(
                                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_RESTORE, 
                                        Activity::PROPERTY_USER_ID => $this->get_user_id(), 
                                        Activity::PROPERTY_DATE => time(), 
                                        Activity::PROPERTY_CONTENT_OBJECT_ID => $version->get_id(), 
                                        Activity::PROPERTY_CONTENT => $version->get_title()));
                            }
                            else
                            {
                                $failures ++;
                            }
                        }
                    }
                    else
                    {
                        $failures ++;
                    }
                }
                else
                {
                    $failures ++;
                }
            }
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectNotRestored', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation::get(
                        'ObjectNotRestored', 
                        array('OBJECTS' => Translation::get('ContentObjects')), 
                        Utilities::COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectRestored', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsRestored', 
                        array('OBJECTS' => Translation::get('ContentObjects')), 
                        Utilities::COMMON_LIBRARIES);
                }
            }
            
            $this->redirect(
                $message, 
                ($failures ? true : false), 
                array(Application::PARAM_ACTION => self::ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS));
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', 
                        array('OBJECT' => Translation::get('ContentObject')), 
                        Utilities::COMMON_LIBRARIES)));
        }
    }

    public function repository_category_exists($id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class_name(), RepositoryCategory::PROPERTY_ID), 
            new StaticConditionVariable($id));
        return (DataManager::count(RepositoryCategory::class_name(), $condition) > 0);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS)), 
                Translation::get('BrowserComponent')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS)), 
                Translation::get('RepositoryManagerRecycleBinBrowserComponent')));
        $breadcrumbtrail->add_help('repository_restorer');
    }
}
