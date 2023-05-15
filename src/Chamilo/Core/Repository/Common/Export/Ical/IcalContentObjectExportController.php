<?php
namespace Chamilo\Core\Repository\Common\Export\Ical;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use DateTime;
use DateTimeZone;
use Exception;
use Sabre\VObject\Component;
use Sabre\VObject\Component\VCalendar;

class IcalContentObjectExportController extends ContentObjectExportController
{

    /**
     * @var \Sabre\VObject\Component\VCalendar
     */
    private $calendar;

    private $file;

    public function __construct(ExportParameters $parameters)
    {
        parent::__construct($parameters);
        $this->calendar = new VCalendar();
        $this->prepare_file_system();
    }

    public function run()
    {
        $content_object_ids = $this->get_parameters()->get_content_object_ids();

        if (count($content_object_ids) > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID), $content_object_ids
            );
        }
        else
        {
            $condition = null;
        }

        $parameters = new DataClassRetrievesParameters($condition);
        $content_objects = DataManager::retrieve_active_content_objects(ContentObject::class, $parameters);

        foreach ($content_objects as $content_object)
        {
            $this->process($content_object);
        }

        $this->addTimeZone();
        $this->save();

        return $this->file;
    }

    private function addTimeZone()
    {
        $from = time();
        $to = $from;

        try
        {
            $tz = new DateTimeZone(date_default_timezone_get());

            // get all transitions for one year back/ahead
            $year = 86400 * 360;
            $transitions = $tz->getTransitions($from - $year, $to + $year);

            $vt = new Component($this->get_calendar(), 'VTIMEZONE');
            $vt->TZID = $tz->getName();

            $std = null;
            $dst = null;
            foreach ($transitions as $i => $trans)
            {
                $cmp = null;

                // skip the first entry...
                if ($i == 0)
                {
                    // ... but remember the offset for the next TZOFFSETFROM value
                    $tzfrom = $trans['offset'] / 3600;
                    continue;
                }

                // daylight saving time definition
                if ($trans['isdst'])
                {
                    $t_dst = $trans['ts'];
                    $dst = new Component($this->get_calendar(), 'DAYLIGHT');
                    $cmp = $dst;
                }
                // standard time definition
                else
                {
                    $t_std = $trans['ts'];
                    $std = new Component($this->get_calendar(), 'STANDARD');
                    $cmp = $std;
                }

                if ($cmp)
                {
                    $dt = new DateTime($trans['time']);
                    $offset = $trans['offset'] / 3600;

                    $cmp->DTSTART = $dt->format('Ymd\THis');
                    $cmp->TZOFFSETFROM =
                        sprintf('%s%02d%02d', $tzfrom >= 0 ? '+' : '', floor($tzfrom), ($tzfrom - floor($tzfrom)) * 60);
                    $cmp->TZOFFSETTO =
                        sprintf('%s%02d%02d', $offset >= 0 ? '+' : '', floor($offset), ($offset - floor($offset)) * 60);

                    // add abbreviated timezone name if available
                    if (!empty($trans['abbr']))
                    {
                        $cmp->TZNAME = $trans['abbr'];
                    }

                    $tzfrom = $offset;
                    $vt->add($cmp);
                }

                // we covered the entire date range
                if ($std && $dst && min($t_std, $t_dst) < $from && max($t_std, $t_dst) > $to)
                {
                    break;
                }
            }

            $this->get_calendar()->add($vt);
        }
        catch (Exception $e)
        {
        }
    }

    /**
     * @return \Sabre\VObject\Component\VCalendar
     */
    public function get_calendar()
    {
        return $this->calendar;
    }

    /**
     * @see \core\repository\ContentObjectExportController::get_filename()
     */
    public function get_filename()
    {
        return 'export_ical.ics';
    }

    public function prepare_file_system()
    {
        $user_id = Session::get_user_id();
        $directory = Path::getInstance()->getTemporaryPath() . $user_id . '/';

        if (!is_dir($directory))
        {
            mkdir($directory, 0777, true);
        }

        $this->file = $directory . 'export_ical.ics';
    }

    public function process($content_object)
    {
        $export_types = ContentObjectExportImplementation::get_types_for_object($content_object::CONTEXT);

        if (in_array(ContentObjectExport::FORMAT_ICAL, $export_types))
        {
            ContentObjectExportImplementation::launch(
                $this, $content_object, ContentObjectExport::FORMAT_ICAL, $this->get_parameters()->getType()
            );
        }
    }

    public function save()
    {
        $content = $this->calendar->serialize();

        $handle = fopen($this->file, 'w+');
        fwrite($handle, $content);
        fclose($handle);
    }
}
