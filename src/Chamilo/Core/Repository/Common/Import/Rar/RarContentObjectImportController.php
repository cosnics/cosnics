<?php
namespace Chamilo\Core\Repository\Common\Import\Rar;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Quota\Calculator;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Translation;

class RarContentObjectImportController extends ContentObjectImportController
{
    const FORMAT = 'rar';

    /**
     *
     * @var multitype:RepositoryCategory
     */
    private $created_categories;

    /**
     *
     * @var multitype:integer
     */
    private $created_content_object_ids;

    /**
     *
     * @var string
     */
    private $temporary_path;

    /**
     *
     * @param ContentObjectImportParameters $parameters
     */
    public function __construct($parameters)
    {
        parent :: __construct($parameters);
        $this->created_categories = array();
        $this->created_content_object_ids = array();
        $this->temporary_path = Path :: getInstance()->getTemporaryPath() . uniqid();
        Filesystem :: create_dir($this->temporary_path);
    }

    /*
     * (non-PHPdoc) @see \core\repository\ContentObjectImportController::run()
     */
    public function run()
    {
        $file = $this->get_parameters()->get_file();
        if (self :: is_available())
        {
            if (in_array($this->get_parameters()->get_file()->get_extension(), self :: get_allowed_extensions()))
            {
                $rar = \RarArchive :: open($file->get_path());
                $entries = $rar->getEntries();
                if (count($entries) != 0)
                {
                    $total_filesize = 0;
                    foreach ($entries as $entry)
                    {
                        $total_filesize += $entry->getUnpackedSize();
                    }
                    $calculator = new Calculator(
                        \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(
                            \Chamilo\Core\User\Storage\DataClass\User :: class_name(),
                            (int) $this->get_parameters()->get_user()));
                    if ($calculator->get_available_user_disk_quota() < $total_filesize)
                    {
                        $this->add_message(Translation :: get('InsufficientDiskQuota'), self :: TYPE_ERROR);
                        return array();
                    }
                    if ($this->get_parameters()->get_category() == 0)
                    {
                        $category = new RepositoryCategory();
                        $category->set_id(0);
                        $category->set_user_id($this->get_parameters()->get_user());
                        $category->set_type(RepositoryCategory :: TYPE_PERSONAL);
                    }
                    else
                    {
                        $category = DataManager :: retrieve_by_id(
                            RepositoryCategory :: class_name(),
                            $this->get_parameters()->get_category());
                    }
                    if ($category instanceof RepositoryCategory)
                    {
                        $this->created_categories[md5('/')] = $category;
                        $failures = 0;
                        // Create categories
                        $folders = array();
                        foreach ($entries as $entry)
                        {
                            if ($entry->isDirectory())
                            {
                                $path_parts = explode('/', $entry->getName());
                                $folders[count($path_parts)][] = $entry;
                            }
                        }
                        ksort($folders);
                        foreach ($folders as $subfolders)
                        {
                            foreach ($subfolders as $subfolder)
                            {
                                if (! $this->create_category($subfolder))
                                {
                                    $failures ++;
                                }
                            }
                        }
                        // Create files
                        foreach ($entries as $entry)
                        {
                            if (! $entry->isDirectory())
                            {
                                $path_parts = explode('/', $entry->getName());
                                $file_name = array_pop($path_parts);
                                if (count($path_parts) > 0)
                                {
                                    $dir_name = '/' . implode('/', $path_parts) . '/';
                                }
                                else
                                {
                                    $dir_name = '/';
                                }
                                if (isset($this->created_categories[md5($dir_name)]))
                                {
                                    $parent = $this->created_categories[md5($dir_name)];
                                    if (! $this->create_content_object($file_name, $entry, $parent->get_id()))
                                    {
                                        $failures ++;
                                    }
                                }
                                else
                                {
                                    $failures ++;
                                }
                            }
                        }
                        if ($failures == 0)
                        {
                            $this->add_message(Translation :: get('ObjectImported'), self :: TYPE_CONFIRM);
                            return $this->created_content_object_ids;
                        }
                        else
                        {
                            $this->add_message(Translation :: get('ObjectNotImported'), self :: TYPE_ERROR);
                        }
                    }
                    else
                    {
                        $this->add_message(
                            Translation :: get('NotImportedCategoryWithSameNameExists'),
                            self :: TYPE_ERROR);
                    }
                }
                else
                {
                    $this->add_message(Translation :: get('EmptyRar'), self :: TYPE_WARNING);
                }
                Filesystem :: remove($this->temporary_path);
            }
            else
            {
                $this->add_message(
                    Translation :: get(
                        'UnsupportedFileFormat',
                        array('TYPES' => implode(', ', self :: get_allowed_extensions()))),
                    self :: TYPE_ERROR);
            }
        }
        else
        {
            $this->add_message(Translation :: get('DocumentObjectNotAvailable'), self :: TYPE_WARNING);
        }
    }

    /**
     *
     * @param string $file_name
     * @param \RarEntry $entry
     * @param integer $parent
     * @return boolean
     */
    private function create_content_object($file_name, $entry, $parent)
    {
        $temporary_file_path = $this->temporary_path . DIRECTORY_SEPARATOR . uniqid();
        if (! $entry->extract(null, $temporary_file_path))
        {
            return false;
        }
        $file = new File();
        $file->set_title($file_name);
        $file->set_description($file_name);
        $file->set_owner_id($this->get_parameters()->get_user());
        $file->set_parent_id($parent);
        $file->set_filename($file_name);
        $file->set_temporary_file_path($temporary_file_path);
        if ($file->create())
        {
            $this->created_content_object_ids[] = $file->get_id();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param \RarEntry $entry
     * @return boolean
     */
    private function create_category($entry)
    {
        $path_parts = explode('/', $entry->getName());
        if (count($path_parts) > 1)
        {
            $base_name = array_pop($path_parts);
            $dir_name = '/' . implode('/', $path_parts) . '/';
        }
        else
        {
            $dir_name = '/';
            $base_name = $path_parts[0];
        }
        $category = new RepositoryCategory();
        $category->set_name(
            DataManager :: create_unique_category_name(
                $this->get_parameters()->get_user(),
                $this->created_categories[md5($dir_name)]->get_id(),
                $base_name));
        $category->set_parent($this->created_categories[md5($dir_name)]->get_id());
        $category->set_user_id($this->get_parameters()->get_user());
        $category->set_type(RepositoryCategory :: TYPE_PERSONAL);
        if (! $category->create())
        {
            return false;
        }
        else
        {
            $this->created_categories[md5($dir_name . $base_name . '/')] = $category;
            return true;
        }
    }

    /**
     * Returns the allowed extensions
     *
     * @return array
     */
    public static function get_allowed_extensions()
    {
        return array('rar');
    }

    /**
     *
     * @return boolean
     */
    public static function is_available()
    {
        $file_available = in_array(
            'Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File',
            DataManager :: get_registered_types(true));
        $rar_extension_available = extension_loaded('rar');

        return $rar_extension_available && $file_available;
    }
}
