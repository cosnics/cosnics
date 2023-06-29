<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package group.lib.group_manager.component
 */
class EditorComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (!$this->get_user()->isPlatformAdmin())
        {
            throw new NotAllowedException();
        }
        $id = Request::get(self::PARAM_GROUP_ID);
        $this->set_parameter(self::PARAM_GROUP_ID, $id);

        if ($id)
        {
            $group = $this->retrieve_group($id);

            if (!$this->get_user()->isPlatformAdmin())
            {
                throw new NotAllowedException();
            }

            $form = new GroupForm(
                GroupForm::TYPE_EDIT, $group, $this->get_url([self::PARAM_GROUP_ID => $id]), $this->get_user()
            );

            if ($form->validate())
            {
                $success = $form->update_group();
                $group = $form->get_group();
                $message = $success ? Translation::get(
                    'ObjectUpdated', ['OBJECT' => Translation::get('Group')], StringUtilities::LIBRARIES
                ) : Translation::get(
                    'ObjectNotUpdated', ['OBJECT' => Translation::get('Group')], StringUtilities::LIBRARIES
                );

                $this->redirectWithMessage(
                    $message, !$success, [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => $group->get_id()
                    ]
                );
            }
            else
            {
                $html = [];

                $html[] = $this->render_header();
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(
                htmlentities(Translation::get('NoObjectSelected', null, StringUtilities::LIBRARIES))
            );
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url([Application::PARAM_ACTION => self::ACTION_BROWSE_GROUPS]),
                Translation::get('BrowserComponent')
            )
        );
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    [
                        Application::PARAM_ACTION => self::ACTION_VIEW_GROUP,
                        self::PARAM_GROUP_ID => Request::get(self::PARAM_GROUP_ID)
                    ]
                ), Translation::get('ViewerComponent')
            )
        );
    }

    // public function getAdditionalParameters(array $additionalParameters = []): array
    // {
    // $additionalParameters[] = self::PARAM_GROUP_ID;
    // return parent::getAdditionalParameters($additionalParameters);
    // }
}
