<?php
namespace Chamilo\Core\Lynx\Manager\Component;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Core\Lynx\Manager\Manager;
use Chamilo\Core\Lynx\Manager\PackageDisplay;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

class ViewerComponent extends Manager implements DelegateComponent
{

    private $context;

    private $registration;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->context = Request :: get(self :: PARAM_CONTEXT);
        $this->registration = \Chamilo\Configuration\Storage\DataManager :: get_registration($this->context);

        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(
                null,
                Translation :: get(
                    'ViewingPackage',
                    array('PACKAGE' => Translation :: get('TypeName', null, $this->context)))));

        $display = new PackageDisplay($this);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->get_action_bar()->as_html();
        $html[] = $display->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->get_registration() instanceof Registration)
        {
            if ($this->get_registration()->is_active())
            {
                if (! is_subclass_of(
                    $this->get_registration()->get_context() . '\Deactivator',
                    'Chamilo\Configuration\Package\NotAllowed'))
                {
                    $action_bar->add_common_action(
                        new ToolbarItem(
                            Translation :: get('Deactivate', array(), Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getImagePath('Chamilo\Core\Lynx\Manager', 'Action/Deactivate'),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_DEACTIVATE,
                                    self :: PARAM_CONTEXT => $this->context))));
                }
            }
            else
            {
                if (! is_subclass_of(
                    $this->get_registration()->get_context() . '\Activator',
                    'Chamilo\Configuration\Package\NotAllowed'))
                {
                    $action_bar->add_common_action(
                        new ToolbarItem(
                            Translation :: get('Activate', array(), Utilities :: COMMON_LIBRARIES),
                            Theme :: getInstance()->getImagePath('Chamilo\Core\Lynx\Manager', 'Action/Activate'),
                            $this->get_url(
                                array(
                                    self :: PARAM_ACTION => self :: ACTION_ACTIVATE,
                                    self :: PARAM_CONTEXT => $this->context))));
                }
            }
        }
        else
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('Install', array(), Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Lynx\Manager', 'Action/Install'),
                    $this->get_url(
                        array(self :: PARAM_ACTION => self :: ACTION_INSTALL, self :: PARAM_CONTEXT => $this->context)),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                    true));
        }

        return $action_bar;
    }

    public function get_context()
    {
        return $this->context;
    }

    public function get_registration()
    {
        return $this->registration;
    }
}
