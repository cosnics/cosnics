<?php
namespace Chamilo\Core\Repository\ContentObject\File\Storage\DataClass;

use Chamilo\Core\Repository\Architecture\DownloadSupport;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataManager;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Interfaces\FileStorageSupportInterface;
use Chamilo\Libraries\Architecture\Interfaces\IncludeableInterface;
use Chamilo\Libraries\Architecture\Interfaces\VersionableInterface;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\File\ConfigurablePathBuilder;
use Chamilo\Libraries\File\FileType;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Storage\DataClass\Interfaces\DataClassExtensionInterface;
use Chamilo\Libraries\Storage\DataClass\Traits\DataClassExtensionTrait;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @package Chamilo\Core\Repository\ContentObject\File\Storage\DataClass
 */
class File extends ContentObject
    implements VersionableInterface, IncludeableInterface, FileStorageSupportInterface, DownloadSupport,
    DataClassExtensionInterface
{
    use DataClassExtensionTrait;

    public const CONTEXT = 'Chamilo\Core\Repository\ContentObject\File';

    public const PROPERTY_EXTENSION = 'extension';
    public const PROPERTY_FILENAME = 'filename';
    public const PROPERTY_FILESIZE = 'filesize';
    public const PROPERTY_HASH = 'hash';
    public const PROPERTY_PATH = 'path';
    public const PROPERTY_SHOW_INLINE = 'show_inline';
    public const PROPERTY_STORAGE_PATH = 'storage_path';

    public const TYPE_APPLICATION = 11;
    public const TYPE_ARCHIVE = 10;
    public const TYPE_AUDIO = 1;
    public const TYPE_CODE = 13;
    public const TYPE_DATABASE = 8;
    public const TYPE_FLASH = 12;
    public const TYPE_IMAGE = 3;
    public const TYPE_PDF = 4;
    public const TYPE_PRESENTATION = 7;
    public const TYPE_SPREADSHEET = 5;
    public const TYPE_TEXT = 6;
    public const TYPE_VIDEO = 2;
    public const TYPE_WEB = 9;

    private $contents;

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
        $stringUtilities = $this->getStringUtilities();

        // Title
        if ($stringUtilities->isNullOrEmpty($this->get_title()))
        {
            $this->addError(Translation::get('FileTitleIsRequired'));
        }

        $descriptionRequired = $this->getConfigurationConsulter()->getSetting(
            [Manager::CONTEXT, 'description_required']
        );

        // Description
        if ($descriptionRequired && $stringUtilities->isNullOrEmpty($this->get_description()))
        {
            $this->addError(Translation::get('FileDescriptionIsRequired'));
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
                    $this->addError(Translation::get('FileDuplicateError'));
                }
            }

            $fullpath = $this->get_full_path();

            if (!isset($fullpath) || !file_exists($fullpath))
            {
                $this->addError(Translation::get('FileFileContentNotSet'));
            }
        }

        // Filename
        if ($stringUtilities->isNullOrEmpty($this->get_filename()))
        {
            $this->addError(Translation::get('FileFilenameIsRequired'));
        }

        // Path
        if ($stringUtilities->isNullOrEmpty($this->get_path()))
        {
            $this->addError(Translation::get('FilePathToFileNotSet'));
        }

        // Hash
        if ($stringUtilities->isNullOrEmpty($this->get_hash()))
        {
            $this->addError(Translation::get('FileHashNotSet'));
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
            if (DataManager::is_only_file_occurence($this->get_storage_path(), $this->get_path()))
            {
                $filesystem->remove($this->get_full_path());
            }
        }
        elseif ($this->getStringUtilities()->isValidPath($this->get_full_path()))
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
        elseif ($this->is_code())
        {
            return 'code';
        }
        else
        {
            return 'default';
        }
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
            /**
             * @var \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
             */
            $configurablePathBuilder = DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
                ConfigurablePathBuilder::class
            );

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
            self::PROPERTY_STORAGE_PATH,
            self::PROPERTY_SHOW_INLINE
        ];
    }

    /**
     * @param int $size
     * @param bool $isAvailable
     * @param string[] $extraClasses
     *
     * @return \Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph
     */
    public function getGlyph($size = IdentGlyph::SIZE_SMALL, $isAvailable = true, $extraClasses = [])
    {
        $glyph = FileType::getGlyphForExtension($this->get_extension(), $size);

        if (!$isAvailable)
        {
            $extraClasses[] = 'fas-ci-disabled';
        }

        $glyph->setExtraClasses($extraClasses);
        $glyph->setTitle($this->get_extension());

        return $glyph;
    }

    /**
     * Returns whether or not the file must be shown inline
     *
     * @return bool
     */
    public function getShowInline()
    {
        return $this->getAdditionalProperty(self::PROPERTY_SHOW_INLINE);
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
        return 'repository_file';
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

    public static function get_code_types()
    {
        return FileType::get_type_extensions(FileType::TYPE_CODE);
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
     * Will be saved on disk if it doesn't exist yet. Mainly used to create a new File.
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
        return [self::PROPERTY_FILENAME];
    }

    public static function get_showable_types()
    {
        $showable_types = [];
        $showable_types[] = 'html';
        $showable_types[] = 'htm';
        $showable_types[] = 'txt';
        $showable_types[] = 'pdf';
        $showable_types[] = 'java';

        // $showable_types[] = 'pps';
        // $showable_types[] = 'ppt';
        // $showable_types[] = 'doc';
        // $showable_types[] = 'xls';
        // $showable_types[] = 'ppsx';
        // $showable_types[] = 'pptx';
        // $showable_types[] = 'docx';
        // $showable_types[] = 'xlsx';
        // $showable_types[] = 'mht';

        return $showable_types;
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

    public function get_type_string(): string
    {
        return Translation::get('TypeFile', ['EXTENSION' => strtoupper($this->get_extension())]);
    }

    public function get_url()
    {
        /**
         * @var \Chamilo\Libraries\File\ConfigurablePathBuilder $configurablePathBuilder
         */
        $configurablePathBuilder =
            DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(ConfigurablePathBuilder::class);

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

    public function is_code()
    {
        return FileType::is_code($this->get_extension());
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
        $fileName = str_replace(' ', '_', $this->get_filename());

        $file = $this->get_full_path();
        $response = new StreamedResponse();
        $response->headers->add(
            ['Content-Type' => $this->get_mime_type(), 'Content-Length' => $this->get_filesize()]
        );

        $safeFileName = StringUtilities::getInstance()->createString($fileName)->toAscii()->replace('/', '-')->replace(
            '\\', '-'
        )->replace('%', '_')->__toString();

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE, $safeFileName
        );

        $response->headers->set('Content-Disposition', $dispositionHeader);
        $response->setCallback(
            function () use ($file) {
                readfile($file);
            }
        );

        $response->send();
        exit();
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
                        $this->addError(
                            'File move failed. From: ' . $this->temporary_file_path . ' to ' . $path_to_save
                        );
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
        $fileName = str_replace(' ', '_', $this->get_filename());

        $file = $this->get_full_path();
        $response = new StreamedResponse();
        $response->headers->add(
            ['Content-Type' => $this->get_mime_type(), 'Content-Length' => $this->get_filesize()]
        );

        $safeFileName = StringUtilities::getInstance()->createString($fileName)->toAscii()->replace('/', '-')->replace(
            '\\', '-'
        )->replace('%', '_')->__toString();

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, $safeFileName
        );

        $response->headers->set('Content-Disposition', $dispositionHeader);

        $response->setCallback(
            function () use ($file) {
                readfile($file);
            }
        );

        $response->send();
        exit();
    }

    /**
     * Sets whether or not the file must be shown inline
     *
     * @param bool $showInline
     */
    public function setShowInline($showInline)
    {
        return $this->setAdditionalProperty(self::PROPERTY_SHOW_INLINE, $showInline);
    }

    /**
     * Active record functions
     */

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

    /**
     * Set In memory file content.
     * Will be saved on disk if it doesn't exist yet. Mainly used to create a new File.
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
                throw new Exception('A File can not have a temporary file path and in memory content');
            }

            $this->in_memory_file = $in_memory_file;
        }
    }

    public function set_path($path)
    {
        return $this->setAdditionalProperty(self::PROPERTY_PATH, $path);
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

        return parent::update($trueUpdate);
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
