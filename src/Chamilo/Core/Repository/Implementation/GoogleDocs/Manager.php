<?php
namespace Chamilo\Core\Repository\Implementation\GoogleDocs;

use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Infrastructure\Service\MimeTypeExtensionParser;
use Chamilo\Core\Repository\Implementation\GoogleDocs\Menu\CategoryTreeMenu;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Condition\PatternMatchCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class Manager extends \Chamilo\Core\Repository\External\Manager
{
    const REPOSITORY_TYPE = 'google_docs';
    const PARAM_EXPORT_FORMAT = 'export_format';
    const PARAM_FOLDER = 'folder';
    const ACTION_LOGIN = 'Login';
    const ACTION_LOGOUT = 'Logout';
    const DEFAULT_ACTION = self::ACTION_LOGIN;

    private $categoryTreeMenu;

    public function getCategoryTreeMenu()
    {
        return $this->categoryTreeMenu;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#validate_settings()
     */
    public function validate_settings($external_repository)
    {
        return true;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#support_sorting_direction()
     */
    public function support_sorting_direction()
    {
        return false;
    }

    /**
     *
     * @param \core\repository\external\ExternalObject $object
     * @return string
     */
    public function get_external_repository_object_viewing_url($object)
    {
        $parameters = array();
        $parameters[self::PARAM_ACTION] = self::ACTION_VIEW_EXTERNAL_REPOSITORY;
        $parameters[self::PARAM_EXTERNAL_REPOSITORY_ID] = $object->get_id();
        
        return $this->get_url($parameters);
    }

    /**
     *
     * @return array
     */
    public function get_menu_items()
    {
        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            
            $menu_items = array();
            
            $line = array();
            $line['title'] = '';
            $line['class'] = 'divider';
            
            // Basic list of all documents
            $all_items = array();
            $all_items['title'] = Translation::get('AllItems');
            $all_items['url'] = $this->get_url(array(self::PARAM_FOLDER => null));
            $all_items['class'] = 'home';
            $menu_items[] = $all_items;
            
            // Special lists of documents
            $owned = array();
            
//            $shared = array();
//            $shared['title'] = Translation::get('SharedWithMe');
//            $shared['url'] = $this->get_url(array(self::PARAM_FOLDER => DataConnector::DOCUMENTS_SHARED));
//            $shared['class'] = 'external_repository';
//            $menu_items[] = $shared;
            
//            $recent = array();
//            $recent['title'] = Translation::get('Recent');
//            $recent['url'] = $this->get_url(array(self::PARAM_FOLDER => DataConnector::DOCUMENTS_RECENT));
//            $recent['class'] = 'recent';
//            $menu_items[] = $recent;
            
//            $followed = array();
//            $followed['title'] = Translation::get('Followed');
//            $followed['url'] = $this->get_url(array(self::PARAM_FOLDER => DataConnector::DOCUMENTS_FOLLOWED));
//            $followed['class'] = 'followed';
//            $menu_items[] = $followed;
            
//            $trashed = array();
//            $trashed['title'] = Translation::get('Trash');
//            $trashed['url'] = $this->get_url(array(self::PARAM_FOLDER => DataConnector::DOCUMENTS_TRASH));
//            $trashed['class'] = 'trash';
//            $menu_items[] = $trashed;
            
            return $menu_items;
        }
        else
        {
            return $this->display_warning_page(Translation::get('YouMustBeLoggedIn'));
        }
    }

    public function get_menu()
    {
        if (! isset($this->categoryTreeMenu))
        {
            $this->categoryTreeMenu = new CategoryTreeMenu(
                $this->get_external_repository_manager_connector(), 
                $this->get_menu_items());
        }
        return $this->categoryTreeMenu;
    }

    /*
     * (non-PHPdoc) @see
     * application/common/external_repository_manager/ExternalRepositoryManager#get_external_repository_actions()
     */
    public function get_external_repository_actions()
    {
        $actions = array(self::ACTION_BROWSE_EXTERNAL_REPOSITORY);
        if ($this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $actions[] = self::ACTION_UPLOAD_EXTERNAL_REPOSITORY;
        }
        
        if (! $this->get_external_repository()->get_user_setting($this->get_user_id(), 'session_token'))
        {
            $actions[] = self::ACTION_LOGIN;
        }
        else
        {
            $actions[] = self::ACTION_LOGOUT;
        }
        return $actions;
    }

    /*
     * (non-PHPdoc) @see common/extensions/external_repository_manager/Manager#get_content_object_type_conditions()
     */
    public function get_content_object_type_conditions()
    {
        $document_conditions = array();
        $document_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(File::class_name(), File::PROPERTY_FILENAME), 
            '*.doc', 
            File::get_type_name());
        $document_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(File::class_name(), File::PROPERTY_FILENAME), 
            '*.xls', 
            File::get_type_name());
        $document_conditions[] = new PatternMatchCondition(
            new PropertyConditionVariable(File::class_name(), File::PROPERTY_FILENAME), 
            '*.ppt', 
            File::get_type_name());
        
        return new OrCondition($document_conditions);
    }

    /**
     *
     * @param $object ExternalObject
     * @return array
     */
    public function get_external_repository_object_actions(\Chamilo\Core\Repository\External\ExternalObject $object)
    {
        $actions = parent::get_external_repository_object_actions($object);
        if (in_array(Manager::ACTION_IMPORT_EXTERNAL_REPOSITORY, array_keys($actions)))
        {
            unset($actions[Manager::ACTION_IMPORT_EXTERNAL_REPOSITORY]);
            $export_types = $object->get_export_types();
            
            $mimeTypeExtensionParser = new MimeTypeExtensionParser();
            
            foreach ($export_types as $export_type)
            {
                $exportTypeExtension = $mimeTypeExtensionParser->getExtensionForMimeType($export_type);
                if (! $exportTypeExtension)
                {
                    continue;
                }
                
                $camelizedExportTypeExtension = StringUtilities::getInstance()->createString($exportTypeExtension)->upperCamelize();
                
                $actions[$export_type] = new ToolbarItem(
                    Translation::getInstance()->getTranslation(
                        'ImportAs', 
                        array('TYPE' => $exportTypeExtension), 
                        self::context()), 
                    Theme::getInstance()->getFileExtension($camelizedExportTypeExtension), 
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_IMPORT_EXTERNAL_REPOSITORY, 
                            self::PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(), 
                            self::PARAM_EXPORT_FORMAT => $export_type)), 
                    ToolbarItem::DISPLAY_ICON);
            }
        }
        
        return $actions;
    }

    /**
     *
     * @return string
     */
    public function get_repository_type()
    {
        return self::REPOSITORY_TYPE;
    }
}
