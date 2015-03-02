<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Libraries\Calendar\Event\Interfaces\ActionSupport;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package libraries\calendar\renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventListRenderer extends EventRenderer
{

    /**
     * Gets a html representation of an event for a list renderer
     *
     * @return string
     */
    public function run()
    {
        $html = array();

        $html[] = '<div class="content_object" style="background-image: url(' .
             Theme :: getInstance()->getImagePath($this->get_event()->get_context(), 'Logo/22') . ');">';
        $html[] = '<div class="title">' . htmlentities($this->get_event()->get_title()) . '</div>';
        $html[] = $this->get_description();
        $html[] = $this->get_actions();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_description()
    {
        $html = array();
        $html[] = '<div class="description">';
        $html[] = $this->get_range();
        $html[] = $this->get_content();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_range()
    {
        $html = array();

        $date_format = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);

        if ($this->get_event()->get_end_date() != '')
        {
            $html[] = '<div class="calendar_event_range">' .
                 htmlentities(
                    Translation :: get('From', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                     DatetimeUtilities :: format_locale_date($date_format, $this->get_event()->get_start_date()) . ' ' .
                     Translation :: get('Until', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                     DatetimeUtilities :: format_locale_date($date_format, $this->get_event()->get_end_date())) . '</div>';
        }
        else
        {
            $html[] = '<div class="calendar_event_range">' . DatetimeUtilities :: format_locale_date(
                $date_format,
                $this->get_event()->get_start_date()) . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function get_content()
    {
        return $this->get_event()->get_content();
    }

    public function get_actions()
    {
        $html = array();

        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $toolbar->add_item(
            new ToolbarItem(
                Translation :: get('View', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagePath('action_browser'),
                html_entity_decode($this->get_event()->get_url()),
                ToolbarItem :: DISPLAY_ICON));

        if ($this->get_renderer()->get_application() instanceof ActionSupport)
        {
            foreach ($this->get_renderer()->get_actions($this->get_event()) as $action)
            {
                $toolbar->add_item($action);
            }
        }

        $html[] = '<div style="float: right;">';
        $html[] = $toolbar->as_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
