<?php
namespace Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home\Type;

use Chamilo\Core\Admin\Announcement\Integration\Chamilo\Core\Home\SortableTable;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\InequalityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;

class SystemAnnouncements extends \Chamilo\Core\Home\BlockRendition
{

    private $publications;

    public static function get_default_image_path($application = '', $type = '', $size = Theme :: ICON_MEDIUM)
    {
        if ($type)
        {
            return parent :: get_default_image_path($application, $type, $size);
        }
        else
        {
            /**
             * SystemAnnouncement may not be available if not installed.
             * Therefore do not use SystemAnnouncement::...
             */
            return Theme :: getInstance()->getImagePath(
                ContentObject :: get_content_object_type_namespace('system_announcement'),
                'Logo/' . $size);
        }
    }

    public function is_visible()
    {
        if (! $this->get_user() || ($this->is_empty() && ! $this->show_when_empty()))
        {
            return false;
        }

        return true; // i.e.display on homepage when anonymous
    }

    public function show_when_empty()
    {
        $configuration = $this->get_configuration();
        $result = isset($configuration['show_when_empty']) ? $configuration['show_when_empty'] : true;
        $result = (bool) $result;
        return $result;
    }

    public function is_empty()
    {
        $announcements = $this->get_publications();
        return $announcements->size() == 0;
    }

    /**
     * Returns the url to the icon.
     *
     * @return string
     */
    public function get_icon()
    {
        return self :: get_default_image_path();
    }

    public function get_publications()
    {
        if (! isset($this->publications))
        {
            $from_date_variables = new PropertyConditionVariable(
                Publication :: class_name(),
                Publication :: PROPERTY_FROM_DATE);

            $to_date_variable = new PropertyConditionVariable(
                Publication :: class_name(),
                Publication :: PROPERTY_TO_DATE);

            $time_conditions = array();

            $time_conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Publication :: class_name(), Publication :: PROPERTY_HIDDEN),
                new StaticConditionVariable(0));

            $forever_conditions = array();
            $forever_conditions[] = new EqualityCondition($from_date_variables, new StaticConditionVariable(0));
            $forever_conditions[] = new EqualityCondition($to_date_variable, new StaticConditionVariable(0));
            $forever_condition = new AndCondition($forever_conditions);

            $between_conditions = array();
            $between_conditions[] = new InequalityCondition(
                $from_date_variables,
                InequalityCondition :: LESS_THAN_OR_EQUAL,
                new StaticConditionVariable(time()));
            $between_conditions[] = new InequalityCondition(
                $to_date_variable,
                InequalityCondition :: GREATER_THAN_OR_EQUAL,
                new StaticConditionVariable(time()));
            $between_condition = new AndCondition($between_conditions);

            $time_conditions[] = new OrCondition(array($forever_condition, $between_condition));

            $condition = new AndCondition($time_conditions);

            $this->publications = \Chamilo\Core\Admin\Announcement\Storage\DataManager :: retrieve_publications_for_me(
                $condition,
                array(),
                0,
                - 1,
                $this->get_user()->get_id());
        }

        return $this->publications;
    }

    public function get_publication_link($publication, $admin)
    {
        $paremeters = array();
        $parameters[\Chamilo\Core\Admin\Manager :: PARAM_ACTION] = \Chamilo\Core\Admin\Manager :: ACTION_SYSTEM_ANNOUNCEMENTS;
        $parameters[\Chamilo\Core\Admin\Announcement\Manager :: PARAM_ACTION] = \Chamilo\Core\Admin\Announcement\Manager :: ACTION_VIEW;
        $parameters[\Chamilo\Core\Admin\Announcement\Manager :: PARAM_SYSTEM_ANNOUNCEMENT_ID] = $publication[Publication :: PROPERTY_ID];

        $result = Redirect :: get_link($parameters, null, null, Redirect :: TYPE_CORE);
        return $result;
    }

    public function display_content()
    {
        $publcations = $this->get_publications();

        if ($publcations->size() == 0)
        {
            return htmlspecialchars(Translation :: get('NoSystemAnnouncementsCurrently'));
        }

        $data = array();

        while ($publication = $publcations->next_result())
        {
            $content_object = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_content_object(
                (int) $publication[Publication :: PROPERTY_CONTENT_OBJECT_ID]);

            $icon = $content_object->get_icon_image(
                Theme :: ICON_MINI,
                ! (boolean) $publication[Publication :: PROPERTY_HIDDEN]);

            $href = htmlspecialchars($this->get_publication_link($publication));
            $title = htmlspecialchars($content_object->get_title());
            $target = $this->get_view() == self :: WIDGET_VIEW ? ' target="_blank" ' : '';
            $link = '<a href="' . $href . '"' . $target . '>' . $title . '</a>';

            $data[] = array($icon, $link);
        }

        $table = new SortableTable($data);
        $table->setAttribute('class', 'data_table invisible_table');
        $table->set_header(0, null, false);
        $table->getHeader()->setColAttributes(0, 'class="action invisible"');
        $table->set_header(1, null, false);
        $table->getHeader()->setColAttributes(1, 'class="invisible"');

        $html[] = $table->as_html();

        return implode(PHP_EOL, $html);
    }
}
