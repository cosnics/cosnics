<?php
namespace Chamilo\Application\Survey\Rights\Publication;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

abstract class Manager extends Application
{
    const PARAM_ACTION = 'survey_rights_action';
    const PARAM_LOCATION_ENTITY_RIGHT_GROUP_ID = 'location_entity_right_group_id';
    const ACTION_MAILER = 'mailer';
    const ACTION_ADMINISTRATOR = 'administrator';
    const ACTION_INVITEE = 'invitee';
    const DEFAULT_ACTION = self :: ACTION_INVITEE;

    public static function launch($application)
    {
        parent :: launch(null, $application);
    }

    public function get_tabs($current_tab, $content)
    {
        $tabs = new DynamicVisualTabsRenderer(Utilities :: get_classname_from_namespace(__NAMESPACE__, true), $content);

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_INVITEE,
                Translation :: get('Invitee'),
                Theme :: getInstance()->getImagePath() . 'tab/' . self :: ACTION_INVITEE . '.png',
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_INVITEE)),
                ($current_tab == self :: ACTION_INVITEE ? true : false)));

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_ADMINISTRATOR,
                Translation :: get('Administrator'),
                Theme :: getInstance()->getImagePath() . 'tab/' . self :: ACTION_ADMINISTRATOR . '.png',
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ADMINISTRATOR)),
                ($current_tab == self :: ACTION_ADMINISTRATOR ? true : false)));

        $tabs->add_tab(
            new DynamicVisualTab(
                self :: ACTION_MAILER,
                Translation :: get('Mailer'),
                Theme :: getInstance()->getImagePath() . 'tab/' . self :: ACTION_MAILER . '.png',
                $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MAILER)),
                ($current_tab == self :: ACTION_MAILER ? true : false)));

        return $tabs;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE)),
                Translation :: get('BrowserComponent', array(), '\application\survey')));
        $breadcrumbtrail->add(
            new Breadcrumb(
                $this->get_url(
                    array(
                        \Chamilo\Application\Survey\Manager :: PARAM_ACTION => \Chamilo\Application\Survey\Manager :: ACTION_BROWSE_PARTICIPANTS,
                        \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID => Request :: get(
                            \Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID))),
                Translation :: get('ParticipantBrowserComponent')));
    }

    function get_parameters()
    {
        return array(\Chamilo\Application\Survey\Manager :: PARAM_PUBLICATION_ID);
    }
}
