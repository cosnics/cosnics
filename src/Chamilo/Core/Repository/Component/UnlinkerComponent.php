<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.repository_manager.component
 */
class UnlinkerComponent extends Manager
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
                $unlinkAllowed = $this->getPublicationAggregator()->canContentObjectBeUnlinked($object);

                if (!$unlinkAllowed)
                {
                    $failures ++;
                    continue;
                }

                if ($this->getWorkspaceRightsService()->canDestroyContentObject(
                    $this->get_user(), $object, $this->getWorkspace()
                ))
                {
                    $versions = $object->get_content_object_versions();

                    foreach ($versions as $version)
                    {
                        if (!$version->delete_links())
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

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotUnlinked';
                    $parameter = ['OBJECT' => Translation::get('ContentObject')];
                }
                else
                {
                    $message = 'ObjectsNotUnlinked';
                    $parameter = ['OBJECTS' => Translation::get('ContentObjects')];
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectUnlinked';
                    $parameter = ['OBJECT' => Translation::get('ContentObject')];
                }
                else
                {
                    $message = 'ObjectsUnlinked';
                    $parameter = ['OBJECTS' => Translation::get('ContentObjects')];
                }
            }

            if (count($ids) == 1)
            {
                $parameters = [
                    Application::PARAM_ACTION => self::ACTION_VIEW_CONTENT_OBJECTS,
                    self::PARAM_CONTENT_OBJECT_ID => $ids[0]
                ];
            }
            else
            {
                $parameters = [Application::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS];
            }

            $this->redirectWithMessage(
                Translation::get($message, $parameter, StringUtilities::LIBRARIES), (bool) $failures, $parameters
            );
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectSelected', null, StringUtilities::LIBRARIES))
            );
        }
    }

    public function addAdditionalBreadcrumbs(BreadcrumbTrail $breadcrumbtrail): void
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS]),
                Translation::get('BrowserComponent')
            )
        );
    }
}
