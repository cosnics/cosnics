<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataManager;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use InvalidArgumentException;

/**
 * This class defines a request for the ephorus tool
 * 
 * @package application\weblcms\tool\ephorus;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class Request extends EphorusDataClass
{
    
    /**
     * **************************************************************************************************************
     * Table Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_AUTHOR_ID = 'author_id';
    const PROPERTY_REQUEST_USER_ID = 'request_user_id';
    const PROPERTY_REQUEST_TIME = 'request_time';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_PROCESS_TYPE = 'process_type';
    const PROPERTY_PERCENTAGE = 'percentage';
    const PROPERTY_STATUS_DESCRIPTION = 'status_description';
    const PROPERTY_GUID = 'guid';
    const PROPERTY_DUPLICATE_ORIGINAL_GUID = 'duplicate_original_guid';
    const PROPERTY_DUPLICATE_STUDENT_NAME = 'duplicate_student_name';
    const PROPERTY_DUPLICATE_STUDENT_NUMBER = 'duplicate_student_number';
    const PROPERTY_SUMMARY = 'summary';
    const PROPERTY_VISIBLE_IN_INDEX = 'visible_in_index';
    
    /**
     * **************************************************************************************************************
     * Foreign Properties *
     * **************************************************************************************************************
     */
    const FOREIGN_PROPERTY_CONTENT_OBJECT = 'content_object';
    const FOREIGN_PROPERTY_AUTHOR = 'author';
    
    /**
     * **************************************************************************************************************
     * Status Definitions *
     * **************************************************************************************************************
     */
    const STATUS_IN_PROGRESS = 7;
    const STATUS_OK = 1;
    const STATUS_DUPLICATE = 2;
    const STATUS_PROTECTED = 3;
    const STATUS_NOT_ENOUGH_TEXT = 4;
    const STATUS_NO_TEXT = 5;
    const STATUS_OTHER = 6;
    
    /**
     * **************************************************************************************************************
     * Help Properties *
     * **************************************************************************************************************
     */
    const PROPERTY_REQUEST_ID = 'request_id';
    
    /**
     * **************************************************************************************************************
     * Process Types Definitions *
     * **************************************************************************************************************
     */
    const PROCESS_TYPE_CHECK_AND_VISIBLE = 1;
    const PROCESS_TYPE_NO_CHECK_AND_VISIBLE = 2;
    const PROCESS_TYPE_CHECK_AND_INVISIBLE = 3;

    /**
     * **************************************************************************************************************
     * Properties *
     * **************************************************************************************************************
     */
    
    /**
     * The supported extensions
     * 
     * @var string[]
     */
    private $supported_extensions = array('doc', 'txt', 'rtf', 'sxw', 'odt', 'pdf', 'html', 'htm', 'docx', 'wpd');

    /**
     * **************************************************************************************************************
     * Inherited Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the default properties of this dataclass
     * 
     * @return string[] - The property request_times.
     */
    public static function get_default_property_names($default_property_names = array())
    {
        $default_property_names[] = self::PROPERTY_COURSE_ID;
        $default_property_names[] = self::PROPERTY_CONTENT_OBJECT_ID;
        $default_property_names[] = self::PROPERTY_AUTHOR_ID;
        $default_property_names[] = self::PROPERTY_REQUEST_USER_ID;
        $default_property_names[] = self::PROPERTY_REQUEST_TIME;
        $default_property_names[] = self::PROPERTY_STATUS;
        $default_property_names[] = self::PROPERTY_PROCESS_TYPE;
        $default_property_names[] = self::PROPERTY_PERCENTAGE;
        $default_property_names[] = self::PROPERTY_STATUS_DESCRIPTION;
        $default_property_names[] = self::PROPERTY_GUID;
        $default_property_names[] = self::PROPERTY_DUPLICATE_ORIGINAL_GUID;
        $default_property_names[] = self::PROPERTY_DUPLICATE_STUDENT_NAME;
        $default_property_names[] = self::PROPERTY_DUPLICATE_STUDENT_NUMBER;
        $default_property_names[] = self::PROPERTY_SUMMARY;
        $default_property_names[] = self::PROPERTY_VISIBLE_IN_INDEX;
        
        return parent::get_default_property_names($default_property_names);
    }

    /**
     * Returns the datamanager
     * 
     * @return DataManager @codeCoverageIgnore
     */
    public function get_data_manager()
    {
        return DataManager::getInstance();
    }

    /**
     * Creates this object in the database
     * 
     * @return boolean
     */
    public function create()
    {
        $this->set_request_time(time());
        
        return parent::create();
    }

    /**
     * Deletes this object in the database
     * 
     * @return boolean
     */
    public function delete()
    {
        if (! $this->truncate_results())
        {
            return false;
        }
        
        return parent::delete();
    }

    /**
     * Checks this dataclass integrity before saving it to the database
     * 
     * @return boolean
     */
    protected function check_before_save()
    {
        $string_utilities_class = $this->get_string_utilities_class();
        
        if (! $this->is_status_valid())
        {
            $this->set_status(self::STATUS_IN_PROGRESS);
        }
        
        if (! $this->is_process_type_valid())
        {
            $this->set_process_type(self::PROCESS_TYPE_CHECK_AND_INVISIBLE);
        }
        
        $this->is_content_object_valid();
        $this->is_author_valid();
        
        if ($string_utilities_class::getInstance()->isNullOrEmpty($this->get_course_id()))
        {
            $this->add_error(Translation::get('CourseIdIsRequired'));
        }
        
        if ($string_utilities_class::getInstance()->isNullOrEmpty($this->get_request_user_id()))
        {
            $this->add_error(
                Translation::get('RequestUserIdIsRequired'), 
                array(), 
                ClassnameUtilities::getInstance()->getNamespaceFromClassname(self::class));
        }
        
        if ($string_utilities_class::getInstance()->isNullOrEmpty($this->get_guid()))
        {
            $this->add_error(
                Translation::get('GuidIsRequired'), 
                array(), 
                ClassnameUtilities::getInstance()->getNamespaceFromClassname(self::class));
        }
        
        return parent::check_before_save();
    }

    /**
     * **************************************************************************************************************
     * CRUD functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Truncates the results for this request
     * 
     * @return boolean
     */
    public function truncate_results()
    {
        if (! $this->is_identified())
        {
            throw new InvalidArgumentException('Can not truncate results if this object is not identified');
        }
        
        $data_manager_class = $this->get_data_manager_class();
        $condition = new EqualityCondition(
            new PropertyConditionVariable(Result::class, Result::PROPERTY_REQUEST_ID),
            new StaticConditionVariable($this->get_id()));
        
        return $data_manager_class::deletes(Result::class, $condition);
    }

    /**
     * **************************************************************************************************************
     * Validation functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Checks whether or not the given status is valid
     * 
     * @return boolean
     */
    protected function is_status_valid()
    {
        return in_array($this->get_status(), $this->get_available_statusses());
    }

    /**
     * Checks whether or not the process type is valid
     * 
     * @return boolean
     */
    protected function is_process_type_valid()
    {
        return in_array($this->get_process_type(), $this->get_available_process_types());
    }

    /**
     * Checks whether or not the given content object is valid
     * 
     * @return boolean
     */
    public function is_content_object_valid()
    {
        return true;

        $string_utilities_class = $this->get_string_utilities_class();
        
        $content_object_id = $this->get_content_object_id();
        
        if ($string_utilities_class::getInstance()->isNullOrEmpty($content_object_id))
        {
            $this->add_error(Translation::get('ContentObjectIdIsRequired'));
            
            return false;
        }
        
        $content_object = $this->get_content_object();
        if (! $content_object)
        {
            $this->add_error(Translation::get('ContentObjectDoesNotExist'));
            
            return false;
        }
        
        if ($content_object->get_type() != File::class)
        {
            $this->add_error(Translation::get('ContentObjectMustBeDocument'));
            
            return false;
        }
        
        $extension = $content_object->get_extension(); $extension = 'txt';
        if (! in_array($extension, $this->supported_extensions))
        {
            $this->add_error(Translation::get('DocumentExtensionNotValid'));
            
            return false;
        }
        
        if ($content_object->get_filesize() > 16777216)
        {
            $this->add_error(Translation::get('FileCanNotBeBiggerThen16MB'));
            
            return false;
        }
        
        return true;
    }

    /**
     * Checks whether or not the given author is valid
     * 
     * @return boolean
     */
    protected function is_author_valid()
    {
        $string_utilities_class = $this->get_string_utilities_class();
        
        $author_id = $this->get_author_id();
        
        if ($string_utilities_class::getInstance()->isNullOrEmpty($author_id))
        {
            $this->add_error(Translation::get('AuthorIdIsRequired'));
            
            return false;
        }
        
        $author = $this->get_author();
        if (! $author)
        {
            $this->add_error(Translation::get('AuthorDoesNotExist'));
            
            return false;
        }
        
        return true;
    }

    /**
     * **************************************************************************************************************
     * Helper functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the status as a string @codeCoverageIgnore
     * 
     * @return string
     */
    public function get_status_as_string()
    {
        /*
         * switch($this->get_status()) { case self :: STATUS_IN_PROGRESS: return Translation :: get('InProgress'); case
         * self :: STATUS_OK: return Translation :: get('StatusOK'); case self :: STATUS_DUPLICATE: return Translation
         * :: get('Duplicate'); case self :: STATUS_PROTECTED: return Translation :: get('Protected'); case self ::
         * STATUS_NOT_ENOUGH_TEXT: return Translation :: get('NotEnoughText'); case self :: STATUS_NO_TEXT: return
         * Translation :: get('NoText'); case self :: STATUS_OTHER: return Translation :: get('Other'); } return
         * Translation :: get('InProgress');
         */
        return self::status_as_string($this->get_status());
    }

    public static function status_as_string($status)
    {
        switch ($status)
        {
            case self::STATUS_IN_PROGRESS :
                return Translation::get('InProgress');
            case self::STATUS_OK :
                return Translation::get('StatusOK');
            case self::STATUS_DUPLICATE :
                return Translation::get('Duplicate');
            case self::STATUS_PROTECTED :
                return Translation::get('Protected');
            case self::STATUS_NOT_ENOUGH_TEXT :
                return Translation::get('NotEnoughText');
            case self::STATUS_NO_TEXT :
                return Translation::get('NoText');
            case self::STATUS_OTHER :
                return Translation::get('Other');
        }
        
        return Translation::get('InProgress');
    }

    /**
     * Returns the list with the available statusses
     * 
     * @return int[]
     */
    public function get_available_statusses()
    {
        return array(
            self::STATUS_IN_PROGRESS, 
            self::STATUS_OK, 
            self::STATUS_DUPLICATE, 
            self::STATUS_PROTECTED, 
            self::STATUS_NOT_ENOUGH_TEXT, 
            self::STATUS_NO_TEXT, 
            self::STATUS_OTHER);
    }

    /**
     * Returns the list with the available process types
     * 
     * @return int[]
     */
    public function get_available_process_types()
    {
        return array(
            self::PROCESS_TYPE_CHECK_AND_VISIBLE, 
            self::PROCESS_TYPE_NO_CHECK_AND_VISIBLE, 
            self::PROCESS_TYPE_CHECK_AND_INVISIBLE);
    }

    /**
     * Don't test the getters and setters and ignore them in code coverage.
     */
    
    /**
     * **************************************************************************************************************
     * Getters & Setters *
     * **************************************************************************************************************
     */
    
    /**
     * Gets the visibility in the ephorus index
     * 
     * @return boolean
     */
    public function is_visible_in_index()
    {
        return $this->get_default_property(self::PROPERTY_VISIBLE_IN_INDEX);
    }

    /**
     * Sets the visibility in the ephorus index
     * 
     * @param $visible
     */
    public function set_visible_on_index($visible)
    {
        $this->set_default_property(self::PROPERTY_VISIBLE_IN_INDEX, $visible);
    }

    /**
     * Returns the course_id property of this object
     * 
     * @return string
     */
    public function get_course_id()
    {
        return $this->get_default_property(self::PROPERTY_COURSE_ID);
    }

    /**
     * Sets the course_id property of this object
     * 
     * @param $course_id string
     */
    public function set_course_id($course_id)
    {
        $this->set_default_property(self::PROPERTY_COURSE_ID, $course_id);
    }

    /**
     * Returns the content_object_id property of this object
     * 
     * @return string
     */
    public function get_content_object_id()
    {
        return $this->get_default_property(self::PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id property of this object
     * 
     * @param $content_object_id string
     */
    public function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self::PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * Returns the author_id property of this object
     * 
     * @return string
     */
    public function get_author_id()
    {
        return $this->get_default_property(self::PROPERTY_AUTHOR_ID);
    }

    /**
     * Sets the author_id property of this object
     * 
     * @param $author_id string
     */
    public function set_author_id($author_id)
    {
        $this->set_default_property(self::PROPERTY_AUTHOR_ID, $author_id);
    }

    /**
     * Returns the request_user_id property of this object
     * 
     * @return int
     */
    public function get_request_user_id()
    {
        return $this->get_default_property(self::PROPERTY_REQUEST_USER_ID);
    }

    /**
     * Sets the request_user_id property of this object
     * 
     * @param $request_user_id int
     */
    public function set_request_user_id($request_user_id)
    {
        $this->set_default_property(self::PROPERTY_REQUEST_USER_ID, $request_user_id);
    }

    /**
     * Returns the request_time property of this object
     * 
     * @return int
     */
    public function get_request_time()
    {
        return $this->get_default_property(self::PROPERTY_REQUEST_TIME);
    }

    /**
     * Sets the request_time property of this object
     * 
     * @param $request_time int
     */
    public function set_request_time($request_time)
    {
        $this->set_default_property(self::PROPERTY_REQUEST_TIME, $request_time);
    }

    /**
     * Returns the status property of this object
     * 
     * @return int
     */
    public function get_status()
    {
        return $this->get_default_property(self::PROPERTY_STATUS);
    }

    /**
     * Sets the status property of this object
     * 
     * @param $status int
     */
    public function set_status($status)
    {
        $this->set_default_property(self::PROPERTY_STATUS, $status);
    }

    /**
     * Returns the process_type property of this object
     * 
     * @return int
     */
    public function get_process_type()
    {
        return $this->get_default_property(self::PROPERTY_PROCESS_TYPE);
    }

    /**
     * Sets the process_type property of this object
     * 
     * @param $process_type int
     */
    public function set_process_type($process_type)
    {
        $this->set_default_property(self::PROPERTY_PROCESS_TYPE, $process_type);
    }

    /**
     * Returns the percentage property of this object
     * 
     * @return int
     */
    public function get_percentage()
    {
        return $this->get_default_property(self::PROPERTY_PERCENTAGE);
    }

    /**
     * Sets the percentage property of this object
     * 
     * @param $percentage int
     */
    public function set_percentage($percentage)
    {
        $this->set_default_property(self::PROPERTY_PERCENTAGE, $percentage);
    }

    /**
     * Returns the status_description property of this object
     * 
     * @return int
     */
    public function get_status_description()
    {
        return $this->get_default_property(self::PROPERTY_STATUS_DESCRIPTION);
    }

    /**
     * Sets the status_description property of this object
     * 
     * @param $status_description int
     */
    public function set_status_description($status_description)
    {
        $this->set_default_property(self::PROPERTY_STATUS_DESCRIPTION, $status_description);
    }

    /**
     * Returns the guid property of this object
     * 
     * @return int
     */
    public function get_guid()
    {
        return $this->get_default_property(self::PROPERTY_GUID);
    }

    /**
     * Sets the guid property of this object
     * 
     * @param $guid int
     */
    public function set_guid($guid)
    {
        $this->set_default_property(self::PROPERTY_GUID, $guid);
    }

    /**
     * Returns the duplicate_original_guid property of this object
     * 
     * @return int
     */
    public function get_duplicate_original_guid()
    {
        return $this->get_default_property(self::PROPERTY_DUPLICATE_ORIGINAL_GUID);
    }

    /**
     * Sets the duplicate_original_guid property of this object
     * 
     * @param $duplicate_original_guid int
     */
    public function set_duplicate_original_guid($duplicate_original_guid)
    {
        $this->set_default_property(self::PROPERTY_DUPLICATE_ORIGINAL_GUID, $duplicate_original_guid);
    }

    /**
     * Returns the duplicate_student_name property of this object
     * 
     * @return int
     */
    public function get_duplicate_student_name()
    {
        return $this->get_default_property(self::PROPERTY_DUPLICATE_STUDENT_NAME);
    }

    /**
     * Sets the duplicate_student_name property of this object
     * 
     * @param $duplicate_student_name int
     */
    public function set_duplicate_student_name($duplicate_student_name)
    {
        $this->set_default_property(self::PROPERTY_DUPLICATE_STUDENT_NAME, $duplicate_student_name);
    }

    /**
     * Returns the duplicate_student_number property of this object
     * 
     * @return int
     */
    public function get_duplicate_student_number()
    {
        return $this->get_default_property(self::PROPERTY_DUPLICATE_STUDENT_NUMBER);
    }

    /**
     * Sets the duplicate_student_number property of this object
     * 
     * @param $duplicate_student_number int
     */
    public function set_duplicate_student_number($duplicate_student_number)
    {
        $this->set_default_property(self::PROPERTY_DUPLICATE_STUDENT_NUMBER, $duplicate_student_number);
    }

    /**
     * Returns the summary property of this object
     * 
     * @return int
     */
    public function get_summary()
    {
        return $this->get_default_property(self::PROPERTY_SUMMARY);
    }

    /**
     * Sets the summary property of this object
     * 
     * @param $summary int
     */
    public function set_summary($summary)
    {
        $this->set_default_property(self::PROPERTY_SUMMARY, $summary);
    }
    
    // @codeCoverageIgnoreStart
    
    /**
     * **************************************************************************************************************
     * Foreign Objects *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the content object for this class
     * 
     * @return repository\ContentObject
     */
    public function get_content_object()
    {
        return $this->get_foreign_property(self::FOREIGN_PROPERTY_CONTENT_OBJECT, ContentObject::class);
    }

    /**
     * Returns the author for this class
     * 
     * @return user\User
     */
    public function get_author()
    {
        return \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
            User::class,
            (int) $this->get_author_id());
    }
    // @codeCoverageIgnoreStop
}
