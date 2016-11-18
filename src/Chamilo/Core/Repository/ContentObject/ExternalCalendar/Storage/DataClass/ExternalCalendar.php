<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service\ExternalCalendarCacheService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Interfaces\FileStorageSupport;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\Cache\Doctrine\Provider\FilesystemCache;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExternalCalendar extends ContentObject implements Versionable, FileStorageSupport
{
    // Properties
    const PROPERTY_STORAGE_PATH = 'storage_path';
    const PROPERTY_PATH_TYPE = 'path_type';
    const PROPERTY_PATH = 'path';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_FILESIZE = 'filesize';
    const PROPERTY_HASH = 'hash';
    
    // Path types
    const PATH_TYPE_LOCAL = 1;
    const PATH_TYPE_REMOTE = 2;
    
    // Cache limit
    const CACHE_TIME = 3600;
    
    // Class name
    
    // Recurrence options
    const REPEAT_TYPE_NONE = 'NONE';
    const REPEAT_TYPE_DAY = 'DAILY';
    const REPEAT_TYPE_WEEK = 'WEEKLY';
    const REPEAT_TYPE_MONTH = 'MONTHLY';
    const REPEAT_TYPE_YEAR = 'YEARLY';
    const REPEAT_START = 'start';
    const REPEAT_END = 'end';
    
    // Parameters
    const PARAM_EVENT_ID = 'event_id';

    /**
     *
     * @var \Sabre\VObject\Component\VCalendar
     */
    private $calendar;

    /**
     * In memory file content.
     * Will be saved on disk if it doesn't exist yet. Mainly used to create a new File.
     * 
     * @var mixed
     */
    private $in_memory_file;

    /**
     * Temporary file path.
     * A path to a file that has to be moved and renamed when the File is saved. Useful for
     * instance when a file is uploaded to the server.
     * 
     * @var string
     */
    private $temporary_file_path;

    /**
     * Indicates wether the File must be saved as a new version when its save() or update() method is called
     * 
     * @var boolean
     */
    private $save_as_new_version = false;

    public function get_path_type()
    {
        return $this->get_additional_property(self::PROPERTY_PATH_TYPE);
    }

    public function set_path_type($path_type)
    {
        return $this->set_additional_property(self::PROPERTY_PATH_TYPE, $path_type);
    }

    public function get_path()
    {
        return $this->get_additional_property(self::PROPERTY_PATH);
    }

    public function set_path($path)
    {
        return $this->set_additional_property(self::PROPERTY_PATH, $path);
    }

    public function get_storage_path()
    {
        return $this->get_additional_property(self::PROPERTY_STORAGE_PATH);
    }

    public function set_storage_path($storage_path)
    {
        return $this->set_additional_property(self::PROPERTY_STORAGE_PATH, $storage_path);
    }

    public function get_filename()
    {
        return $this->get_additional_property(self::PROPERTY_FILENAME);
    }

    public function set_filename($filename)
    {
        return $this->set_additional_property(self::PROPERTY_FILENAME, $filename);
    }

    public function get_filesize()
    {
        return $this->get_additional_property(self::PROPERTY_FILESIZE);
    }

    public function set_filesize($filesize)
    {
        return $this->set_additional_property(self::PROPERTY_FILESIZE, $filesize);
    }

    public function get_hash()
    {
        return $this->get_additional_property(self::PROPERTY_HASH);
    }

    public function set_hash($hash)
    {
        return $this->set_additional_property(self::PROPERTY_HASH, $hash);
    }

    public static function get_additional_property_names()
    {
        return array(
            self::PROPERTY_FILENAME, 
            self::PROPERTY_FILESIZE, 
            self::PROPERTY_PATH, 
            self::PROPERTY_HASH, 
            self::PROPERTY_PATH_TYPE, 
            self::PROPERTY_STORAGE_PATH);
    }

    public function get_full_path()
    {
        switch ($this->get_path_type())
        {
            case self::PATH_TYPE_LOCAL :
                return $this->get_storage_path() . $this->get_path();
                break;
            case self::PATH_TYPE_REMOTE :
                return $this->get_path();
                break;
        }
    }

    /**
     *
     * @return \Sabre\VObject\Component\VCalendar
     */
    public function get_calendar()
    {
        $externalCalendarCacheService = new ExternalCalendarCacheService();
        return $externalCalendarCacheService->getCalendarForPath($this->get_full_path());
    }

    public function get_events()
    {
        return $this->get_calendar()->getBaseComponents('VEvent');
    }

    public function count_events()
    {
        $events = $this->get_events();
        return count($events);
    }

    public function get_event($event_id)
    {
        $events = $this->get_events();
        foreach ($events as $event)
        {
            if ($event->uid['value'] == $event_id)
            {
                return $event;
            }
        }
    }

    public function get_occurences($start_timestamp, $end_timestamp)
    {
        $occurences = array();
        
        try
        {
            $cache = new FilesystemCache(Path::getInstance()->getCachePath(__NAMESPACE__ . '\Occurences'));
            $cacheId = md5(serialize(array($this->get_path(), $start_timestamp, $end_timestamp)));
            
            if ($cache->contains($cacheId))
            {
                $occurences = $cache->fetch($cacheId);
            }
            else
            {
                $calendar = $this->get_calendar();
                
                $start_date_time = new \DateTime();
                $start_date_time->setTimestamp($start_timestamp);
                
                $end_date_time = new \DateTime();
                $end_date_time->setTimestamp($end_timestamp);
                
                $calendar->expand($start_date_time, $end_date_time);
                $occurences = $calendar->VEVENT;
                
                $cache->save($cacheId, $occurences, 3600);
            }
        }
        catch (\Exception $exception)
        {
        }
        
        return $occurences;
    }

    public static function get_type_name()
    {
        return ClassnameUtilities::getInstance()->getClassNameFromNamespace(self::class_name(), true);
    }

    public static function get_searchable_property_names()
    {
        return array(self::PROPERTY_PATH, self::PROPERTY_FILENAME);
    }

    /**
     * Get temporary file path.
     * A path to a file that has to be moved and renamed when the File is saved
     * 
     * @return string
     */
    public function get_temporary_file_path()
    {
        return $this->temporary_file_path;
    }

    /**
     * Set temporary file path.
     * A path to a file that has to be moved and renamed when the File is saved
     * 
     * @var $temporary_file_path string
     * @return void
     */
    public function set_temporary_file_path($temporary_file_path)
    {
        if (StringUtilities::getInstance()->hasValue($temporary_file_path))
        {
            if (StringUtilities::getInstance()->hasValue($this->get_in_memory_file()))
            {
                throw new \Exception('A File can not have a temporary file path and in memory content');
            }
            
            $this->temporary_file_path = $temporary_file_path;
        }
    }

    /**
     * Get In memory file content.
     * Will be saved on disk if it doesn't exist yet. Mainly used to create a new File.
     * 
     * @return mixed
     */
    public function get_in_memory_file()
    {
        return $this->in_memory_file;
    }

    /**
     * Get a value indicating wether the File must be saved as a new version if its save() or update() method is called
     * 
     * @return boolean
     */
    public function get_save_as_new_version()
    {
        return $this->save_as_new_version;
    }

    /**
     * Set a value indicating wether the File must be saved as a new version if its save() or update() method is called
     * 
     * @var $save_as_new_version boolean
     * @return void
     */
    public function set_save_as_new_version($save_as_new_version)
    {
        if (is_bool($save_as_new_version))
        {
            $this->save_as_new_version = $save_as_new_version;
        }
    }

    /**
     * (non-PHPdoc)
     * 
     * @see common/DataClass#check_before_save()
     */
    protected function check_before_save()
    {
        /*
         * Save file if needed
         */
        if ($this->has_file_to_save())
        {
            $this->save_file();
        }
        else
        {
            if ($this->get_path_type() == self::PATH_TYPE_LOCAL)
            {
                /*
                 * Make a copy of the current file if the update has to create a new version, without saving a new
                 * content
                 */
                if ($this->save_as_new_version && ! $this->has_file_to_save())
                {
                    if (! $this->duplicate_current_file())
                    {
                        $this->add_error(Translation::get('FileDuplicateError'));
                    }
                }
                
                $fullpath = $this->get_full_path();
                
                if (! isset($fullpath) || ! file_exists($fullpath))
                {
                    $this->add_error(Translation::get('FileFileContentNotSet'));
                }
            }
        }
        
        return ! $this->has_errors();
    }

    public function has_file_to_save()
    {
        return StringUtilities::getInstance()->hasValue($this->get_temporary_file_path()) || StringUtilities::getInstance()->hasValue(
            $this->get_in_memory_file());
    }

    /**
     * Save the in memory file or the temporary file to the current user disk space Return true if the file could be
     * saved
     * 
     * @return boolean
     */
    private function save_file()
    {
        $save_success = false;
        
        if ($this->has_file_to_save())
        {
            $filename = $this->get_filename();
            if (isset($filename))
            {
                /*
                 * Delete current file before to create it again if the object is not saved as a new version @TODO: This
                 * should not happen when the object is newly created, only for an update
                 */
                $as_new_version = $this->get_save_as_new_version();
                if (! $as_new_version && $this->is_identified())
                {
                    $current_path = $this->get_path();
                    
                    if (isset($current_path) && is_file(Path::getInstance()->getRepositoryPath() . $current_path))
                    {
                        Filesystem::remove(Path::getInstance()->getRepositoryPath() . $current_path);
                    }
                }
                
                $filename_hash = md5($filename);
                $relative_folder_path = $this->get_owner_id() . '/' . Text::char_at($filename_hash, 0);
                $full_folder_path = Path::getInstance()->getRepositoryPath() . $relative_folder_path;
                
                Filesystem::create_dir($full_folder_path);
                $unique_hash = Filesystem::create_unique_name($full_folder_path, $filename_hash);
                
                $relative_path = $relative_folder_path . '/' . $unique_hash;
                $path_to_save = $full_folder_path . '/' . $unique_hash;
                
                $save_success = false;
                if (StringUtilities::getInstance()->hasValue($this->temporary_file_path))
                {
                    if (Filesystem::move_file($this->temporary_file_path, $path_to_save, ! $as_new_version))
                    {
                        $save_success = true;
                    }
                    else
                    {
                        if (Filesystem::copy_file($this->temporary_file_path, $path_to_save, ! $as_new_version))
                        {
                            if (Filesystem::remove($this->temporary_file_path))
                            {
                                $save_success = true;
                            }
                        }
                    }
                }
                elseif (StringUtilities::getInstance()->hasValue($this->in_memory_file) && Filesystem::write_to_file(
                    $path_to_save, 
                    $this->in_memory_file))
                {
                    $save_success = true;
                }
                
                if ($save_success)
                {
                    Filesystem::chmod(
                        $path_to_save, 
                        Configuration::getInstance()->get_setting(array('Chamilo\Core\Admin', 'permissions_new_files')));
                    
                    $file_bytes = Filesystem::get_disk_space($path_to_save);
                    
                    $this->set_filesize($file_bytes);
                    $this->set_storage_path(Path::getInstance()->getRepositoryPath());
                    $this->set_path($relative_path);
                    $this->set_hash($unique_hash);
                    $this->set_content_hash(md5_file($path_to_save));
                }
                else
                {
                    $this->add_error(Translation::get('FileStoreError'));
                }
            }
            else
            {
                $this->add_error(Translation::get('FileFilenameNotSet'));
            }
        }
        
        return $save_success;
    }

    /**
     * Copy the current file to a new unique filename.
     * Set the new values of path and hash of the current object. Useful
     * when a File is updated as a new version, without replacing the content Note: needed as when saving a new version
     * of a File, a new record is saved in the repository_document table, and the 'hash' field must be unique.
     * 
     * @return boolean
     */
    private function duplicate_current_file()
    {
        $full_current_file_path = $this->get_full_path();
        
        if (file_exists($full_current_file_path))
        {
            $filename_hash = md5($this->get_filename());
            $relative_folder_path = $this->get_owner_id() . '/' . Text::char_at($filename_hash, 0);
            $full_folder_path = Path::getInstance()->getRepositoryPath() . $relative_folder_path;
            
            $unique_filename_hash = Filesystem::create_unique_name($full_folder_path, $filename_hash);
            
            $path_to_copied_file = $full_folder_path . '/' . $unique_filename_hash;
            
            $this->set_storage_path(Path::getInstance()->getRepositoryPath());
            $this->set_path($relative_folder_path . '/' . $unique_filename_hash);
            $this->set_hash($unique_filename_hash);
            
            return copy($full_current_file_path, $path_to_copied_file);
        }
        else
        {
            return false;
        }
    }

    public function send_as_download()
    {
        $filename = str_replace(' ', '_', $this->get_filename());
        
        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Cache-Control: public');
        header('Pragma: no-cache');
        header('Content-type: text/calendar');
        header('Content-length: ' . $this->get_filesize());
        if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
        {
            header('Content-Disposition: filename= "' . $filename . '"');
        }
        else
        {
            header('Content-Disposition: attachment; filename= "' . $filename . '"');
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
        {
            header('Pragma: ');
            header('Cache-Control: ');
            header('Cache-Control: public'); // IE cannot download from sessions
                                                 // without a cache
        }
        header('Content-Description: ' . $filename);
        header('Content-transfer-encoding: binary');
        $fp = fopen($this->get_full_path(), 'r');
        fpassthru($fp);
        return true;
    }

    public function open_in_browser()
    {
        $filename = str_replace(' ', '_', $this->get_filename());
        
        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Content-type: text/calendar');
        header('Content-length: ' . $this->get_filesize());
        header('Content-Description: ' . $filename);
        header('Content-Disposition: inline; filename= "' . $filename . '"');
        $fp = fopen($this->get_full_path(), 'r');
        fpassthru($fp);
        return true;
    }
}
