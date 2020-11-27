<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\ExamAssignment\Service\EntriesDownloader\EntriesDownloaderFactory;
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
    /**
     * @param null $pageTitle
     *
     * @return string|null
     */
    public function render_header($pageTitle = null)
    {
        $html = [];
        $html[] = parent::render_header($pageTitle);

        $html[] = '<div class="alert alert-info">';
        $html[] = $this->getTranslator()->trans('ExamAssignmentDifferences', [], Manager::context());
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
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

    /**
     *
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    }
}
