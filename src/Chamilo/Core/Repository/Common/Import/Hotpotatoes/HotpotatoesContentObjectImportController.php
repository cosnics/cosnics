<?php
namespace Chamilo\Core\Repository\Common\Import\Hotpotatoes;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRelationRepository;
use Chamilo\Core\Repository\Workspace\Service\ContentObjectRelationService;
use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\File\Compression\Filecompression;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\File\Properties\WebpageProperties;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\String\Text;

class HotpotatoesContentObjectImportController extends ContentObjectImportController
{
    const FORMAT = 'Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes';

    private $created_objects = array();

    private $failures = 0;

    public function run()
    {
        $file = $this->get_parameters()->get_file();

        if (self:: is_available())
        {
            if (in_array($file->get_extension(), self:: get_allowed_extensions()))
            {
                if (in_array($file->get_extension(), array('htm', 'html')))
                {
                    $filename_hash = md5($file->get_path());
                    $relative_folder_path = Text:: char_at($filename_hash, 0);
                    $full_folder_path = Path:: getInstance()->getPublicStoragePath(
                            'Chamilo\Core\Repository\ContentObject\Hotpotatoes'
                        ) .
                        $this->get_parameters()->get_user() . '/' . $relative_folder_path;

                    Filesystem:: create_dir($full_folder_path);
                    $unique_hash = Filesystem:: create_unique_name($full_folder_path, $filename_hash);
                    Filesystem:: create_dir($full_folder_path . '/' . $unique_hash);

                    $relative_path = $relative_folder_path . '/' . $unique_hash . '/index.htm';
                    $path_to_save = $full_folder_path . '/' . $unique_hash . '/index.htm';

                    Filesystem:: move_file($file->get_path(), $path_to_save);

                    $this->process_content_object($relative_path, $path_to_save);
                }
                else
                {
                    $this->process_archive($file->get_path());

                    if ($this->failures > 0)
                    {
                        $this->add_message(Translation:: get('ObjectNotImported'), self :: TYPE_ERROR);
                    }
                    else
                    {
                        $this->add_message(Translation:: get('ObjectImported'), self :: TYPE_CONFIRM);
                    }
                }

                return $this->created_objects;
            }
            else
            {
                $this->add_message(
                    Translation:: get(
                        'UnsupportedFileFormat',
                        array('TYPES' => implode(', ', self:: get_allowed_extensions()))
                    ),
                    self :: TYPE_ERROR
                );
            }
        }
        else
        {
            $this->add_message(
                Translation:: get('ObjectNotAvailable', array('OBJECT' => 'Hot Potatoes')),
                self :: TYPE_WARNING
            );
        }
    }

    /**
     *
     * @param string $exercise_path
     */
    public function process_content_object($exercise_path, $full_exercise_path)
    {
        $webpage_properties = WebpageProperties:: from_path($full_exercise_path);

        $hotpotatoes = ContentObject:: factory(
            'Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes'
        );
        $hotpotatoes->set_owner_id($this->get_parameters()->get_user());
        $hotpotatoes->set_parent_id($this->determine_parent_id());

        if ($webpage_properties->get_title())
        {
            $hotpotatoes->set_title($webpage_properties->get_title());
        }
        else
        {
            $hotpotatoes->set_title($this->get_parameters()->get_file()->get_name());
        }

        if (!$webpage_properties->get_description() && !$webpage_properties->get_title())
        {
            $hotpotatoes->set_description($this->get_parameters()->get_file()->get_name());
        }
        else
        {
            if ($webpage_properties->get_description())
            {
                $hotpotatoes->set_description($webpage_properties->get_description());
            }
            else
            {
                $hotpotatoes->set_description($webpage_properties->get_title());
            }
        }

        $hotpotatoes->set_path($exercise_path);

        if (!$hotpotatoes->create())
        {
            $this->process_workspace($hotpotatoes);

            $this->failures ++;
        }
    }

    public function process_archive($archive_path)
    {
        $htm_files = array();
        $extra_files = array();

        $zip = Filecompression:: factory();
        $extracted_files_dir = $zip->extract_file($archive_path, false);
        $this->process_folder($extracted_files_dir);
        Filesystem:: remove($extracted_files_dir);
    }

    public function process_folder($folder_path)
    {
        $hotpotatoes_path =
            Path:: getInstance()->getPublicStoragePath('Chamilo\Core\Repository\ContentObject\Hotpotatoes') .
            $this->get_parameters()->get_user() .
            '/';

        $entries = Filesystem:: get_directory_content($folder_path, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
        if (count($entries) == 0)
        {
            $this->add_message(Translation:: get('EmptyHotpotatoes'), self :: TYPE_WARNING);
        }
        else
        {
            $failures = 0;

            // Process all files in the archive
            foreach ($entries as $entry)
            {
                $entry_path = $folder_path . '/' . $entry;

                if (is_dir($entry_path))
                {
                    // Process a folder;
                    $this->process_folder($entry_path);
                }
                elseif (is_file($entry_path))
                {
                    $entry_properties = FileProperties:: from_path($entry_path);

                    if ($entry_properties->get_extension() == 'zip')
                    {
                        $this->process_archive($entry_properties->get_path());
                    }
                    else
                    {
                        if (in_array($entry_properties->get_extension(), array('htm', 'html')))
                        {
                            $htm_files[] = $entry_properties;
                        }
                        else
                        {
                            $extra_files[] = $entry_properties;
                        }
                    }
                }
            }

            // If there's an index.htm file, the floating files were probably all generated by Masher, so treat it as a
            // single Hot Potatoes exerciese
            $masher = false;

            if (count($htm_files) > 1)
            {
                foreach ($htm_files as $html_file_properties)
                {
                    if ($html_file_properties->get_name() == 'index')
                    {
                        $masher = $html_file_properties;
                    }
                }
            }
            else
            {
                $masher = $htm_files[0];
            }

            if ($masher instanceof FileProperties)
            {
                $masher_hash = md5($masher->get_path());
                $relative_folder_path = Text:: char_at($masher_hash, 0);
                $full_folder_path = Path :: getInstance()->getPublicStoragePath('Chamilo\Core\Repository\ContentObject\Hotpotatoes') .
                    $this->get_parameters()->get_user() . '/' . $relative_folder_path;

                Filesystem:: create_dir($full_folder_path);
                $unique_hash = Filesystem:: create_unique_name($full_folder_path, $masher_hash);

                foreach ($htm_files as $masher_file_properties)
                {
                    $relative_path = $relative_folder_path . '/' . $unique_hash . '/' .
                        $masher_file_properties->get_name_extension();
                    $path_to_save = $full_folder_path . '/' . $unique_hash . '/' .
                        $masher_file_properties->get_name_extension();
                    Filesystem:: move_file($masher_file_properties->get_path(), $path_to_save);
                }

                foreach ($extra_files as $masher_extra_file_properties)
                {
                    $relative_path = $relative_folder_path . '/' . $unique_hash . '/' .
                        $masher_extra_file_properties->get_name_extension();
                    $path_to_save = $full_folder_path . '/' . $unique_hash . '/' .
                        $masher_extra_file_properties->get_name_extension();
                    Filesystem:: move_file($masher_extra_file_properties->get_path(), $path_to_save);
                }

                $masher_path = $relative_folder_path . '/' . $unique_hash . '/' . $masher->get_name_extension();
                $masher_full_path = $full_folder_path . '/' . $unique_hash . '/' . $masher->get_name_extension();

                $this->process_content_object($masher_path, $masher_full_path);
            }
            else
            {
                // If the files are not the result of Masher, they were probably put into one folder by a user, in which
                // case they would more then likely be simple, individual htm files. There shouldn't be any additional
                // files, as they might overlap if there were.

                foreach ($htm_files as $hot_potatoes_file_properties)
                {
                    $hot_potatoes_hash = md5($hot_potatoes_file_properties->get_path());
                    $relative_folder_path = Text:: char_at($hot_potatoes_hash, 0);
                    $full_folder_path = Path :: getInstance()->getPublicStoragePath('Chamilo\Core\Repository\ContentObject\Hotpotatoes') .
                        $this->get_parameters()->get_user() . '/' . $relative_folder_path;

                    Filesystem:: create_dir($full_folder_path);
                    $unique_hash = Filesystem:: create_unique_name($full_folder_path, $hot_potatoes_hash);
                    $relative_path = $relative_folder_path . '/' . $unique_hash . '/' .
                        $hot_potatoes_file_properties->get_name_extension();
                    $path_to_save = $full_folder_path . '/' . $unique_hash . '/' .
                        $hot_potatoes_file_properties->get_name_extension();
                    Filesystem:: move_file($hot_potatoes_file_properties->get_path(), $path_to_save);

                    $this->process_content_object($relative_path, $path_to_save);
                }
            }
        }
    }

    /**
     *
     * @return multitype:string
     */
    public static function get_allowed_extensions()
    {
        return array('zip', 'htm', 'html');
    }

    public static function is_available()
    {
        return in_array(self :: FORMAT, DataManager:: get_registered_types(true));
    }

    /**
     *
     * @return integer
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

    /**
     * @param ContentObject $contentObject
     */
    public function process_workspace(ContentObject $contentObject)
    {
        if ($this->get_parameters()->getWorkspace() instanceof Workspace)
        {
            $contentObjectRelationService = new ContentObjectRelationService(new ContentObjectRelationRepository());
            $contentObjectRelationService->createContentObjectRelation(
                $this->get_parameters()->getWorkspace()->getId(),
                $contentObject->getId(),
                $this->get_parameters()->get_category()
            );
        }
    }
}
