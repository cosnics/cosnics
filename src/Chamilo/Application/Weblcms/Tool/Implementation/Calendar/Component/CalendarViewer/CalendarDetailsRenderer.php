<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar\Component\CalendarViewer;

use Chamilo\Application\Weblcms\Renderer\PublicationList\Type\ContentObjectPublicationDetailsRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package application.lib.weblcms.tool.calendar.component.calendar_viewer
 */
class CalendarDetailsRenderer extends ContentObjectPublicationDetailsRenderer
{

    public function __construct($browser)
    {
        parent::__construct($browser);
    }

    public function as_html()
    {
        return parent::as_html();
    }

    public function render_description($publication)
    {
        $event = $publication->get_content_object();
        $html[] = '<em>';
        // TODO: date formatting
        $html[] = htmlentities(Translation::get('From', null, StringUtilities::LIBRARIES)) . ': ' .
            date('r', $event->get_start_date());
        $html[] = '<br />';
        // TODO: date formatting
        $html[] = htmlentities(Translation::get('To', null, StringUtilities::LIBRARIES)) . ': ' .
            date('r', $event->get_end_date());
        $html[] = '</em>';
        $html[] = '<br />';
        $html[] = $event->get_description();

        return implode(PHP_EOL, $html);
    }
}
