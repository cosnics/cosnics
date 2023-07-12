<?php
namespace Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass;

use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataManager;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\FileStorageSupport;
use Chamilo\Libraries\Architecture\Interfaces\Includeable;
use Chamilo\Libraries\Architecture\Interfaces\Versionable;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FileType;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\String\Text;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * @package Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass
 */
class Webpage extends ContentObject implements Versionable, Includeable, FileStorageSupport
{
    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\Webpage';

    public const PROPERTY_EXTENSION = 'extension';
    public const PROPERTY_FILENAME = 'filename';
    public const PROPERTY_FILESIZE = 'filesize';
    public const PROPERTY_HASH = 'hash';
    public const PROPERTY_PATH = 'path';
    public const PROPERTY_STORAGE_PATH = 'storage_path';

    public const TYPE_AUDIO = 'audio';
    public const TYPE_FLASH = 'flash';
    public const TYPE_FLASH_VIDEO = 'flash_video';
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';

    private $contents;

    /**
     * In memory file content.
     * Will be saved on disk if it doesn't exist yet. Mainly used to create a new Webpage.
     *
     * @var mixed
     */
    private $in_memory_file;

    /**
     * Indicates wether the Webpage must be saved as a new version when its save() or update() method is called
     *
     * @var bool
     */
    private $save_as_new_version = false;

    /**
     * Temporary file path.
     * A path to a file that has to be moved and renamed when the Webpage is saved. Useful for
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
        $stringUtilities = $this->getStringUtilities();

        // Title
        if ($stringUtilities->isNullOrEmpty($this->get_title()))
        {
            $this->addError(Translation::get('WebpageTitleIsRequired'));
        }

        $descriptionRequired = $this->getConfigurationConsulter()->getSetting(
            [Manager::CONTEXT, 'description_required']
        );

        // Description
        if ($descriptionRequired && $stringUtilities->isNullOrEmpty($this->get_description()))
        {
            $this->addError(Translation::get('WebpageDescriptionIsRequired'));
        }

        // OwnerId
        $owner_id = $this->get_owner_id();
        if (!isset($owner_id) || !is_numeric($owner_id))
        {
            $this->addError(Translation::get('ContentObjectOwnerIsRequired'));
        }

        /*
         * Save file if needed
         */
        if ($this->has_file_to_save())
        {
            $this->save_file();
        }
        else
        {

            /*
             * Make a copy of the current file if the update has to create a new version, without saving a new content
             */
            if ($this->save_as_new_version && !$this->has_file_to_save())
            {
                if (!$this->duplicate_current_file())
                {
                    $this->addError(Translation::get('WebpageDuplicateError'));
                }
            }

            $fullpath = $this->get_full_path();

            if (!isset($fullpath) || !file_exists($fullpath))
            {
                $this->addError(Translation::get('WebpageFileContentNotSet'));
            }
        }

        // Filename
        if ($stringUtilities->isNullOrEmpty($this->get_filename()))
        {
            $this->addError(Translation::get('WebpageFilenameIsRequired'));
        }

        // Path
        if ($stringUtilities->isNullOrEmpty($this->get_path()))
        {
            $this->addError(Translation::get('WebpagePathToFileNotSet'));
        }

        // Hash
        if ($stringUtilities->isNullOrEmpty($this->get_hash()))
        {
            $this->addError(Translation::get('WebpageHashNotSet'));
        }

        return !$this->hasErrors();
    }

    /**
     * (non-PHPdoc)
     *
     * @see repository/lib/ContentObject#create()
     */
    public function create(): bool
    {
        $this->clearErrors();

        return parent::create();
    }

    public function delete($only_version = false): bool
    {
        $filesystem = $this->getFilesystem();

        if ($only_version)
        {
            if (DataManager::is_only_webpage_occurence($this->get_storage_path(), $this->get_path()))
            {
                $filesystem->remove($this->get_full_path());
            }
        }
        elseif (Text::is_valid_path($this->get_full_path()))
        {
            $filesystem->remove($this->get_full_path());
        }

        return parent::delete($only_version);
    }

    public function determine_type()
    {
        if ($this->is_audio())
        {
            return 'audio';
        }
        elseif ($this->is_flash())
        {
            return 'flash';
        }
        elseif ($this->is_image())
        {
            return 'image';
        }
        elseif ($this->is_video())
        {
            return 'video';
        }
        else
        {
            return 'default';
        }
    }

    /**
     * Copy the current file to a new unique filename.
     * Set the new values of path and hash of the current object. Useful
     * when a Webpage is updated as a new version, without replacing the content Note: needed as when saving a new
     * version of a Webpage, a new record is saved in the repository_document table, and the 'hash' field must be
     * unique.
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
            $relative_folder_path = $this->get_owner_id() . '/' . Text::char_at($filename_hash, 0);
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
            self::PROPERTY_STORAGE_PATH
        ];
    }

    /**
     * @return string
     */
    public static function getStorageSpaceProperty()
    {
        return self::PROPERTY_FILESIZE;
    }

    /**
     * @return string
     */
    public static function getStorageUnitName(): string
    {
        return 'repository_webpage';
    }

    /**
     * Get extensions for audio
     *
     * @return string[]
     * @deprecated Use FileType::get_type_extensions(FileType::TYPE_AUDIO) now
     */
    public static function get_audio_types()
    {
        return FileType::get_type_extensions(FileType::TYPE_AUDIO);
    }

    /**
     * @return string
     */
    public static function get_disk_space_properties()
    {
        return static::getStorageSpaceProperty();
    }

    public function get_extension()
    {
        $filename = $this->get_filename();
        $parts = explode('.', $filename);

        return strtolower($parts[count($parts) - 1]);
    }

    public function get_filename()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FILENAME);
    }

    public function get_filesize()
    {
        return $this->getAdditionalProperty(self::PROPERTY_FILESIZE);
    }

    /**
     * Get extensions for flash
     *
     * @return string[]
     * @deprecated Use FileType::get_type_extensions(FileType::TYPE_FLASH) now
     */
    public static function get_flash_types()
    {
        return FileType::get_type_extensions(FileType::TYPE_FLASH);
    }

    public static function get_flash_video_types()
    {
        $flash_types = [];
        $flash_types[] = 'flv';

        return $flash_types;
    }

    public function get_full_path()
    {
        return $this->get_storage_path() . $this->get_path();
    }

    public function get_hash()
    {
        return $this->getAdditionalProperty(self::PROPERTY_HASH);
    }

    /**
     * Get extensions for images
     *
     * @return string[]
     * @deprecated Use FileType::get_type_extensions(FileType::TYPE_IMAGE) now
     */
    public static function get_image_types()
    {
        return FileType::get_type_extensions(FileType::TYPE_IMAGE);
    }

    /**
     * Get In memory file content.
     * Will be saved on disk if it doesn't exist yet. Mainly used to create a new Webpage.
     *
     * @return mixed
     */
    public function get_in_memory_file()
    {
        return $this->in_memory_file;
    }

    public function get_mime_type()
    {
        return FileType::get_mimetype($this->get_extension());
    }

    public function get_path()
    {
        return $this->getAdditionalProperty(self::PROPERTY_PATH);
    }

    /**
     * Get a value indicating wether the Webpage must be saved as a new version if its save() or update() method is
     * called
     *
     * @return bool
     */
    public function get_save_as_new_version()
    {
        return $this->save_as_new_version;
    }

    public static function get_searchable_property_names()
    {
        return [self::PROPERTY_FILENAME];
    }

    public static function get_showable_types()
    {
        $showable_types = [];
        $showable_types[] = 'html';
        $showable_types[] = 'htm';
        $showable_types[] = 'txt';
        $showable_types[] = 'pdf';

        return $showable_types;
    }

    public function get_storage_path()
    {
        return $this->getAdditionalProperty(self::PROPERTY_STORAGE_PATH);
    }

    /**
     * Get temporary file path.
     * A path to a file that has to be moved and renamed when the Webpage is saved
     *
     * @return string
     */
    public function get_temporary_file_path()
    {
        return $this->temporary_file_path;
    }

    public function get_type_string(): string
    {
        return Translation::get('TypeWebpage', ['EXTENSION' => strtoupper($this->get_extension())]);
    }

    public function get_url()
    {
        /**
         * @var \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
         */
        $configurablePathBuilder = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            ConfigurablePathBuilder::class
        );

        return $configurablePathBuilder->getRepositoryPath() . $this->get_path();
    }

    /**
     * Get extensions for video
     *
     * @return string[]
     * @deprecated Use FileType::get_type_extensions(FileType::TYPE_VIDEO) now
     */
    public static function get_video_types()
    {
        return FileType::get_type_extensions(FileType::TYPE_VIDEO);
    }

    public function has_file_to_save()
    {
        return StringUtilities::getInstance()->hasValue($this->get_temporary_file_path()) ||
            StringUtilities::getInstance()->hasValue(
                $this->get_in_memory_file()
            );
    }

    /**
     * Determines if this document is an audio file
     *
     * @return bool True if the document is an audio file
     */
    public function is_audio()
    {
        return FileType::is_audio($this->get_extension());
    }

    /**
     * Determines if this document is a flash movie
     *
     * @return bool True if the document is a flash movie
     */
    public function is_flash()
    {
        return FileType::is_flash($this->get_extension());
    }

    /**
     * Determines if this document is an image
     *
     * @return bool True if the document is an image
     */
    public function is_image()
    {
        return FileType::is_image($this->get_extension());
    }

    /**
     * Determines if this document is a video
     *
     * @return bool True if the document is a video
     */
    public function is_video()
    {
        return FileType::is_video($this->get_extension());
    }

    public function open_in_browser()
    {
        $filename = str_replace(' ', '_', $this->get_filename());

        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Content-type: ' . $this->get_mime_type());
        header('Content-length: ' . $this->get_filesize());
        header('Content-Description: ' . $filename);
        header('Content-Disposition: inline; filename= "' . $filename . '"');
        $fp = fopen($this->get_full_path(), 'r');
        fpassthru($fp);

        return true;
    }

    /**
     * e.g: documents: in the html file
     */
    public function process_additional_include_links($pattern, $replacement_string)
    {
        $this->contents = preg_replace($pattern, $replacement_string, $this->contents);
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
                $stringUtilties = $this->getStringUtilities();

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
                $relative_folder_path = $this->get_owner_id() . '/' . Text::char_at($filename_hash, 0);
                $full_folder_path = $configurablePathBuilder->getRepositoryPath() . $relative_folder_path;

                $filesystem->mkdir($full_folder_path);
                $unique_hash = $this->getFilesystemTools()->createUniqueName($full_folder_path, $filename_hash);

                $relative_path = $relative_folder_path . '/' . $unique_hash;
                $path_to_save = $full_folder_path . '/' . $unique_hash;

                if ($stringUtilties->hasValue($this->temporary_file_path))
                {
                    try
                    {
                        $filesystem->rename($this->temporary_file_path, $path_to_save, !$as_new_version);
                        $save_success = true;
                    }
                    catch (Exception)
                    {
                        $save_success = false;
                    }
                }
                elseif ($stringUtilties->hasValue($this->in_memory_file))
                {
                    try
                    {
                        $filesystem->dumpFile($path_to_save, $this->in_memory_file);
                        $save_success = true;
                    }
                    catch (Exception)
                    {
                        $save_success = false;
                    }
                }
                else
                {
                    $save_success = false;
                }

                if ($save_success)
                {
                    $filesystem->chmod(
                        $path_to_save, (int) $this->getConfigurationConsulter()->getSetting(
                        ['Chamilo\Core\Admin', 'permissions_new_files']
                    )
                    );

                    $file_bytes = $this->getFilesystemTools()->getDiskSpace($path_to_save);

                    $this->set_filesize($file_bytes);
                    $this->set_storage_path($configurablePathBuilder->getRepositoryPath());
                    $this->set_path($relative_path);
                    $this->set_hash($unique_hash);
                    $this->set_content_hash(md5_file($path_to_save));
                }
                else
                {
                    $this->addError(Translation::get('WebpageStoreError'));
                }
            }
            else
            {
                $save_success = false;
                $this->addError(Translation::get('WebpageFilenameNotSet'));
            }
        }
        else
        {
            $save_success = false;
        }

        return $save_success;
    }

    public function send_as_download()
    {
        $filename = str_replace(' ', '_', $this->get_filename());

        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Cache-Control: public');
        header('Pragma: no-cache');
        header('Content-type: ' . $this->get_mime_type());
        // header('Content-Type: application/force-download');
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

    /**
     * Active record functions
     */

    public function set_hash($hash)
    {
        return $this->setAdditionalProperty(self::PROPERTY_HASH, $hash);
    }

    /**
     * Set In memory file content.
     * Will be saved on disk if it doesn't exist yet. Mainly used to create a new Webpage.
     *
     * @return void
     * @var $in_memory_file mixed
     */
    public function set_in_memory_file($in_memory_file)
    {
        if (StringUtilities::getInstance()->hasValue($in_memory_file))
        {
            if (StringUtilities::getInstance()->hasValue($this->get_temporary_file_path()))
            {
                throw new Exception('A Webpage can not have a temporary file path and in memory content');
            }

            $this->in_memory_file = $in_memory_file;
        }
    }

    public function set_path($path)
    {
        return $this->setAdditionalProperty(self::PROPERTY_PATH, $path);
    }

    /**
     * Set a value indicating wether the Webpage must be saved as a new version if its save() or update() method is
     * called
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
     * A path to a file that has to be moved and renamed when the Webpage is saved
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
                throw new Exception('A Webpage can not have a temporary file path and in memory content');
            }

            $this->temporary_file_path = $temporary_file_path;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see repository/lib/ContentObject#update($trueUpdate)
     */
    public function update($trueUpdate = true): bool
    {
        /*
         * Force using version() instead of update() if the object is marked to be saved as a new version
         */
        if ($this->save_as_new_version)
        {
            return $this->version();
        }

        $this->clearErrors();

        if ($this->checkBeforeSave()) // may be called twice in some situation
            // (if the calling method is 'save()
            // from the DataClass), but the create()
            // method in the content_object class
            // doesn't call it
        {
            return parent::update($trueUpdate);
        }
        else
        {
            return false;
        }
    }

    public function update_include_links(array $mapping)
    {
        if ($this->get_extension() == 'html')
        {
            // open file
            $handle = fopen($this->get_full_path(), 'r+');

            // read contents
            $this->contents = fread($handle, filesize($this->get_full_path()));

            // process changes
            parent::update_include_links($mapping);

            // write file
            rewind($handle);
            fwrite($handle, $this->contents);

            // close file
            fclose($handle);
        }
    }

    public function version($trueUpdate = true)
    {
        $this->clearErrors();

        return parent::version($trueUpdate);
    }
}
