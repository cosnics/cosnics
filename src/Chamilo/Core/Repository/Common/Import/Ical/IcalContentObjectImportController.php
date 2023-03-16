<?php
namespace Chamilo\Core\Repository\Common\Import\Ical;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportImplementation;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;
use Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Translation\Translation;
use Sabre\VObject;
use Sabre\VObject\Component\VEvent;
use Sabre\VObject\Component\VTodo;

class IcalContentObjectImportController extends ContentObjectImportController
{
    public const FORMAT = 'ical';

    private $cache;

    /**
     * @var \Sabre\VObject\Component\VCalendar
     */
    private $calendar;

    private $temporary_directory;

    public function __construct($parameters)
    {
        parent::__construct($parameters);

        $icalData = file_get_contents($this->get_parameters()->get_file()->get_path());

        if (empty($icalData))
        {
            throw new UserException(
                Translation::getInstance()->getTranslation('CouldNotReadIcalFile', null, 'Chamilo\Core\Repository')
            );
        }

        $this->calendar = VObject\Reader::read(
            $icalData, VObject\Reader::OPTION_FORGIVING
        );
    }

    public function run()
    {
        if (in_array($this->get_parameters()->get_file()->get_extension(), self::get_allowed_extensions()))
        {
            $component_types = ['VEvent', 'VTodo'];
            $total_count = 0;

            foreach ($component_types as $component_type)
            {
                $components = $this->calendar->getBaseComponents($component_type);

                if (count($components) > 0)
                {
                    $total_count += count($components);

                    foreach ($components as $component)
                    {
                        $this->process_component($component);
                    }

                    $this->add_message(
                        Translation::get('IcalComponentsImported', ['TYPE' => $component_type]), self::TYPE_CONFIRM
                    );
                }
            }

            if ($total_count == 0)
            {
                $this->add_message(Translation::get('NoEvents'), self::TYPE_WARNING);
            }
        }
        else
        {
            $this->add_message(
                Translation::get(
                    'UnsupportedFileFormat', ['TYPES' => implode(', ', self::get_allowed_extensions())]
                ), self::TYPE_ERROR
            );
        }

        return $this->cache;
    }

    /**
     * @return int
     */
    public function determine_parent_id()
    {
        if ($this->get_parameters()->getWorkspace() instanceof PersonalWorkspace)
        {
            return $this->get_parameters()->get_category();
        }
        else
        {
            return 0;
        }
    }

    public static function get_allowed_extensions()
    {
        return ['ics'];
    }

    public static function is_available()
    {
        $calendar_event_available = in_array(
            'Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent',
            DataManager::get_registered_types(true)
        );

        $task_available = in_array(
            'Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task', DataManager::get_registered_types(true)
        );

        return $calendar_event_available || $task_available;
    }

    public function process_component($component)
    {
        if ($component instanceof VEvent && in_array(
                'Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent',
                DataManager::get_registered_types(true)
            ))
        {
            $type = CalendarEvent::class;
        }
        elseif ($component instanceof VTodo && in_array(
                'Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass\Task',
                DataManager::get_registered_types(true)
            ))
        {
            $type = Task::class;
        }

        if ($type)
        {
            $content_object_parameter = new IcalContentObjectImportParameters($component);

            $content_object = ContentObjectImportImplementation::launch($this, $type, $content_object_parameter);
            if ($content_object->create())
            {
                $this->process_workspace($content_object);

                $this->cache[] = $content_object->get_id();
            }
            else
            {
                $this->add_message(
                    Translation::get(
                        'UnsupportedFileFormat', ['TYPES' => implode(', ', self::get_allowed_extensions())]
                    ), self::TYPE_ERROR
                );
            }
        }
    }

    /**
     * @param ContentObject $contentObject
     */
    public function process_workspace(ContentObject $contentObject)
    {
        if ($this->get_parameters()->getWorkspace() instanceof Workspace)
        {
            $this->getContentObjectRelationService()->createContentObjectRelationFromParameters(
                $this->get_parameters()->getWorkspace()->getId(), $contentObject->getId(),
                $this->get_parameters()->get_category()
            );
        }
    }
}
