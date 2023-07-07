<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Tracking\Storage\DataClass\Event;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
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
        $ids = $this->getRequest()->getFromRequestOrQuery(self::PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(self::PARAM_CONTENT_OBJECT_ID, $ids);
        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = [$ids];
            }
            $failures = 0;
            foreach ($ids as $object_id)
            {
                $object = DataManager::retrieve_by_id(ContentObject::class, $object_id);

                if ($this->getWorkspaceRightsService()->canDestroyContentObject(
                    $this->get_user(), $object, $this->getWorkspace()
                ))
                {
                    if ($object->get_state() == ContentObject::STATE_RECYCLED)
                    {
                        $versions = $object->get_content_object_versions();
                        foreach ($versions as $version)
                        {
                            $version->set_state(ContentObject::STATE_NORMAL);

                            if (!$this->repository_category_exists($version->get_parent_id()))
                            {
                                $version->set_parent_id(0);
                            }

                            if ($version->update())
                            {
                                Event::trigger(
                                    'Activity', Manager::CONTEXT, [
                                        Activity::PROPERTY_TYPE => Activity::ACTIVITY_RESTORE,
                                        Activity::PROPERTY_USER_ID => $this->get_user_id(),
                                        Activity::PROPERTY_DATE => time(),
                                        Activity::PROPERTY_CONTENT_OBJECT_ID => $version->get_id(),
                                        Activity::PROPERTY_CONTENT => $version->get_title()
                                    ]
                                );
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
                        'ObjectNotRestored', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                    );
                }
                else
                {
                    $message = Translation::get(
                        'ObjectNotRestored', ['OBJECTS' => Translation::get('ContentObjects')],
                        StringUtilities::LIBRARIES
                    );
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation::get(
                        'ObjectRestored', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                    );
                }
                else
                {
                    $message = Translation::get(
                        'ObjectsRestored', ['OBJECTS' => Translation::get('ContentObjects')], StringUtilities::LIBRARIES
                    );
                }
            }

            $this->redirectWithMessage(
                $message, (bool) $failures, [Application::PARAM_ACTION => self::ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS]
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(
                    Translation::get(
                        'NoObjectSelected', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                    )
                )
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS]),
                Translation::get('BrowserComponent')
            )
        );
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS]),
                Translation::get('RepositoryManagerRecycleBinBrowserComponent')
            )
        );
    }

    public function repository_category_exists($id)
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(RepositoryCategory::class, RepositoryCategory::PROPERTY_ID),
            new StaticConditionVariable($id)
        );

        return (DataManager::count(RepositoryCategory::class, new DataClassCountParameters($condition)) > 0);
    }
}
