<?php
namespace Chamilo\Core\Repository\Common\Action;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\ContentObject\Hotpotatoes\Storage\DataClass\Hotpotatoes;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Webpage\Storage\DataClass\Webpage;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Interfaces\ComplexContentObjectSupport;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\String\Text;

/**
 *
 * @package repository.lib
 */
/**
 * Class that makes it possible to move a content object to another user repository.
 * This class takes the following
 * things in account: Simple content object The children of a complex object The included objects The attached objects
 * The physical files (documents, hotpotatoes, scorm) The references of LearningPathItem / PortfolioItem The
 * LearningPath prerequisites (only for chamilo learning paths) Links to other files in a description field
 *
 * @author Sven Vanpoucke
 */
class ContentObjectOwnerChanger
{

    /**
     * The target repository
     *
     * @var Int
     */
    private $target_repository;

    /**
     * Counter to count the items that failed while copying
     *
     * @var Int
     */
    private $failed;

    /**
     * The target category id
     *
     * @var int
     */
    private $category_id;

    /**
     * Move a content object to the target repository
     *
     * @param Int $co
     * @param Int $category_id
     * @return Int ID of the new content object
     */
    public function move_content_object($co, $target_repository = 0, $category_id = 0)
    {
        $this->failed = 0;
        $this->target_repository = $target_repository;
        $this->category_id = $category_id;

        return $this->change_content_object_owner($co);
    }

    /**
     * Returns how many items have failed
     *
     * @return int
     */
    public function get_failed()
    {
        return $this->failed;
    }

    /**
     * Create a content object in the target repository
     *
     * @param ContentObject $co
     * @return ContentObject the moved content object
     */
    private function change_content_object_owner($co)
    {
        $old_user_id = $co->get_owner_id();
        $old_location = $co->get_rights_location();

        if ($old_user_id == $this->target_repository)
        {
            return $co;
        }

        $co->set_owner_id($this->target_repository);

        // Set the category
        if (DataManager::is_helper_type($co->get_type()))
        {
            $co->set_parent_id(0);
        }
        else
        {
            $co->set_parent_id($this->category_id);
        }

        if (! $co->update())
        {
            $this->failed ++;
            return false;
        }

        // Process the versions
        $this->move_versions($co);

        // Process the children
        $this->move_complex_children($co);

        // Process the included items, attachments and sync data
        $this->move_includes($co);
        $this->move_attachments($co);

        // Process the physical files
        $this->move_files($co, $old_user_id);

        if (in_array($co->get_type(), DataManager::get_active_helper_types()))
        {
            $co = DataManager::retrieve_by_id(ContentObject::class_name(), $co->get_reference());
            if ($co)
            {
                $this->change_content_object_owner($co);
            }
        }

        return $co;
    }

    private function move_versions($content_object)
    {
        if ($content_object->is_latest_version())
        {
            $versions = DataManager::retrieve_content_object_versions($content_object);
            foreach ($versions as $version)
            {
                $this->change_content_object_owner($version);
            }
        }
    }

    /**
     * Move the children of a content object (both items and wrappers)
     *
     * @param ContentObject $co
     */
    private function move_complex_children($co)
    {
        if (! $co instanceof ComplexContentObjectSupport)
        {
            return;
        }

        $item_references = array();

        $condition = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(),
                ComplexContentObjectItem::PROPERTY_PARENT),
            new StaticConditionVariable($co->get_id()),
            ComplexContentObjectItem::get_table_name());

        $items = DataManager::retrieve_complex_content_object_items(ComplexContentObjectItem::class_name(), $condition);
        while ($item = $items->next_result())
        {
            $co = DataManager::retrieve_by_id(ContentObject::class_name(), $item->get_ref());
            $this->change_content_object_owner($co);

            $item->set_user_id($this->target_repository);
            $item->update();
        }
    }

    /**
     * Move the included content objects
     *
     * @param ContentObject $co
     */
    private function move_includes($co)
    {
        $includes = $co->get_includes();

        foreach ($includes as $include)
        {
            $include = DataManager::retrieve_by_id(ContentObject::class_name(), $include->get_id());
            $this->change_content_object_owner($include);
        }
    }

    /**
     * Move the attached content objects
     *
     * @param ContentObject $co
     */
    private function move_attachments($content_object)
    {
        $attachments = $content_object->get_attachments();

        foreach ($attachments as $attachment)
        {
            $attachment = DataManager::retrieve_by_id(ContentObject::class_name(), $attachment->get_id());
            $this->change_content_object_owner($attachment);
        }
    }

    /**
     * Moves the physical files
     *
     * @param ContentObject $co;
     */
    private function move_files($co, $old_user_id)
    {
        $type = $co->get_type();
        switch ($type)
        {
            case File::get_type_name() :
                return $this->move_document_files($co);
            case Webpage::get_type_name() :
                return $this->move_document_files($co);
            case Hotpotatoes::get_type_name() :
                return $this->move_hotpotatoes_files($co, $old_user_id);
            case LearningPath::get_type_name() :
                if ($co->get_version() == 'SCORM1.2' || $co->get_version() == 'SCORM2004')
                {
                    return $this->move_scorm_files($co, $old_user_id);
                }
            default :
                return;
        }
    }

    /**
     * Move the files from the content object type document
     *
     * @param Document $co
     */
    private function move_document_files($co)
    {
        $base_path = Path::getInstance()->getRepositoryPath();
        $new_path = $this->target_repository . '/' . Text::char_at($co->get_hash(), 0);
        $new_full_path = $base_path . $new_path;
        Filesystem::create_dir($new_full_path);

        $new_hash = Filesystem::create_unique_name($new_full_path, $co->get_hash());
        $new_full_path .= '/' . $new_hash;

        Filesystem::move_file($co->get_full_path(), $new_full_path);

        $co->set_hash($new_hash);
        $co->set_path($new_path . '/' . $new_hash);
        $co->set_storage_path($base_path);
        $co->update();
    }

    /**
     * Move the files from the content object type hotpotatoes
     *
     * @param Hotpotatoes $co
     */
    private function move_hotpotatoes_files($co, $old_user_id)
    {
        $filename = basename($co->get_path());
        $base_path = Path::getInstance()->getStoragePath('hotpotatoes') . $this->target_repository . '/';

        $new_path = Filesystem::create_unique_name($base_path, dirname($co->get_path()));
        $new_full_path = $base_path . $new_path;
        Filesystem::create_dir($new_full_path);

        Filesystem::recurse_move(
            Path::getInstance()->getStoragePath('hotpotatoes') . $old_user_id . '/' . dirname($co->get_path()),
            $new_full_path,
            false);

        $co->set_path($new_path . '/' . $filename);
        $co->update();
    }

    /**
     * Move the files from the content object type learning path
     *
     * @param LearningPath $co
     */
    private function move_scorm_files($co, $old_user_id)
    {
        $base_path = Path::getInstance()->getStoragePath('scorm') . $this->target_repository . '/';

        $new_folder = Filesystem::create_unique_name($base_path, $co->get_path());
        $new_full_path = $base_path . $new_folder;
        Filesystem::create_dir($new_full_path);

        Filesystem::recurse_move(
            Path::getInstance()->getStoragePath('scorm') . $old_user_id . '/' . $co->get_path(),
            $new_full_path,
            false);

        $co->set_path($new_folder);
        $co->update();
    }
}
