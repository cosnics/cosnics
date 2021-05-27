<?php
namespace Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass;

use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\AttachmentSupport;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Calendar\Event\Recurrence\RecurringContentObject;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Core\Repository\ContentObject\Task\Storage\DataClass
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
class Task extends RecurringContentObject implements Versionable, AttachmentSupport, Includeable
{

    const CATEGORY_ANNIVERSARY = 'Anniversary';
    const CATEGORY_BUSINESS = 'Business';
    const CATEGORY_CALL = 'Call';
    const CATEGORY_CLIENT = 'Client';
    const CATEGORY_COMPETITION = 'Competition';
    const CATEGORY_CONFERENCE = 'Conference';
    const CATEGORY_FAVORITE = 'Favorite';
    const CATEGORY_GIFT = 'Gift';
    const CATEGORY_HOLIDAY = 'Holiday';
    const CATEGORY_IDEAS = 'Ideas';
    const CATEGORY_MEETING = 'Meeting';
    const CATEGORY_MONITORING = 'Monitoring';
    const CATEGORY_PRIVATE = 'Private';
    const CATEGORY_PROBLEMS = 'Problems';
    const CATEGORY_PROFESSIONAL = 'Professional';
    const CATEGORY_PROJECTS = 'Projects';
    const CATEGORY_PUBLIC_HOLIDAY = 'PublicHoliday';
    const CATEGORY_SUPPLIER = 'Supplier';
    const CATEGORY_TRAVEL = 'Travel';
    const CATEGORY_VARIOUS = 'Various';

    const PRIORITY_HIGH = 1;
    const PRIORITY_LOW = 9;
    const PRIORITY_NONE = 0;
    const PRIORITY_NORMAL = 5;
    const PROPERTY_BYDAY = 'byday';
    const PROPERTY_BYMONTH = 'bymonth';
    const PROPERTY_BYMONTHDAY = 'bymonthday';

    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_DUE_DATE = 'due_date';
    const PROPERTY_PRIORITY = 'priority';
    const PROPERTY_START_DATE = 'start_date';

    /**
     * @param string $type
     *
     * @return string
     * @throws \Exception
     */
    public static function category_as_string($type)
    {
        switch ($type)
        {
            case self::CATEGORY_ANNIVERSARY :
                $string = Translation::get('Anniversary');
                break;
            case self::CATEGORY_BUSINESS :
                $string = Translation::get('Business');
                break;
            case self::CATEGORY_CALL :
                $string = Translation::get('Call');
                break;
            case self::CATEGORY_HOLIDAY :
                $string = Translation::get('Holiday');
                break;
            case self::CATEGORY_GIFT :
                $string = Translation::get('Gift');
                break;
            case self::CATEGORY_CLIENT :
                $string = Translation::get('Client');
                break;
            case self::CATEGORY_COMPETITION :
                $string = Translation::get('Competition');
                break;
            case self::CATEGORY_CONFERENCE :
                $string = Translation::get('Conference');
                break;
            case self::CATEGORY_VARIOUS :
                $string = Translation::get('Various');
                break;
            case self::CATEGORY_SUPPLIER :
                $string = Translation::get('Supplier');
                break;
            case self::CATEGORY_IDEAS :
                $string = Translation::get('Ideas');
                break;
            case self::CATEGORY_PUBLIC_HOLIDAY :
                $string = Translation::get('PublicHoliday');
                break;
            case self::CATEGORY_PRIVATE :
                $string = Translation::get('Private');
                break;
            case self::CATEGORY_FAVORITE :
                $string = Translation::get('Favorite');
                break;
            case self::CATEGORY_PROBLEMS :
                $string = Translation::get('Problems');
                break;
            case self::CATEGORY_PROFESSIONAL :
                $string = Translation::get('Professional');
                break;
            case self::CATEGORY_PROJECTS :
                $string = Translation::get('Projects');
                break;
            case self::CATEGORY_MEETING :
                $string = Translation::get('Meeting');
                break;
            case self::CATEGORY_MONITORING :
                $string = Translation::get('Monitoring');
                break;
            case self::CATEGORY_TRAVEL :
                $string = Translation::get('Travel');
                break;
            default:
                throw new Exception();
        }

        return $string;
    }

    /**
     * @return string[]
     */
    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_START_DATE,
            self::PROPERTY_DUE_DATE,
            self::PROPERTY_UNTIL,
            self::PROPERTY_FREQUENCY,
            self::PROPERTY_FREQUENCY_COUNT,
            self::PROPERTY_FREQUENCY_INTERVAL,
            self::PROPERTY_BYDAY,
            self::PROPERTY_BYMONTH,
            self::PROPERTY_BYMONTHDAY
        );
    }

    /**
     * @return string
     */
    public function get_category()
    {
        return $this->get_additional_property(self::PROPERTY_CATEGORY);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function get_category_as_string()
    {
        return self::category_as_string($this->get_category());
    }

    /**
     * @return integer
     */
    public function get_due_date()
    {
        return $this->get_additional_property(self::PROPERTY_DUE_DATE);
    }

    /**
     * @return integer
     */
    public function get_priority()
    {
        return $this->get_additional_property(self::PROPERTY_PRIORITY);
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function get_priority_as_string()
    {
        return self::priority_as_string($this->get_priority());
    }

    /**
     * @return string[]
     */
    public static function get_priority_options()
    {
        $options = [];

        $options[self::PRIORITY_NONE] = Translation::get('Unspecified');
        $options[self::PRIORITY_LOW] = Translation::get('Low');
        $options[self::PRIORITY_NORMAL] = Translation::get('Normal');
        $options[self::PRIORITY_HIGH] = Translation::get('High');

        return $options;
    }

    /**
     * @return integer
     */
    public function get_start_date()
    {
        return $this->get_additional_property(self::PROPERTY_START_DATE);
    }

    /**
     * @return string
     */
    public static function get_table_name()
    {
        return 'repository_task';
    }

    /**
     * @return string
     */
    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class, true);
    }

    /**
     * @return string
     */
    public function get_type_string()
    {
        if ($this->has_frequency())
        {
            return Translation::get('RepeatingCalendarEvent');
        }
        else
        {
            return parent::get_type_string();
        }
    }

    /**
     * @return string[]
     */
    public static function get_types_options()
    {
        $types = [];

        $types[self::CATEGORY_ANNIVERSARY] = Translation::get('Anniversary');
        $types[self::CATEGORY_BUSINESS] = Translation::get('Business');
        $types[self::CATEGORY_CALL] = Translation::get('Call');
        $types[self::CATEGORY_HOLIDAY] = Translation::get('Holiday');
        $types[self::CATEGORY_GIFT] = Translation::get('Gift');
        $types[self::CATEGORY_CLIENT] = Translation::get('Client');
        $types[self::CATEGORY_COMPETITION] = Translation::get('Competition');
        $types[self::CATEGORY_CONFERENCE] = Translation::get('Conference');
        $types[self::CATEGORY_VARIOUS] = Translation::get('Various');
        $types[self::CATEGORY_SUPPLIER] = Translation::get('Supplier');
        $types[self::CATEGORY_IDEAS] = Translation::get('Ideas');
        $types[self::CATEGORY_PUBLIC_HOLIDAY] = Translation::get('PublicHoliday');
        $types[self::CATEGORY_PRIVATE] = Translation::get('Private');
        $types[self::CATEGORY_FAVORITE] = Translation::get('Favorite');
        $types[self::CATEGORY_PROBLEMS] = Translation::get('Problems');
        $types[self::CATEGORY_PROFESSIONAL] = Translation::get('Professional');
        $types[self::CATEGORY_PROJECTS] = Translation::get('Projects');
        $types[self::CATEGORY_MEETING] = Translation::get('Meeting');
        $types[self::CATEGORY_MONITORING] = Translation::get('Monitoring');
        $types[self::CATEGORY_TRAVEL] = Translation::get('Travel');
        asort($types);

        return $types;
    }

    /**
     * @return string
     */
    public static function parent_class_name()
    {
        return ContentObject::class;
    }

    /**
     * @param integer $priority
     *
     * @return string
     * @throws \Exception
     */
    public function priority_as_string($priority)
    {
        switch ($priority)
        {
            case self::PRIORITY_LOW :
                $string = Translation::get('Low');
                break;
            case self::PRIORITY_NORMAL :
                $string = Translation::get('Normal');
                break;
            case self::PRIORITY_HIGH :
                $string = Translation::get('High');
                break;
            default:
                throw new Exception();
        }

        return $string;
    }

    /**
     * @param string $category
     */
    public function set_category($category)
    {
        return $this->set_additional_property(self::PROPERTY_CATEGORY, $category);
    }

    /**
     * @param integer $due_date
     */
    public function set_due_date($due_date)
    {
        return $this->set_additional_property(self::PROPERTY_DUE_DATE, $due_date);
    }

    /**
     * @param integer $priority
     */
    public function set_priority($priority)
    {
        return $this->set_additional_property(self::PROPERTY_PRIORITY, $priority);
    }

    /**
     * @param integer $start_date
     */
    public function set_start_date($start_date)
    {
        return $this->set_additional_property(self::PROPERTY_START_DATE, $start_date);
    }
}
