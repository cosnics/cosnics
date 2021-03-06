<?php
namespace Chamilo\Core\Help\Component;

use Chamilo\Core\Help\Form\HelpItemForm;
use Chamilo\Core\Help\Manager;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package help.lib.help_manager.component
 */
class UpdaterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $id = Request::Get(Manager::PARAM_HELP_ITEM);
        $this->set_parameter(Manager::PARAM_HELP_ITEM, $id);

        if ($id)
        {
            $help_item = DataManager::retrieve_by_id(HelpItem::class_name(), $id);

            $form = new HelpItemForm($help_item, $this->get_url(array(Manager::PARAM_HELP_ITEM => $id)));

            if ($form->validate())
            {
                $success = $form->update_help_item();
                $help_item = $form->get_help_item();
                $this->redirect(
                    Translation::get($success ? 'HelpItemUpdated' : 'HelpItemNotUpdated'),
                    ($success ? false : true),
                    array(Application::PARAM_ACTION => Manager::ACTION_BROWSE_HELP_ITEMS));
            }
            else
            {
                $html = array();

                $html[] = $this->render_header();
                $html[] = '<h4>' . Translation::get('UpdateItem') . ': ' . $help_item->get_context() . ' - ' .
                     $help_item->get_identifier() . '</h4>';
                $html[] = $form->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation::get('NoHelpItemSelected')));
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(array(Application::PARAM_ACTION => Manager::ACTION_BROWSE_HELP_ITEMS)),
                Translation::get('HelpManagerBrowserComponent')));
        $breadcrumbtrail->add_help('help_updater');
    }
}
