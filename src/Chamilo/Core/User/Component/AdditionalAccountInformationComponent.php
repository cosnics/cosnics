<?php
namespace Chamilo\Core\User\Component;

use Chamilo\Core\User\Manager;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbGenerator;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Header;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * $Id: additional_account_information.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 *
 * @package user.lib.user_manager.component
 */
class AdditionalAccountInformationComponent extends Manager
{

    private $tabs;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        Header :: get_instance()->set_section('my_account');

        $form_executer = new \Chamilo\Configuration\Form\Executer(
            $this,
            'account_fields',
            Translation :: get('AdditionalUserInformation'));
        return $form_executer->run();
    }

    public function render_header()
    {
        $html = array();

        $actions = array();
        $actions[] = self :: ACTION_VIEW_ACCOUNT;
        $actions[] = self :: ACTION_USER_SETTINGS;
        $actions[] = self :: ACTION_ADDITIONAL_ACCOUNT_INFORMATION;

        $this->tabs = new DynamicVisualTabsRenderer('account');

        foreach ($actions as $action)
        {
            $selected = ($action == self :: ACTION_ADDITIONAL_ACCOUNT_INFORMATION ? true : false);

            $label = htmlentities(
                Translation :: get(
                    (string) StringUtilities :: getInstance()->createString($action)->upperCamelize() . 'Title'));
            $link = $this->get_url(array(self :: PARAM_ACTION => $action));

            $this->tabs->add_tab(
                new DynamicVisualTab(
                    $action,
                    $label,
                    Theme :: getInstance()->getImagePath('Chamilo\Core\User\\') . 'place_' . $action . '.png',
                    $link,
                    $selected));
        }

        $html[] = parent :: render_header();
        $html[] = $this->tabs->header();
        $html[] = $this->tabs->body_header();

        return implode("\n", $html);
    }

    public function render_footer()
    {
        $html = array();

        $html[] = $this->tabs->body_footer();
        $html[] = $this->tabs->footer();
        $html[] = parent :: render_footer();

        return implode("\n", $html);
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(), Translation :: get('MyAccount')));
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
