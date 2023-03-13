<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\DataManager\DataManager;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package Chamilo\Core\Repository\Component$ReverterComponent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Dieter de Neef
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ReverterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $ids = Request::get(self::PARAM_CONTENT_OBJECT_ID);
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

                if (!$this->getWorkspaceRightsService()->canEditContentObject(
                    $this->get_user(), $object, $this->getWorkspace()
                ))
                {
                    throw new NotAllowedException();
                }

                if (\Chamilo\Core\Repository\Storage\DataManager::content_object_revert_allowed($object))
                {
                    $object->version();
                }
                else
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                $message = Translation::get(
                    'ObjectNotReverted', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                );
            }
            else
            {
                $message = Translation::get(
                    'ObjectReverted', ['OBJECT' => Translation::get('ContentObject')], StringUtilities::LIBRARIES
                );
            }
            $this->redirectWithMessage(
                $message, (bool) $failures, [Application::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS]
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

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_CONTENT_OBJECTS]),
                Translation::get('BrowserComponent')
            )
        );
    }
}
