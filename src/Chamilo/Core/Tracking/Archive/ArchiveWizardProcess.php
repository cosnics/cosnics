<?php
namespace Chamilo\Core\Tracking\Archive;

use Chamilo\Configuration\Package\Action\Installer;
use Chamilo\Core\Tracking\Manager;
use Chamilo\Core\Tracking\Storage\DataClass\ArchiveControllerItem;
use Chamilo\Core\Tracking\Storage\DataClass\Tracker;
use Chamilo\Core\Tracking\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use HTML_QuickForm_Action;

/**
 * $Id: archive_wizard_process.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 *
 * @package tracking.lib.tracking_manager.component.wizards.archive
 */

/**
 * This class implements the action to take after the user has completed a archive trackers wizard
 *
 * @author Sven Vanpoucke
 */
class ArchiveWizardProcess extends HTML_QuickForm_Action
{

    /**
     * The component in which the wizard runs
     */
    private $parent;

    private $tdm;

    /**
     * Constructor
     *
     * @param $parent TrackingManagerArchiveComponent The component in which the wizard runs
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->tdm = DataManager :: get_instance();
    }

    /**
     * Executes this page
     *
     * @param $page ArchiveWizardPage the page that has to be executed
     * @param $actionName string the action
     */
    public function perform($page, $actionName)
    {
        $exports = $page->controller->exportValues();

        // Display the page header
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(
            new Breadcrumb(
                $this->parent->get_url(array(Application :: PARAM_ACTION => Manager :: ACTION_ARCHIVE)),
                Translation :: get('Archiver')));

        $html = array();

        $html[] = $this->parent->render_header();

        $startdate = $exports['start_date'];
        list($syear, $smonth, $sday) = split('-', $startdate);
        $enddate = $exports['end_date'];
        list($eyear, $emonth, $eday) = split('-', $enddate);

        $startdate = DatetimeUtilities :: time_from_datepicker_without_timepicker($startdate);
        $enddate = DatetimeUtilities :: time_from_datepicker_without_timepicker($enddate, 23, 59, 59);

        $period = $exports['period'];

        foreach ($exports as $key => $export)
        {
            if (substr($key, strlen($key) - strlen('event'), strlen($key)) == 'event')
            {
                $application = substr($key, 0, strpos($key, '_'));
                $eventname = substr($key, strpos($key, '_') + 1, strlen($key) - strlen('event') - strpos($key, '_') - 2);
                $event = $this->parent->retrieve_event_by_name($eventname, $application);

                $html[] = $this->display_event_header($eventname);

                foreach ($exports as $key2 => $export2)
                {
                    if ((strpos($key2, $eventname) !== false) && ($key2 != $key))
                    {
                        $id = substr($key2, strlen($application . '_' . $eventname . '_event_'));
                        $trackerregistration = $this->parent->retrieve_tracker_registration($id);

                        $tracker = Tracker :: factory(
                            $trackerregistration->get_tracker(),
                            $trackerregistration->get_context());

                        $html[] = (' &nbsp; &nbsp; ' . Translation :: get('Archiving_tracker') . ': ' .
                             ClassnameUtilities :: getInstance()->getClassnameFromObject($tracker) . '<br />');

                        $application_path = Path :: getInstance()->namespaceToFullPath(
                            $trackerregistration->get_context());
                        $path = $application_path . 'trackers/tracker_tables/' . $tracker->get_table() . '.xml';

                        $storage_units = array();

                        if ($tracker->is_summary_tracker())
                        {
                            $storage_units[] = $tracker->get_table() . '_' . $startdate;
                            if ($this->create_storage_unit($path, '_' . $startdate))
                                $this->create_archive_controller_item(
                                    $tracker->get_table(),
                                    $startdate,
                                    $period,
                                    $enddate);
                        }
                        else
                        {
                            $difference = gregoriantojd($emonth, $eday, $eyear) - gregoriantojd($smonth, $sday, $syear);

                            if ($difference == 0)
                                $difference = 1;

                            $amount_of_tables = ceil($difference / $period);

                            for ($i = 0; $i < $amount_of_tables; $i ++)
                            {
                                $added_days = $i * $period;
                                $date = mktime(
                                    0,
                                    0,
                                    0,
                                    date("m", $startdate),
                                    date("d", $startdate) + $added_days,
                                    date("Y", $startdate));
                                $storage_units[$date] = $tracker->get_table() . '_' . $date;
                                if ($this->create_storage_unit($path, '_' . $date))
                                    $this->create_archive_controller_item(
                                        $tracker->get_table(),
                                        $date,
                                        $period,
                                        $enddate);
                            }
                        }

                        $resultset = $tracker->export($startdate, $enddate, $event);

                        foreach ($resultset as $result)
                        {
                            if ($tracker->is_summary_tracker())
                            {
                                $this->tdm->create_record($tracker :: class_name(), $result);
                            }
                            else
                            {
                                $date = DatetimeUtilities :: time_from_datepicker($result->get_date());

                                foreach ($storage_units as $start_time => $storage_unit)
                                {
                                    $end_time = mktime(
                                        23,
                                        59,
                                        59,
                                        date("m", $start_time),
                                        date("d", $start_time) + $period,
                                        date("Y", $start_time));
                                    if (($date >= $start_time) && ($date <= $end_time))
                                    {
                                        $this->tdm->create_record($tracker :: class_name(), $result);
                                        break;
                                    }
                                }
                            }
                            $result->delete();
                        }
                    }
                }
                $html[] = $this->display_event_footer();
            }
        }

        $time = time();
        $setting = \Chamilo\Configuration\Storage\DataManager :: retrieve_setting_from_variable_name(
            'last_time_archived',
            'core\tracking');
        $setting->set_value($time);
        $setting->update();

        $html[] = '<a href="' . $this->parent->get_url(
            array(\Chamilo\Core\Admin\Manager :: PARAM_CONTEXT => \Chamilo\Core\Admin\Manager :: context()),
            array(Manager :: PARAM_ACTION)) . '">' . Translation :: get('Go_to_administration') . '</a>';

        // Display the page footer
        $html[] = $this->parent->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * Creates a new item in the archive controller table
     *
     * @param $tablename string the original tablename
     * @param int startdate the startdate
     * @param int period the amount of days for 1 table
     * @param int total_end_date the end date of the archiving process
     * @return true if creation is valid
     */
    public function create_archive_controller_item($tablename, $startdate, $period, $total_end_date)
    {
        $enddate = mktime(0, 0, 0, date("m", $startdate), date("d", $startdate) + $period, date("Y", $startdate));
        if ($enddate > $total_end_date)
            $enddate = $total_end_date;
        $new_tablename = $tablename . '_' . $startdate;

        $controller_item = new ArchiveControllerItem();

        $controller_item->set_start_date($startdate);
        $controller_item->set_end_date($enddate);
        $controller_item->set_original_table($tablename);
        $controller_item->set_table_name($new_tablename);

        return $controller_item->create();
    }

    public function create_storage_unit($path, $extra_name)
    {
        $storage_unit_info = Installer :: parse_xml_file($path);

        $name = $storage_unit_info['name'] . $extra_name;
        $tables = $this->tdm->get_tables();

        $tname = 'tracker_' . $name;

        if (in_array($tname, $tables))
        {
            return false;
        }

        return $this->tdm->create_storage_unit($name, $storage_unit_info['properties'], $storage_unit_info['indexes']);
    }

    public function display_event_header($eventname)
    {
        $html = array();

        $html[] = '<div class="content_object" style="padding: 15px 15px 15px 76px;">';
        $html[] = '<div class="title">' . Translation :: get('Event') . ' ' . $eventname . '</div>';
        $html[] = '<div class="description">';

        return implode(PHP_EOL, $html);
    }

    public function display_event_footer()
    {
        $html = array();

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
