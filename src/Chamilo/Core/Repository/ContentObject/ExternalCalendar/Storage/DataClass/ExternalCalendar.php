<?php
namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Service\ExternalCalendarCacheService;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\FileStorageSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use DateTime;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Sabre\VObject\InvalidDataException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ExternalCalendar extends ContentObject implements VersionableInterface, FileStorageSupportInterface
{
    public const CACHE_TIME = 3600;

    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\ExternalCalendar';

    public const PARAM_EVENT_ID = 'event_id';

    public const PATH_TYPE_LOCAL = 1;
    public const PATH_TYPE_REMOTE = 2;

    public const PROPERTY_FILENAME = 'filename';
    public const PROPERTY_FILESIZE = 'filesize';
    public const PROPERTY_HASH = 'hash';
    public const PROPERTY_PATH = 'path';
    public const PROPERTY_PATH_TYPE = 'path_type';
    public const PROPERTY_STORAGE_PATH = 'storage_path';

    public const REPEAT_END = 'end';
    public const REPEAT_START = 'start';
    public const REPEAT_TYPE_DAY = 'DAILY';
    public const REPEAT_TYPE_MONTH = 'MONTHLY';
    public const REPEAT_TYPE_NONE = 'NONE';
    public const REPEAT_TYPE_WEEK = 'WEEKLY';
    public const REPEAT_TYPE_YEAR = 'YEARLY';

    /**
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
     * Indicates wether the File must be saved as a new version when its save() or update() method is called
     *
     * @var bool
     */
    private $save_as_new_version = false;

    /**
     * Temporary file path.
     * A path to a file that has to be moved and renamed when the File is saved. Useful for
     * instance when a file is uploaded to the server.
     *
     * @var string
     */
    private $temporary_file_path;

    /**
     * (non-PHPdoc)
     *
     * @see common/DataClass#checkBeforeSave()
     */
    protected function checkBeforeSave(): bool
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
                if ($this->save_as_new_version && !$this->has_file_to_save())
                {
                    if (!$this->duplicate_current_file())
                    {
                        $this->addError(Translation::get('FileDuplicateError'));
                    }
                }

                $fullpath = $this->get_full_path();

                if (!isset($fullpath) || !file_exists($fullpath))
                {
                    $this->addError(Translation::get('FileFileContentNotSet'));
                }
            }
        }

        return !$this->hasErrors();
    }

    public function count_events()
    {
        $events = $this->get_events();

        return count($events);
    }

    /**
     * Copy the current file to a new unique filename.
     * Set the new values of path and hash of the current object. Useful
     * when a File is updated as a new version, without replacing the content Note: needed as when saving a new version
     * of a File, a new record is saved in the repository_document table, and the 'hash' field must be unique.
     *
     * @return bool
     */
    private function duplicate_current_file()
    {
        $full_current_file_path = $this->get_full_path();

        if (file_exists($full_current_file_path))
        {
            $configurablePathBuilder = $this->getConfigurablePathBuilder();

            $filename_hash = md5($this->get_filename());
            $relative_folder_path = $this->get_owner_id() . '/' .
                $this->getStringUtilities()->createString($filename_hash)->at(0)->toString();
            $full_folder_path = $configurablePathBuilder->getRepositoryPath() . $relative_folder_path;

            $unique_filename_hash = $this->getFilesystemTools()->createUniqueName($full_folder_path, $filename_hash);

            $path_to_copied_file = $full_folder_path . '/' . $unique_filename_hash;

            $this->set_storage_path($configurablePathBuilder->getRepositoryPath());
            $this->set_path($relative_folder_path . '/' . $unique_filename_hash);
            $this->set_hash($unique_filename_hash);

            return copy($full_current_file_path, $path_to_copied_file);
        }
        else
        {
            return false;
        }
    }

    public static function getAdditionalPropertyNames(): array
    {
        return [
            self::PROPERTY_FILENAME,
            self::PROPERTY_FILESIZE,
            self::PROPERTY_PATH,
            self::PROPERTY_HASH,
            self::PROPERTY_PATH_TYPE,
            self::PROPERTY_STORAGE_PATH
        ];
    }

    protected function getFilesystemAdapter(): FilesystemAdapter
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            'Chamilo\Core\Repository\ContentObject\ExternalCalendar\OccurencesCacheAdapter'
        );
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_external_calendar';
    }

    /**
     * @return \Sabre\VObject\Component\VCalendar
     */
    public function get_calendar()
    {
        $configurablePathBuilder = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurablePathBuilder::class
        );

        $externalCalendarCacheService = new ExternalCalendarCacheService($configurablePathBuilder);

        return $externalCalendarCacheService->getCalendarForPath($this->get_full_path());
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

    public function get_events()
    {
        return $this->get_calendar()->getBaseComponents('VEvent');
    }

    public function get_filename()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FILENAME);
    }

    public function get_filesize()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FILESIZE);
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

    public function get_hash()
    {
        return $this->getAdditionalProperty(self::PROPERTY_HASH);
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

    public function get_occurences($start_timestamp, $end_timestamp)
    {
        $filesystemAdapter = $this->getFilesystemAdapter();

        try
        {
            $cacheId = md5(serialize([$this->get_path(), $start_timestamp, $end_timestamp]));

            $cacheItem = $filesystemAdapter->getItem($cacheId);

            if (!$cacheItem->isHit())
            {
                $calendar = $this->get_calendar();

                $start_date_time = new DateTime();
                $start_date_time->setTimestamp($start_timestamp);

                $end_date_time = new DateTime();
                $end_date_time->setTimestamp($end_timestamp);

                $calendar->expand($start_date_time, $end_date_time);
                $occurences = $calendar->VEVENT;

                $cacheItem->set($occurences);
                $filesystemAdapter->save($cacheItem);
            }

            return $cacheItem->get();
        }
        catch (InvalidArgumentException|InvalidDataException $e)
        {
            return [];
        }
    }

    public function get_path()
    {
        return $this->getAdditionalProperty(self::PROPERTY_PATH);
    }

    public function get_path_type()
    {
        return $this->getAdditionalProperty(self::PROPERTY_PATH_TYPE);
    }

    /**
     * Get a value indicating wether the File must be saved as a new version if its save() or update() method is called
     *
     * @return bool
     */
    public function get_save_as_new_version()
    {
        return $this->save_as_new_version;
    }

    public static function get_searchable_property_names()
    {
        return [self::PROPERTY_PATH, self::PROPERTY_FILENAME];
    }

    public function get_storage_path()
    {
        return $this->getAdditionalProperty(self::PROPERTY_STORAGE_PATH);
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

    public function has_file_to_save()
    {
        return StringUtilities::getInstance()->hasValue($this->get_temporary_file_path()) ||
            StringUtilities::getInstance()->hasValue(
                $this->get_in_memory_file()
            );
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

    /**
     * Save the in memory file or the temporary file to the current user disk space Return true if the file could be
     * saved
     *
     * @return bool
     */
    private function save_file()
    {
        if ($this->has_file_to_save())
        {
            $filename = $this->get_filename();

            if (isset($filename))
            {
                $configurablePathBuilder = $this->getConfigurablePathBuilder();
                $filesystem = $this->getFilesystem();
                $filesystemTools = $this->getFilesystemTools();
                $stringUtilities = $this->getStringUtilities();

                /*
                 * Delete current file before to create it again if the object is not saved as a new version @TODO: This
                 * should not happen when the object is newly created, only for an update
                 */
                $as_new_version = $this->get_save_as_new_version();
                if (!$as_new_version && $this->isIdentified())
                {
                    $current_path = $this->get_path();

                    if (isset($current_path) && is_file($configurablePathBuilder->getRepositoryPath() . $current_path))
                    {
                        $filesystem->remove($configurablePathBuilder->getRepositoryPath() . $current_path);
                    }
                }

                $filename_hash = md5($filename);
                $relative_folder_path = $this->get_owner_id() . '/' .
                    $this->getStringUtilities()->createString($filename_hash)->at(0)->toString();
                $full_folder_path = $configurablePathBuilder->getRepositoryPath() . $relative_folder_path;

                $filesystem->mkdir($full_folder_path);
                $unique_hash = $filesystemTools->createUniqueName($full_folder_path, $filename_hash);

                $relative_path = $relative_folder_path . '/' . $unique_hash;
                $path_to_save = $full_folder_path . '/' . $unique_hash;

                if ($stringUtilities->hasValue($this->temporary_file_path))
                {
                    try
                    {
                        $filesystem->rename($this->temporary_file_path, $path_to_save, !$as_new_version);
                        $saveSuccess = true;
                    }
                    catch (Exception)
                    {
                        $saveSuccess = false;
                    }
                }
                elseif ($stringUtilities->hasValue($this->in_memory_file))
                {
                    try
                    {
                        $filesystem->dumpFile($path_to_save, $this->in_memory_file);
                        $saveSuccess = true;
                    }
                    catch (Exception)
                    {
                        $saveSuccess = false;
                    }
                }
                else
                {
                    $saveSuccess = false;
                }

                if ($saveSuccess)
                {
                    $filesystem->chmod(
                        $path_to_save, (int) $this->getConfigurationConsulter()->getSetting(
                        ['Chamilo\Core\Admin', 'permissions_new_files']
                    )
                    );

                    $file_bytes = $filesystemTools->getDiskSpace($path_to_save);

                    $this->set_filesize($file_bytes);
                    $this->set_storage_path($configurablePathBuilder->getRepositoryPath());
                    $this->set_path($relative_path);
                    $this->set_hash($unique_hash);
                    $this->set_content_hash(md5_file($path_to_save));
                }
                else
                {
                    $this->addError(Translation::get('FileStoreError'));
                }
            }
            else
            {
                $saveSuccess = false;
                $this->addError(Translation::get('FileFilenameNotSet'));
            }
        }
        else
        {
            $saveSuccess = false;
        }

        return $saveSuccess;
    }

    public function send_as_download()
    {
        $filename = str_replace(' ', '_', $this->get_filename());

        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Cache-Control: public');
        header('Pragma: no-cache');
        header('Content-type: text/calendar');
        header('Content-length: ' . $this->get_filesize());
        if (preg_match('/MSIE 5.5/', $_SERVER['HTTP_USER_AGENT']))
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

    public function set_filename($filename)
    {
        return $this->setAdditionalProperty(self::PROPERTY_FILENAME, $filename);
    }

    public function set_filesize($filesize)
    {
        return $this->setAdditionalProperty(self::PROPERTY_FILESIZE, $filesize);
    }

    public function set_hash($hash)
    {
        return $this->setAdditionalProperty(self::PROPERTY_HASH, $hash);
    }

    public function set_path($path)
    {
        return $this->setAdditionalProperty(self::PROPERTY_PATH, $path);
    }

    public function set_path_type($path_type)
    {
        return $this->setAdditionalProperty(self::PROPERTY_PATH_TYPE, $path_type);
    }

    /**
     * Set a value indicating wether the File must be saved as a new version if its save() or update() method is called
     *
     * @return void
     * @var $save_as_new_version bool
     */
    public function set_save_as_new_version($save_as_new_version)
    {
        if (is_bool($save_as_new_version))
        {
            $this->save_as_new_version = $save_as_new_version;
        }
    }

    public function set_storage_path($storage_path)
    {
        return $this->setAdditionalProperty(self::PROPERTY_STORAGE_PATH, $storage_path);
    }

    /**
     * Set temporary file path.
     * A path to a file that has to be moved and renamed when the File is saved
     *
     * @return void
     * @var $temporary_file_path string
     */
    public function set_temporary_file_path($temporary_file_path)
    {
        if (StringUtilities::getInstance()->hasValue($temporary_file_path))
        {
            if (StringUtilities::getInstance()->hasValue($this->get_in_memory_file()))
            {
                throw new Exception('A File can not have a temporary file path and in memory content');
            }

            $this->temporary_file_path = $temporary_file_path;
        }
    }
}
