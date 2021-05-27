<?php
namespace Chamilo\Core\Repository\Common\Export\Ical;

use Chamilo\Core\Repository\Common\Export\ContentObjectExport;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportController;
use Chamilo\Core\Repository\Common\Export\ContentObjectExportImplementation;
use Chamilo\Core\Repository\Common\Export\ExportParameters;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Calendar\TimeZone\TimeZoneCalendarWrapper;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use kigkonsult\iCalcreator\timezoneHandler;
use Sabre\VObject\Component\VCalendar;

class IcalContentObjectExportController extends ContentObjectExportController
{

    /**
     *
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

    /**
     *
     * @return \Sabre\VObject\Component\VCalendar
     */
    public function get_calendar()
    {
        return $this->calendar;
    }

    public function run()
    {
        $content_object_ids = $this->get_parameters()->get_content_object_ids();

        if (count($content_object_ids) > 0)
        {
            $condition = new InCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID),
                $content_object_ids,
                ContentObject::get_table_name());
        }
        else
        {
            $condition = null;
        }

        $parameters = new DataClassRetrievesParameters($condition);
        $content_objects = DataManager::retrieve_active_content_objects(ContentObject::class, $parameters);

        foreach($content_objects as $content_object)
        {
            $this->process($content_object);
        }

        $this->addTimeZone();
        $this->save();

        return $this->file;
    }

    private function addTimeZone()
    {
//        timezoneHandler::createTimezone(
//            $this->get_calendar(),
//            date_default_timezone_get(),
//            [],
//            1,
//            2145916799);
    }

    public function process($content_object)
    {
        $export_types = ContentObjectExportImplementation::get_types_for_object($content_object->package());

        if (in_array(ContentObjectExport::FORMAT_ICAL, $export_types))
        {
            ContentObjectExportImplementation::launch(
                $this,
                $content_object,
                ContentObjectExport::FORMAT_ICAL,
                $this->get_parameters()->get_type());
        }
    }

    public function prepare_file_system()
    {
        $user_id = Session::get_user_id();
        $directory = Path::getInstance()->getTemporaryPath() . $user_id . '/';

        if (! is_dir($directory))
        {
            mkdir($directory, 0777, true);
        }

        $this->file = $directory . 'export_ical.ics';
    }

    public function save()
    {
        $content = $this->calendar->serialize();

        $handle = fopen($this->file, 'w+');
        fwrite($handle, $content);
        fclose($handle);
    }

    /**
     *
     * @see \core\repository\ContentObjectExportController::get_filename()
     */
    public function get_filename()
    {
        return 'export_ical.ics';
    }
}
