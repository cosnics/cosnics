<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupUserImportForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @author vanpouckesven
 * @package group.lib.group_manager.component
 */
class GroupUserImporterComponent extends Manager
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

        $form = new GroupUserImportForm($this->get_url());

        if ($form->validate())
        {
            $success = $form->import_group_users();
            $this->redirect(
                Translation::get($success ? 'GroupUserCSVProcessed' : 'GroupUserCSVNotProcessed') . '<br />' .
                     $form->get_failed_elements(), !$success,
                    array(Application::PARAM_ACTION => self::ACTION_IMPORT_GROUP_USERS));
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->display_extra_information();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function display_extra_information()
    {
        $html = [];
        $html[] = '<p>' . Translation::get('CSVMustLookLike') . ' (' . Translation::get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>group_code</b>;<b>username</b>';
        $html[] = 'A;Chamilo;admin';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . Translation::get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation::get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation::get('Add', null, StringUtilities::LIBRARIES);
        $html[] = '<br />D: ' . Translation::get('Delete', null, StringUtilities::LIBRARIES);
        $html[] = '</blockquote>';

        return implode(PHP_EOL, $html);
    }
}
