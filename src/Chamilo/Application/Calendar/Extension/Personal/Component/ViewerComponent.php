<?php
namespace Chamilo\Application\Calendar\Extension\Personal\Component;

use Chamilo\Application\Calendar\Extension\Personal\Manager;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataClass\Publication;
use Chamilo\Application\Calendar\Extension\Personal\Storage\DataManager;
use Chamilo\Core\Group\Storage\DataClass\Group;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     *
     * @var Publication
     */
    private $publication;

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $id = $this->getRequest()->query->get(Manager :: PARAM_PUBLICATION_ID);

        if ($id)
        {
            if (! $this->can_view())
            {
                throw new NotAllowedException();
            }

            $output = $this->get_publication_as_html();

            $html = array();

            $html[] = $this->render_header();
            $html[] = $this->getButtonToolbarRenderer()->render() . '<br />';
            $html[] = '<div id="action_bar_browser">';
            $html[] = $output;
            $html[] = '</div>';
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
        else
        {
            return $this->display_error_page(htmlentities(Translation :: get('NoEventSelected')));
        }
    }

    public function getPublication()
    {
        if (! isset($this->publication))
        {
            $id = $this->getRequest()->query->get(Manager :: PARAM_PUBLICATION_ID);
            $this->publication = DataManager :: retrieve_by_id(Publication :: class_name(), $id);
        }

        return $this->publication;
    }

    /**
     *
     * @return boolean
     */
    public function can_view()
    {
        $user = $this->get_user();

        $is_target = $this->getPublication()->is_target($user);
        $is_publisher = ($this->getPublication()->get_publisher() == $user->get_id());
        $is_platform_admin = $user->is_platform_admin();

        if (! $is_target && ! $is_publisher && ! $is_platform_admin)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     *
     * @return string
     */
    public function get_publication_as_html()
    {
        $content_object = $this->getPublication()->get_publication_object();
        $content_object_properties = $content_object->get_properties();
        BreadcrumbTrail :: get_instance()->add(
            new Breadcrumb(null, $content_object_properties['default_properties']['title']));

        $html = array();

        $html[] = ContentObjectRenditionImplementation :: launch(
            $content_object,
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FULL,
            $this);

        $html[] = $this->render_info();

        return implode(PHP_EOL, $html);
    }

    public function render_info()
    {
        $html = array();

        $html[] = '<div class="event_publication_info">';
        $html[] = htmlentities(Translation :: get('PublishedOn', null, Utilities :: COMMON_LIBRARIES)) . ' ' .
             $this->render_publication_date();
        $html[] = htmlentities(Translation :: get('By', null, Utilities :: COMMON_LIBRARIES)) . ' ' .
             $this->getPublication()->get_publication_publisher()->get_fullname();
        $html[] = htmlentities(Translation :: get('SharedWith', null, Utilities :: COMMON_LIBRARIES)) . ' ' .
             $this->render_publication_targets();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function render_publication_date()
    {
        $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
        return DatetimeUtilities :: format_locale_date($date_format, $this->getPublication()->get_published());
    }

    /**
     *
     * @return string
     */
    public function render_publication_targets()
    {
        if ($this->getPublication()->is_for_nobody())
        {
            return htmlentities(Translation :: get('Nobody', null, \Chamilo\Core\User\Manager :: context()));
        }
        else
        {
            $users = $this->getPublication()->get_target_users();
            $group_ids = $this->getPublication()->get_target_groups();

            if (count($users) + count($group_ids) == 1)
            {
                if (count($users) == 1)
                {
                    $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                        \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                        (int) $users[0]);
                    return $user->get_firstname() . ' ' . $user->get_lastname();
                }
                else
                {
                    $group = \Chamilo\Core\Group\Storage\DataManager :: retrieve_by_id(
                        Group :: class_name(),
                        $group_ids[0]);
                    return $group->get_name();
                }
            }

            $target_list = array();
            $target_list[] = '<select>';

            foreach ($users as $index => $user_id)
            {
                $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                    \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                    (int) $users[0]);
                $target_list[] = '<option>' . $user->get_firstname() . ' ' . $user->get_lastname() . '</option>';
            }

            $condition = new InCondition(
                new PropertyConditionVariable(Group :: class_name(), Group :: PROPERTY_ID),
                $group_ids);
            $groups = \Chamilo\Core\Group\Storage\DataManager :: retrieves(
                Group :: class_name(),
                new DataClassRetrievesParameters($condition));

            while ($group = $groups->next_result())
            {
                $target_list[] = '<option>' . $group->get_name() . '</option>';
            }

            $target_list[] = '</select>';
            return implode(PHP_EOL, $target_list);
        }
    }

    /**
     *
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $edit_url = $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_EDIT,
                    self :: PARAM_PUBLICATION_ID => $this->getPublication()->get_id()));

            $delete_url = $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_DELETE,
                    self :: PARAM_PUBLICATION_ID => $this->getPublication()->get_id()));

            $ical_url = $this->get_url(
                array(
                    self :: PARAM_ACTION => self :: ACTION_EXPORT,
                    self :: PARAM_PUBLICATION_ID => $this->getPublication()->get_id()));

            $toolActions->addButton(
                new Button(
                    Translation :: get('ExportIcal'),
                    Theme :: getInstance()->getCommonImagePath('Export/Csv'),
                    $ical_url));

            $user = $this->get_user();

            if ($user->is_platform_admin() || $this->getPublication()->get_publisher() == $user->get_id())
            {
                $commonActions->addButton(
                    new Button(
                        Translation :: get('Edit', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Edit'),
                        $edit_url));
                $commonActions->addButton(
                    new Button(
                        Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES),
                        Theme :: getInstance()->getCommonImagePath('Action/Delete'),
                        $delete_url));
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);
            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('personal_calendar_viewer');
    }

    public function get_content_object_display_attachment_url($attachment)
    {
        return $this->get_url(
            array(
                Application :: PARAM_ACTION => Manager :: ACTION_VIEW_ATTACHMENT,
                self :: PARAM_PUBLICATION_ID => $this->getPublication()->get_id(),
                self :: PARAM_OBJECT => $attachment->get_id()));
    }
}
