<?php
namespace Chamilo\Core\Repository\ContentObject\Portfolio\Display\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\Portfolio\Display\Manager;
use Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataClass\Activity;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\OrderBy;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Default viewer component that handles the visualization of the portfolio item or folder
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends Manager implements DelegateComponent
{

    /**
     * Executes this component
     */
    public function run()
    {
        parent :: run();

        if (! $this->get_parent()->is_allowed_to_view_content_object($this->get_current_node()))
        {
            throw new NotAllowedException();
        }

        $html = array();

        $html[] = $this->render_header();

        $content = array();
        $content[] = ContentObjectRenditionImplementation :: launch(
            $this->get_current_content_object(),
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_FULL,
            $this);

        if ($this->get_current_node()->is_root())
        {
            $content[] = $this->render_last_actions();
        }
        else
        {
            $content[] = $this->render_statistics($this->get_current_content_object());
        }

        $this->get_tabs_renderer()->set_content(implode(PHP_EOL, $content));

        $html[] = $this->get_tabs_renderer()->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Render the basic statistics for the portfolio item / folder
     *
     * @param \core\repository\ContentObject $content_object
     * @return string
     */
    public function render_statistics($content_object)
    {
        $html = array();

        $html[] = '<div class="portfolio-statistics">';

        if ($this->get_user_id() == $content_object->get_owner_id())
        {
            $html[] = Translation :: get(
                'CreatedOn',
                array('DATE' => $this->format_date($content_object->get_creation_date())));
        }
        else
        {
            $html[] = Translation :: get(
                'CreatedOnBy',
                array(
                    'DATE' => $this->format_date($content_object->get_creation_date()),
                    'USER' => $content_object->get_owner()->get_fullname()));
        }

        if ($content_object->get_creation_date() != $content_object->get_modification_date())
        {
            $html[] = '<br />';
            $html[] = Translation :: get(
                'LastModifiedOn',
                array('DATE' => $this->format_date($content_object->get_modification_date())));
        }

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     * Formats the given date in a human-readable format.
     *
     * @param $date int A UNIX timestamp.
     * @return string The formatted date.
     */
    public function format_date($date)
    {
        $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);
        return DatetimeUtilities :: format_locale_date($date_format, $date);
    }

    /**
     * Render the last actions undertaken by the user in the portfolio
     *
     * @return string
     */
    public function render_last_actions()
    {
        $html = array();

        $last_activities = \Chamilo\Core\Repository\Integration\Chamilo\Core\Tracking\Storage\DataManager :: retrieve_activities(
            $this->get_current_content_object(),
            null,
            0,
            1,
            array(new OrderBy(new PropertyConditionVariable(Activity :: class_name(), Activity :: PROPERTY_DATE))));

        $last_activity = $last_activities->next_result();

        if ($last_activity)
        {
            $html[] = '<div class="content_object" style="background-image: url(' .
                 Theme :: getInstance()->getImagesPath('Chamilo\Core\Repository\ContentObject\Portfolio\Display\\') .
                 'tab/activity.png)">';
            $html[] = '<div class="description">';

            $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);

            $html[] = Translation :: get(
                'LastEditedOn',
                array('DATE' => DatetimeUtilities :: format_locale_date($date_format, $last_activity->get_date())));

            $html[] = '<br />';

            $html[] = Translation :: get(
                'LastAction',
                array('ACTION' => $last_activity->get_type_string(), 'CONTENT' => $last_activity->get_content()));

            $html[] = '<div class="clear"></div>';

            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
