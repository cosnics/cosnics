<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Service\RightsService;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a content object from the users repository.
 */
class DeleterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = $this->getRequest()->get(self::PARAM_CONTENT_OBJECT_ID);

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $failures = 0;
            $delete_version = Request::get(self::PARAM_DELETE_VERSION);
            $permanent = Request::get(self::PARAM_DELETE_PERMANENTLY);
            $recycled = Request::get(self::PARAM_DELETE_RECYCLED);

            foreach ($ids as $object_id)
            {
                $object = DataManager::retrieve_by_id(ContentObject::class_name(), $object_id);
                $unlinkAllowed = $this->getPublicationAggregator()->canContentObjectBeUnlinked($object);

                if (RightsService::getInstance()->canDestroyContentObject(
                    $this->get_user(),
                    $object,
                    $this->getWorkspace()))
                {
                    if ($delete_version)
                    {
                        if (\Chamilo\Core\Repository\Storage\DataManager::content_object_deletion_allowed(
                            $object,
                            'version'))
                        {
                            if (! $object->delete(true))
                            {
                                $failures ++;
                            }
                            else
                            {
                                Event::trigger(
                                    'Activity',
                                    Manager::context(),
                                    array(
                                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_DELETED,
                                        Activity::PROPERTY_USER_ID => $this->get_user_id(),
                                        Activity::PROPERTY_DATE => time(),
                                        Activity::PROPERTY_CONTENT_OBJECT_ID => $object->get_id(),
                                        Activity::PROPERTY_CONTENT => $object->get_title()));
                            }
                        }
                        else
                        {
                            $failures ++;
                        }
                    }
                    else
                    {
                        if (\Chamilo\Core\Repository\Storage\DataManager::content_object_deletion_allowed($object))
                        {
                            if ($permanent)
                            {
                                $versions = $object->get_content_object_versions();
                                foreach ($versions as $version)
                                {
                                    if (! $version->delete())
                                    {
                                        $failures ++;
                                    }
                                    else
                                    {
                                        Event::trigger(
                                            'Activity',
                                            Manager::context(),
                                            array(
                                                Activity::PROPERTY_TYPE => Activity::ACTIVITY_DELETED,
                                                Activity::PROPERTY_USER_ID => $this->get_user_id(),
                                                Activity::PROPERTY_DATE => time(),
                                                Activity::PROPERTY_CONTENT_OBJECT_ID => $version->get_id(),
                                                Activity::PROPERTY_CONTENT => $version->get_title()));
                                    }
                                }
                            }
                            elseif ($recycled)
                            {
                                if(!$unlinkAllowed)
                                {
                                    $failures ++;
                                    continue;
                                }

                                $versions = $object->get_content_object_versions();
                                foreach ($versions as $version)
                                {
                                    if (! $version->recycle())
                                    {
                                        $failures ++;
                                    }
                                    else
                                    {
                                        Event::trigger(
                                            'Activity',
                                            Manager::context(),
                                            array(
                                                Activity::PROPERTY_TYPE => Activity::ACTIVITY_RECYCLE,
                                                Activity::PROPERTY_USER_ID => $this->get_user_id(),
                                                Activity::PROPERTY_DATE => time(),
                                                Activity::PROPERTY_CONTENT_OBJECT_ID => $version->get_id(),
                                                Activity::PROPERTY_CONTENT => $version->get_title()));
                                    }
                                }
                            }
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

            if ($delete_version)
            {
                if ($failures)
                {
                    $message = 'SelectedVersionNotDeleted';
                }
                else
                {
                    $message = 'SelectedVersionDeleted';
                }
            }
            else
            {
                if ($failures)
                {
                    if (count($ids) == 1)
                    {
                        $message = 'ObjectNot' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                        $parameter = array('OBJECT' => Translation::get('ContentObject'));
                    }
                    elseif (count($ids) > $failures)
                    {
                        $message = 'SomeObjectsNot' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                        $parameter = array('OBJECTS' => Translation::get('ContentObjects'));
                    }
                    else
                    {
                        $message = 'ObjectsNot' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                        $parameter = array('OBJECTS' => Translation::get('ContentObjects'));
                    }
                }
                else
                {
                    if (count($ids) == 1)
                    {
                        $message = 'Object' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                        $parameter = array('OBJECT' => Translation::get('ContentObject'));
                    }
                    else
                    {
                        $message = 'Objects' . ($permanent ? 'Deleted' : 'MovedToRecycleBin');
                        $parameter = array('OBJECTS' => Translation::get('ContentObjects'));
                    }
                }
            }

            $parameters = array();
            $parameters[Application::PARAM_ACTION] = ($permanent ? self::ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS : self::ACTION_BROWSE_CONTENT_OBJECTS);

            $this->redirect(
                Translation::get($message, $parameter, Utilities::COMMON_LIBRARIES),
                $failures > 0,
                $parameters);
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectSelected', null, Utilities::COMMON_LIBRARIES)));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS)),
                Translation::get('BrowserComponent')));
        $breadcrumbtrail->add_help('repository_deleter');
    }

    public function get_additional_parameters()
    {
        return parent::get_additional_parameters(
            array(
                self::PARAM_CONTENT_OBJECT_ID,
                self::PARAM_DELETE_VERSION,
                self::PARAM_DELETE_PERMANENTLY,
                self::PARAM_DELETE_RECYCLED));
    }
}
