<?php
namespace Chamilo\Core\Group\Component;

use Chamilo\Core\Group\Form\GroupImportForm;
use Chamilo\Core\Group\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package group.lib.group_manager.component
 */
class ImporterComponent extends Manager
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

        $form = new GroupImportForm($this->get_url());

        if ($form->validate())
        {
            $success = $form->import_groups();
            $this->redirect(
                Translation::get($success ? 'GroupXMLProcessed' : 'GroupXMLNotProcessed') . '<br />' .
                     $form->get_failed_elements(), !$success,
                    array(Application::PARAM_ACTION => self::ACTION_IMPORT));
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
        $html[] = '<p>' . Translation::get('XMLMustLookLike') . ' (' . Translation::get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;';
        $html[] = '&lt;groups&gt;';
        $html[] = '    &lt;item&gt;';
        $html[] = '        <b>&lt;action&gt;A/U/D&lt;/action&gt;</b>';
        $html[] = '        <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
        $html[] = '        <b>&lt;code&gt;xxx&lt;/code&gt;</b>';
        $html[] = '        &lt;description&gt;xxx&lt;/description&gt;';
        $html[] = '        &lt;children&gt;';
        $html[] = '            &lt;item&gt;';
        $html[] = '                <b>&lt;action&gt;A/U/D&lt;/action&gt;</b>';
        $html[] = '                <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
        $html[] = '                <b>&lt;code&gt;xxx&lt;/code&gt;</b>';
        $html[] = '                &lt;description&gt;xxx&lt;/description&gt;';
        $html[] = '                &lt;children&gt;xxx&lt;/children&gt;';
        $html[] = '            &lt;/item&gt;';
        $html[] = '        &lt;/children&gt;';
        $html[] = '    &lt;/item&gt;';
        $html[] = '&lt;/groups&gt;';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . Translation::get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation::get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation::get('Add', null, StringUtilities::LIBRARIES);
        $html[] = '<br />U: ' . Translation::get('Update', null, StringUtilities::LIBRARIES);
        $html[] = '<br />D: ' . Translation::get('Delete', null, StringUtilities::LIBRARIES);
        $html[] = '</blockquote>';

        return implode(PHP_EOL, $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('group general');
    }
}
