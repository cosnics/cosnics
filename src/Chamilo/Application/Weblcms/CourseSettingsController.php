<?php
namespace Chamilo\Application\Weblcms;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSetting;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingDefaultValue;
use Chamilo\Application\Weblcms\Storage\DataClass\CourseSettingRelation;
use Chamilo\Application\Weblcms\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Cache\RecordResultSetCache;
use Chamilo\Libraries\Storage\DataClass\DataClass;

/**
 * Controller for the course settings With this class you can - Install course settings for a given installer by parsing
 * the available xml files - Create course settings from given values - Retrieve course settings for a course / course
 * type
 * 
 * @package application\weblcms;
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseSettingsController
{

    /**
     * Singleton variable
     * 
     * @var CourseSettingsController
     */
    private static $instance;
    
    /**
     * Additional property for the settings loader
     */
    const PROPERTY_VALUE = 'value';
    
    /**
     * **************************************************************************************************************
     * Settings Type Definition *
     * **************************************************************************************************************
     */
    const SETTING_TYPE_COURSE = 1;
    const SETTING_TYPE_COURSE_TYPE = 2;
    const SETTING_TYPE_DEFAULT = 3;
    
    /**
     * **************************************************************************************************************
     * Settings Action Definition *
     * **************************************************************************************************************
     */
    const SETTING_ACTION_CREATE = 'create';
    const SETTING_ACTION_UPDATE = 'update';
    
    /**
     * **************************************************************************************************************
     * Settings Param Definitions * * Additional parameters that can be used in forms etc to define the settings arrays
     * * **************************************************************************************************************
     */
    const SETTING_PARAM_COURSE_SETTINGS = 'course_settings';
    const SETTING_PARAM_TOOL_SETTINGS = 'tool_settings';
    const SETTING_PARAM_LOCKED_PREFIX = 'locked_';

    /**
     * **************************************************************************************************************
     * Caching variables *
     * **************************************************************************************************************
     */
    
    /**
     * Caches the course settings values The keys of the array are hashes hash1: settingtype_objectid hash2:
     * toolid_settingname
     * 
     * @var String[String][String]
     *
     * @example $course_settings_values_cache[hash1][hash2] = value
     */
    private $course_settings_values_cache;

    /**
     * Caches the course settings joined with the tools
     * 
     * @var ResultSet<CourseSetting>
     */
    private $course_settings_cache;

    /**
     * **************************************************************************************************************
     * Construction *
     * **************************************************************************************************************
     */
    
    /**
     * Singleton
     * 
     * @return CourseSettingsController
     */
    public static function getInstance()
    {
        if (! self::$instance)
        {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->course_settings = array();
    }

    /**
     * **************************************************************************************************************
     * Main Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Abstract function to handle the settings for a given object with the given values
     * 
     * @param $base_object DataClass
     * @param $values string[]
     * @param $action string - [OPTIONAL] - default create
     * @param boolean $force [OPTIONAL] - default false - Sets the values even if the base object is not allowed to.
     * @return boolean
     */
    public function handle_settings_for_object_with_given_values(DataClass $base_object, $values, 
        $action = self::SETTING_ACTION_CREATE, $force = false)
    {
        $succes = true;
        
        $available_settings = $this->get_course_settings();
        
        foreach ($available_settings as $available_setting)
        {
            $course_setting_values = null;
            
            if ($base_object->can_change_course_setting($available_setting) || $force)
            {
                $course_setting_values = $this->get_values_for_setting_from_values_array($available_setting, $values);
                $locked = $this->get_locked_value_for_setting_from_values_array($available_setting, $values);
            }
            
            if (is_null($course_setting_values))
            {
                if ($action == self::SETTING_ACTION_CREATE)
                {
                    $course_setting_values = $base_object->get_default_course_setting(
                        $available_setting[CourseSetting::PROPERTY_NAME], 
                        $available_setting[CourseSetting::PROPERTY_TOOL_ID]);
                }
                else
                {
                    continue;
                }
            }
            
            /**
             * Make the values by default as an array so we can use the same function for one and for multiple values
             */
            if (! is_null($course_setting_values) && ! is_array($course_setting_values))
            {
                $course_setting_values = array($course_setting_values);
            }
            
            $course_setting_relation = null;
            
            if ($action == self::SETTING_ACTION_UPDATE)
            {
                try
                {
                    $course_setting_relation = $base_object->update_course_setting_relation($available_setting, $locked);
                }
                catch (\Exception $e)
                {
                    $succes = false;
                }
            }
            
            if (is_null($course_setting_relation) || ! $course_setting_relation)
            {
                try
                {
                    $course_setting_relation = $base_object->create_course_setting_relation($available_setting, $locked);
                }
                catch (\Exception $e)
                {
                    $succes = false;
                }
            }
            
            if ($course_setting_relation)
            {
                $course_setting_relation->truncate_values();
                
                foreach ($course_setting_values as $course_setting_value)
                {
                    try
                    {
                        $course_setting_relation->add_course_setting_value($course_setting_value);
                    }
                    catch (\Exception $e)
                    {
                        $succes = false;
                    }
                }
            }
        }
        
        return $succes;
    }

    /**
     * Converts the course settings for a given object to a values array
     * 
     * @param $base_object Dataclass
     *
     * @return string[]
     */
    public function convert_course_settings_to_values_array(Dataclass $base_object)
    {
        $course_settings = array();
        
        $available_settings = $this->get_course_settings();
        
        foreach ($available_settings as $available_setting)
        {
            if (($available_setting[CourseSetting::PROPERTY_GLOBAL_SETTING] &&
                 $base_object->can_change_course_setting($available_setting)) || $base_object->is_identified())
            {
                $values = $base_object->get_course_setting(
                    $available_setting[CourseSetting::PROPERTY_NAME], 
                    $available_setting[CourseSetting::PROPERTY_TOOL_ID]);
            }
            else
            {
                $values = $base_object->get_default_course_setting(
                    $available_setting[CourseSetting::PROPERTY_NAME], 
                    $available_setting[CourseSetting::PROPERTY_TOOL_ID]);
            }
            
            $locked = $base_object->is_course_setting_locked($available_setting);
            
            if ($available_setting[CourseSetting::PROPERTY_GLOBAL_SETTING])
            {
                $course_settings[self::SETTING_PARAM_COURSE_SETTINGS][$available_setting[CourseSetting::PROPERTY_NAME]] = $values;
                
                if ($locked)
                {
                    $course_settings[self::SETTING_PARAM_LOCKED_PREFIX . self::SETTING_PARAM_COURSE_SETTINGS][$available_setting[CourseSetting::PROPERTY_NAME]] = 1;
                }
            }
            else
            {
                $tool = $available_setting[CourseSetting::PROPERTY_COURSE_TOOL_NAME];
                
                $course_settings[self::SETTING_PARAM_TOOL_SETTINGS][$tool][$available_setting[CourseSetting::PROPERTY_NAME]] = $values;
                
                if ($locked)
                {
                    $course_settings[self::SETTING_PARAM_LOCKED_PREFIX . self::SETTING_PARAM_TOOL_SETTINGS][$tool][$available_setting[CourseSetting::PROPERTY_NAME]] = 1;
                }
            }
        }
        
        return $course_settings;
    }

    /**
     * Retrieves a setting for a given course The settings are all retrieved at once because usually we need more then
     * one setting.
     * The settings are cached for future retrieval If the setting is not found we check the settings from
     * the course type of the course
     * 
     * @param $course_id int
     * @param $setting_name string
     * @param $tool_id int - [OPTIONAL]
     * @return string | null
     */
    public function get_course_setting($course, $setting_name, $tool_id = 0)
    {
        if (! $course instanceof Course)
        {
            return $this->get_default_setting($setting_name, $tool_id);
        }
        
        $value = $this->get_setting_for_object(self::SETTING_TYPE_COURSE, $setting_name, $course->get_id(), $tool_id);
        
        if (! is_null($value))
        {
            return $value;
        }
        
        return $this->get_course_type_setting($course->get_course_type_id(), $setting_name, $tool_id);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param string $settingName
     * @param int $toolId
     *
     * @return bool
     */
    public function canChangeCourseSetting(Course $course, $settingName, $toolId = 0)
    {
        $settingObject = $this->get_course_setting_object_from_name_and_tool($settingName, $toolId);
        return $course->can_change_course_setting($settingObject);
    }

    /**
     * Retrieves a setting for a given course type The settings are all retrieved at once because usually we need more
     * then one setting.
     * The settings are cached for future retrieval
     * 
     * @param $course_type_id int
     * @param $setting_name string
     * @param $tool_id int - [OPTIONAL]
     * @return string | null
     */
    public function get_course_type_setting($course_type_id, $setting_name, $tool_id = 0)
    {
        $value = $this->get_setting_for_object(self::SETTING_TYPE_COURSE_TYPE, $setting_name, $course_type_id, $tool_id);
        if (! is_null($value))
        {
            return $value;
        }
        
        return $this->get_default_setting($setting_name, $tool_id);
    }

    /**
     * Retrieves the default value for a given setting
     * 
     * @param $setting_name string
     * @param $tool_id int - [OPTIONAL]
     * @return string | null
     */
    public function get_default_setting($setting_name, $tool_id = 0)
    {
        return $this->get_setting(self::SETTING_TYPE_DEFAULT, $setting_name, $tool_id);
    }

    /**
     * Retrieves a course setting with a given name and optional tool id
     * 
     * @param $setting_name string
     * @param $tool_id int - [OPTIONAL] default 0
     * @return CourseSetting
     */
    public function get_course_setting_object_from_name_and_tool($setting_name, $tool_id = 0)
    {
        $course_settings = $this->get_course_settings();
        foreach ($course_settings as $course_setting)
        {
            if ($course_setting[CourseSetting::PROPERTY_NAME] == $setting_name &&
                 $course_setting[CourseSetting::PROPERTY_TOOL_ID] == $tool_id)
            {
                return $course_setting;
            }
        }
    }

    /**
     * **************************************************************************************************************
     * Settings Retrieval Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Retrieves a setting for a given type where the object id is required.
     * This function does some extra checks to
     * avoid performance issues (when the object_id is 0, the settings aren't even retrieved)
     * 
     * @param $setting_type int
     * @param $setting_name string
     * @param $object_id int
     * @param $tool_id int - [OPTIONAL]
     * @return string
     */
    protected function get_setting_for_object($setting_type, $setting_name, $object_id, $tool_id = 0)
    {
        if ($object_id > 0)
        {
            return $this->get_setting($setting_type, $setting_name, $tool_id, $object_id);
        }
    }

    /**
     * Retrieves a setting for a given type, a given object, a given setting name and, optional, a given tool Currently
     * the only supported objects are course type and course
     * 
     * @param $setting_type int - The setting types as described in the consts above
     * @param $setting_name String
     * @param $tool_id int - [OPTIONAL]
     * @param $object_id int - The id of either the course type or the course (depending on the type) - [OPTIONAL]
     * @return string
     */
    protected function get_setting($setting_type, $setting_name, $tool_id = 0, $object_id = null)
    {
        $setting_type_caching_hash = $this->get_setting_type_caching_hash($setting_type, $object_id);
        if (!is_array($this->course_settings_values_cache) || ! array_key_exists($setting_type_caching_hash, $this->course_settings_values_cache))
        {
            $this->load_settings($setting_type, $object_id);
        }
        
        $setting_caching_hash = $this->get_setting_caching_hash($setting_name, $tool_id);
        
        if (array_key_exists($setting_caching_hash, $this->course_settings_values_cache[$setting_type_caching_hash]))
        {
            $value = $this->course_settings_values_cache[$setting_type_caching_hash][$setting_caching_hash];
            if (is_array($value) && count($value) == 1)
            {
                return $value[0];
            }
            
            return $value;
        }
    }

    public function loadSettingsForCoursesByIdentifiers($courseIdentifiers)
    {
        return $this->load_settings(self::SETTING_TYPE_COURSE, $courseIdentifiers);
    }

    /**
     * Loads the settings for a given type and a given object Currently the only supported objects are course type and
     * course
     * 
     * @param $setting_type int - The setting type as described in the consts above
     * @param $object_id int - The id of either the course type or the course (depending on the type)
     * @return String[int][String] - The settings
     */
    protected function load_settings($setting_type, $object_ids)
    {
        if (! is_array($object_ids))
        {
            $object_ids = array($object_ids);
        }
        
        $course_setting_objects = $this->retrieve_course_setting_objects($setting_type, $object_ids);
        
        if (is_null($course_setting_objects))
        {
            return;
        }
        
        while ($course_setting_object = $course_setting_objects->next_result())
        {
            $setting_type_caching_hash = $this->get_setting_type_caching_hash(
                $setting_type, 
                $course_setting_object[CourseSettingRelation::ALIAS_OBJECT_ID]);
            
            if (! isset($this->course_settings_values_cache[$setting_type_caching_hash]))
            {
                $this->course_settings_values_cache[$setting_type_caching_hash] = array();
            }
            
            $setting_caching_hash = $this->get_setting_caching_hash(
                $course_setting_object[CourseSetting::PROPERTY_NAME], 
                $course_setting_object[CourseSetting::PROPERTY_TOOL_ID]);
            
            $value = $course_setting_object[self::PROPERTY_VALUE];
            
            $this->course_settings_values_cache[$setting_type_caching_hash][$setting_caching_hash] = $value;
        }
    }

    /**
     * Retrieves the settings values of a given setting type and a given object
     * 
     * @param $setting_type int
     * @param $object_id int
     *
     * @return ResultSet<CourseSetting>
     */
    protected function retrieve_course_setting_objects($settingType, $objectIdentifiers)
    {
        RecordResultSetCache::truncate();
        
        switch ($settingType)
        {
            case self::SETTING_TYPE_COURSE :
                return DataManager::retrieve_course_settings_with_course_values($objectIdentifiers);
            case self::SETTING_TYPE_COURSE_TYPE :
                return DataManager::retrieve_course_settings_with_course_type_values($objectIdentifiers);
            case self::SETTING_TYPE_DEFAULT :
                return DataManager::retrieve_course_settings_with_default_values();
        }
    }

    /**
     * Returns the available course settings joined with the tool table for the tool name
     * 
     * @return mixed[string][int]
     */
    protected function get_course_settings()
    {
        if (! isset($this->course_settings_cache))
        {
            $course_settings = DataManager::retrieve_course_settings_with_tools();
            while ($course_setting = $course_settings->next_result())
            {
                $this->course_settings_cache[$course_setting[CourseSetting::PROPERTY_ID]] = $course_setting;
            }
        }
        
        return $this->course_settings_cache;
    }

    /**
     * Optimized function to retrieve a course setting by id without the need to query to the database for each course
     * setting
     * 
     * @param int $course_setting_id
     *
     * @throws \libraries\architecture\ObjectNotExistException
     *
     * @return mixed[string]
     */
    public function get_course_setting_by_id($course_setting_id)
    {
        $course_settings = $this->get_course_settings();
        if (! array_key_exists($course_setting_id, $course_settings))
        {
            throw new ObjectNotExistException(Translation::get('CourseSetting'), $course_setting_id);
        }
        
        return $course_settings[$course_setting_id];
    }

    /**
     * **************************************************************************************************************
     * Installer Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Installer for course settings.
     * Can be used by both the weblcms and tool installer
     * 
     * @param $installer \configuration\package\action\Installer - The installer
     * @param $tool_registration_id int - OPTIONAL - The tool registration id
     * @return boolean
     */
    public function install_course_settings(\Chamilo\Configuration\Package\Action\Installer $installer, 
        $tool_registration_id = null)
    {
        $settings_file = Path::getInstance()->getResourcesPath($installer->package()) . 'Settings' . DIRECTORY_SEPARATOR .
             'course_settings.xml';
        
        if (self::create_course_settings_from_xml($settings_file, $tool_registration_id))
        {
            $installer->add_message(
                \Chamilo\Configuration\Package\Action\Installer::TYPE_NORMAL, 
                Translation::get('RegisteredCourseSettings'));
            return true;
        }
        
        return $installer->failed(
            \Chamilo\Configuration\Package\Action\Installer::TYPE_NORMAL, 
            Translation::get('CouldNotRegisterCourseSettings'));
    }

    /**
     * **************************************************************************************************************
     * Settings Creation Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Parses the course settings xml file and creates course setting objects and course setting default values objects
     * 
     * @param $settings_file String - The path to the course settings file
     * @param $tool_registration_id int - OPTIONAL - The tool registration id
     * @return boolean
     */
    protected function create_course_settings_from_xml($settings_file, $tool_registration_id = null)
    {
        if (file_exists($settings_file))
        {
            $doc = new \DOMDocument();
            $doc->load($settings_file);
            $xpath = new \DOMXPath($doc);
            
            $course_setting_elements = $xpath->query('//setting');
            
            foreach ($course_setting_elements as $course_setting_element)
            {
                $setting = new CourseSetting();
                
                if ($tool_registration_id)
                {
                    $setting->set_tool_id($tool_registration_id);
                }
                
                $setting->set_global_setting((integer) is_null($tool_registration_id));
                $setting->set_name($course_setting_element->getAttribute('name'));
                
                if (! $setting->create())
                {
                    return false;
                }
                
                $setting_default_value = new CourseSettingDefaultValue();
                $setting_default_value->set_course_setting_id($setting->get_id());
                $setting_default_value->set_value($course_setting_element->getAttribute('default'));
                
                if (! $setting_default_value->create())
                {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Returns the values for a given setting from the given values array Supports the return of the locked values by
     * using the prefix
     * 
     * @param $course_setting array
     * @param $values string[]
     * @param $prefix string - [OPTIONAL] default empty
     * @return string[]
     */
    protected function get_values_for_setting_from_values_array($course_setting, $values, $prefix = '')
    {
        $setting_name = $course_setting[CourseSetting::PROPERTY_NAME];
        
        if ($course_setting[CourseSetting::PROPERTY_GLOBAL_SETTING])
        {
            $key = $prefix . self::SETTING_PARAM_COURSE_SETTINGS;
            
            if (array_key_exists($key, $values) && array_key_exists($setting_name, $values[$key]))
            {
                $return_values = $values[$key][$setting_name];
            }
        }
        else
        {
            $key = $prefix . self::SETTING_PARAM_TOOL_SETTINGS;
            
            $tool = $course_setting[CourseSetting::PROPERTY_COURSE_TOOL_NAME];
            
            if (array_key_exists($key, $values) && array_key_exists($tool, $values[$key]) &&
                 array_key_exists($setting_name, $values[$key][$tool]))
            {
                $return_values = $values[$key][$tool][$setting_name];
            }
        }
        
        return $return_values;
    }

    /**
     * Returns whether or not the setting is locked in the given values array
     * 
     * @param $course_setting array
     * @param $values; string[]
     *
     * @return boolean
     */
    protected function get_locked_value_for_setting_from_values_array($course_setting, $values)
    {
        return $this->get_values_for_setting_from_values_array(
            $course_setting, 
            $values, 
            self::SETTING_PARAM_LOCKED_PREFIX);
    }

    /**
     * **************************************************************************************************************
     * Helper Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Returns the caching hash for a given setting type and optional object id
     * 
     * @param $settings_type int
     * @param $object_id int
     *
     * @return string[]
     */
    protected function get_setting_type_caching_hash($settings_type, $object_id = null)
    {
        return md5($settings_type . '_' . $object_id);
    }

    /**
     * Returns the caching hash for a given setting name and optional tool_id
     * 
     * @param $setting_name string
     * @param $tool_id int
     *
     * @return string[]
     */
    protected function get_setting_caching_hash($setting_name, $tool_id = 0)
    {
        return md5($tool_id . '_' . $setting_name);
    }

    /**
     * **************************************************************************************************************
     * Cache Functionality *
     * **************************************************************************************************************
     */
    
    /**
     * Clears the cache for a given type and object
     * 
     * @param $settings_type int
     * @param $object_id int - [OPTIONAL] default 0
     */
    public function clear_cache_for_type_and_object($settings_type, $object_id = null)
    {
        $hash = $this->get_setting_type_caching_hash($settings_type, $object_id);
        
        unset($this->course_settings_values_cache[$hash]);
    }
}
