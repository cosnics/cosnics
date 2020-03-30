<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntriesDownloader\EntriesDownloaderFactory;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Table\FormAction\TableFormAction;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package application.weblcms.tool.assignment.php.component Browser for assignments with calendar functionality.
 * @author Joris Willems <joris.willems@gmail.com>
 * @author Alexander Van Paemel
 */
class BrowserComponent extends Manager
{

    public function get_tool_actions()
    {
        $tool_actions = array();

        if ($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $tool_actions[] = new Button(
                Translation::get('ScoresOverview'),
                new FontAwesomeGlyph('chart-bar'),
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Manager::PARAM_TOOL => \Chamilo\Application\Weblcms\Manager::ACTION_REPORTING,
                        \Chamilo\Application\Weblcms\Manager::PARAM_TEMPLATE_ID => \Chamilo\Application\Weblcms\Integration\Chamilo\Core\Reporting\Template\AssignmentScoresTemplate::class_name(
                        ),
                        \Chamilo\Application\Weblcms\Manager::PARAM_TOOL_ACTION => \Chamilo\Application\Weblcms\Tool\Implementation\Reporting\Manager::ACTION_VIEW
                    )
                ),
                Button::DISPLAY_ICON_AND_LABEL, false, null, '_blank'
            );
        }

        return $tool_actions;
    }

    public function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $object = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            Assignment::class_name(),
            $publication[ContentObjectPublication::PROPERTY_CONTENT_OBJECT_ID]
        );

        $calendar_event = ContentObject::factory(CalendarEvent::class_name());

        $calendar_event->set_title($object->get_title());
        $calendar_event->set_description($object->get_description());
        if ($object instanceof Assignment)
        {
            $calendar_event->set_start_date($object->get_start_time());
            $calendar_event->set_end_date($object->get_end_time());
        }
        else
        {
            $calendar_event->set_start_date($object->get_start_date());
            $calendar_event->set_end_date($object->get_end_date());
        }

        $calendar_event->set_frequency(CalendarEvent::FREQUENCY_NONE);

        return $calendar_event;
    }

    public function get_additional_form_actions()
    {
        return array(
            new TableFormAction(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD_ENTRIES
                    )
                ),
                Translation::get('DownloadEntriesSelectedAssignments'),
                false
            ),
            new TableFormAction(
                $this->get_url(
                    array(
                        \Chamilo\Application\Weblcms\Tool\Manager::PARAM_ACTION => self::ACTION_DOWNLOAD_ENTRIES,
                        EntriesDownloaderComponent::PARAM_ENTRIES_DOWNLOAD_STRATEGY => EntriesDownloaderFactory::ENTRIES_DOWNLOADER_ENTITY
                    )
                ),
                Translation::get('DownloadEntriesSelectedAssignmentsByEntities'),
                false
            )
        );
    }

    public function render_header($pageTitle = '')
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);

        if($this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            $html[] = '<div class="alert alert-info" style="display: flex">';
            $html[]= '<div class="fas fa-info" style="font-size: 20px;"></div>';
            $html[]= '<div style="margin-left: 10px;">';
            $html[] = $this->getTranslator()->trans('AssignmentChangesWarning', [], Manager::context());
            $html[] = '</div>';
            $html[] = '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
