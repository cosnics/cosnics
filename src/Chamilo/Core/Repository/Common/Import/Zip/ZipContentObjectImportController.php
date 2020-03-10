<?php
namespace Chamilo\Core\Repository\Common\Import\Zip;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Translation\Translation;
use ZipArchive;

class ZipContentObjectImportController extends ContentObjectImportController
{
    const FORMAT = 'zip';

    private $created_categories;

    private $created_content_object_ids;

    /**
     * Constrcuts this import controller
     * 
     * @param ImportParameters $parameters
     */
    public function __construct($parameters)
    {
        parent::__construct($parameters);
        $this->created_categories = array();
        $this->created_content_object_ids = array();
    }

    public function tryout($file)
    {
        $extracted_files_dir = $path = Path::getInstance()->getTemporaryPath() . uniqid();
        Filesystem::create_dir($path);
        
        $zip = new ZipArchive();
        $zip->open($file->get_path());
        
        for ($i = 0; $i < $zip->numFiles; $i ++)
        {
            $stat_index = $zip->statIndex($i);
            $name = $stat_index['name'];
            
            $utf8 = mb_check_encoding($name, 'UTF-8');
            if (! $utf8)
            {
                $detect_encoding = mb_detect_encoding($name);
                $detect_encoding = $detect_encoding ? $detect_encoding : 'CP437';
                $name = iconv($detect_encoding, 'UTF-8', $name);
            }
        }
        $zip->extractTo($extracted_files_dir);
    }

    /**
     * Runs this import controller
     * 
     * @return array bool
     */
    public function run()
    {
        $file = $this->get_parameters()->get_file();
        
        if (self::is_available())
        {
            if (in_array($file->get_extension(), self::get_allowed_extensions()))
            {
                $zip_archive = $this->open_zip($file->get_path());
                $files_info = $this->get_files_info($zip_archive);
                
                if (! $this->calculate_user_quota($files_info))
                {
                    $this->add_message(Translation::get('InsufficientDiskQuota'), self::TYPE_ERROR);
                    return array();
                }
                
                if (count($files_info) == 0)
                {
                    $this->add_message(Translation::get('EmptyZip'), self::TYPE_WARNING);
                }
                else
                {
                    $category = $this->get_selected_category();
                    
                    if ($category instanceof RepositoryCategory)
                    {
                        $failures = $this->handle_files($zip_archive, $files_info);
                        
                        if ($failures == 0)
                        {
                            $this->add_message(Translation::get('ObjectImported'), self::TYPE_CONFIRM);
                            return $this->created_content_object_ids;
                        }
                        else
                        {
                            $this->add_message(Translation::get('ObjectNotImported'), self::TYPE_ERROR);
                        }
                    }
                    else
                    {
                        $this->add_message(
                            Translation::get('NotImportedCategoryWithSameNameExists'), 
                            self::TYPE_ERROR);
                    }
                }
            }
            else
            {
                $this->add_message(
                    Translation::get(
                        'UnsupportedFileFormat', 
                        array('TYPES' => implode(', ', self::get_allowed_extensions()))), 
                    self::TYPE_ERROR);
            }
        }
        else
        {
            $this->add_message(Translation::get('DocumentObjectNotAvailable'), self::TYPE_WARNING);
        }
    }

    /**
     * Opens the zip file for further processing
     * 
     * @param string $zip_file_path
     *
     * @return \ZipArchive
     */
    protected function open_zip($zip_file_path)
    {
        $zip_archive = new ZipArchive();
        $zip_archive->open($zip_file_path);
        
        return $zip_archive;
    }

    /**
     * Retrieves the file information metadata from the zip archive
     * 
     * @param \ZipArchive $zip_archive
     *
     * @return array
     */
    protected function get_files_info(ZipArchive $zip_archive)
    {
        $files_info = array();
        
        for ($i = 0; $i < $zip_archive->numFiles; $i ++)
        {
            $file_info = $zip_archive->statIndex($i);
            
            if (strpos($file_info['name'], '.') === false || strpos($file_info['name'], '__MACOSX') !== false)
            {
                continue;
            }
            
            $files_info[] = $file_info;
        }
        
        usort($files_info, function ($a, $b)
        {
            return strcmp($a['name'], $b['name']);
        });
        
        return $files_info;
    }

    /**
     * Calculates if the import size of the files is not bigger than the quota of the user
     * 
     * @param array $files_info
     *
     * @return bool
     */
    protected function calculate_user_quota(array $files_info)
    {
        $total_file_size = 0;
        
        foreach ($files_info as $file_info)
        {
            $total_file_size += $file_info['size'];
        }
        
        $calculator = new Calculator(
            \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(
                User::class_name(),
                (int) $this->get_parameters()->get_user()));
        
        if (! $calculator->canUpload($total_file_size))
        {
            return false;
        }
        
        return true;
    }

    /**
     * Retrieves the selected category from the database or mimics the root category
     * 
     * @return \libraries\storage\DataClass RepositoryCategory
     */
    protected function get_selected_category()
    {
        if ($this->get_parameters()->get_category() == 0)
        {
            $category = new RepositoryCategory();
            $category->set_id(0);
            $category->set_type_id($this->get_parameters()->getWorkspace()->getId());
            $category->set_type($this->get_parameters()->getWorkspace()->getWorkspaceType());
        }
        else
        {
            $category = DataManager::retrieve_by_id(
                RepositoryCategory::class_name(), 
                $this->get_parameters()->get_category());
        }
        
        $this->created_categories[0] = $category;
        
        return $category;
    }

    /**
     * Handles the files in the zip archive and returns the amount of failures
     * 
     * @param \ZipArchive $zip_archive
     * @param array $files_info
     *
     * @return int
     */
    protected function handle_files(ZipArchive $zip_archive, array $files_info)
    {
        $extracted_files_dir = Path::getInstance()->getTemporaryPath() . uniqid();
        Filesystem::create_dir($extracted_files_dir);
        
        $failures = 0;
        
        foreach ($files_info as $file_info)
        {
            $relative_filename = $this->cleanup_filename($file_info['name']);
            
            $filename_parts = explode(DIRECTORY_SEPARATOR, $relative_filename);
            $filename = array_pop($filename_parts);
            $path = implode(DIRECTORY_SEPARATOR, $filename_parts);
            
            $category = $this->get_category_by_path($path);
            
            if ($category)
            {
                $zip_archive->extractTo($extracted_files_dir, $file_info['name']);
                
                $files = Filesystem::get_directory_content($extracted_files_dir, Filesystem::LIST_FILES);
                if (count($files) == 0)
                {
                    $failures ++;
                    continue;
                }
                
                $first_file = $files[0];
                if (empty($first_file))
                {
                    $failures ++;
                    continue;
                }
                
                if (! $this->create_content_object($filename, $first_file, $category->get_id()))
                {
                    $failures ++;
                }
                
                Filesystem::remove($first_file);
            }
            else
            {
                $failures ++;
            }
        }
        
        Filesystem::remove($extracted_files_dir);
        
        return $failures;
    }

    /**
     * Cleans up the encoding of the filename
     * 
     * @param string $filename
     *
     * @return string
     */
    protected function cleanup_filename($filename)
    {
        $utf8 = mb_check_encoding($filename, 'UTF-8');
        
        if (! $utf8)
        {
            $detect_encoding = mb_detect_encoding($filename);
            $detect_encoding = $detect_encoding ? $detect_encoding : 'CP437';
            $filename = iconv($detect_encoding, 'UTF-8', $filename);
        }
        
        // convert directory separators to current OS style
        $filename = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $filename);
        
        return $filename;
    }

    /**
     * Creates a content object with a filename, a path to the file and a parent category id
     * 
     * @param string $filename
     * @param string $path
     * @param int $parent
     *
     * @return bool
     */
    protected function create_content_object($filename, $path, $parent)
    {
        $document = new File();
        
        $document->set_temporary_file_path($path);
        $document->set_title($filename);
        
        $document->set_description($filename);
        $document->set_owner_id($this->get_parameters()->get_user());
        $document->set_parent_id($this->determine_parent_id($parent));
        $document->set_filename($filename);
        
        if ($document->create())
        {
            $this->process_workspace($parent, $document);
            $this->created_content_object_ids[] = $document->get_id();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Creates or retrieves the current category by a given path name, works recursively to create nested paths
     * 
     * @param string $path
     *
     * @return bool
     */
    protected function get_category_by_path($path)
    {
        if (empty($path) || $path == '.')
        {
            return $this->created_categories[0];
        }
        
        if ($this->created_categories[$path])
        {
            return $this->created_categories[$path];
        }
        
        // prepare variables
        $filename_parts = explode(DIRECTORY_SEPARATOR, $path);
        $base_name = array_pop($filename_parts);
        $dir_name = implode(DIRECTORY_SEPARATOR, $filename_parts);
        
        $parent_category = $this->get_category_by_path($dir_name);
        if (! $parent_category)
        {
            return null;
        }
        
        $category = new RepositoryCategory();
        
        $category->set_name(
            DataManager::create_unique_category_name(
                $this->get_parameters()->getWorkspace(), 
                $parent_category->get_id(), 
                $base_name));
        
        $category->set_parent($parent_category->get_id());
        $category->set_type_id($this->get_parameters()->getWorkspace()->getId());
        $category->set_type($this->get_parameters()->getWorkspace()->getWorkspaceType());
        
        if ($category->create())
        {
            return $this->created_categories[$path] = $category;
        }
    }

    /**
     * Returns the allowed extensions
     * 
     * @return array
     */
    public static function get_allowed_extensions()
    {
        return array('zip');
    }

    public static function is_available()
    {
        return in_array(
            'Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File', 
            DataManager::get_registered_types(true));
    }

    /**
     *
     * @return integer
     */
    public function determine_parent_id($parent)
    {
        if ($this->get_parameters()->getWorkspace() instanceof PersonalWorkspace)
        {
            return $parent;
        }
        else
        {
            return 0;
        }
    }

    /**
     *
     * @param ContentObject $contentObject
     */
    public function process_workspace($parent, ContentObject $contentObject)
    {
        if ($this->get_parameters()->getWorkspace() instanceof Workspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelationService->createContentObjectRelation(
                $this->get_parameters()->getWorkspace()->getId(), 
                $contentObject->getId(), 
                $parent);
        }
    }
}
