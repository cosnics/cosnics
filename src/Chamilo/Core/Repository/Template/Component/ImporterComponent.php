<?php
namespace Chamilo\Core\Repository\Template\Component;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Template\Manager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

/**
 * $Id: template_importer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * 
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete an object from the users repository.
 */
class ImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $extra_params = array();
        
        $dummy_user_id = 0;
        
        $type = Request :: get(self :: PARAM_IMPORT_TYPE);
        
        if ($type)
        {
            // $import_form = new ContentObjectImportForm('import', 'post', $this->get_url($extra_params), 0, $user,
            // null, false);
            $import_form = ContentObjectImportForm :: factory(
                $type, 
                $this, 
                'post', 
                $this->get_url($extra_params), 
                FALSE);
            
            if ($import_form->validate())
            {
                $values = $import_form->exportValues();
                $parent_id = $values[ContentObject :: PROPERTY_PARENT_ID];
                $new_category_name = $values[ContentObjectImportForm :: NEW_CATEGORY];
                
                if (! StringUtilities :: getInstance()->isNullOrEmpty($new_category_name, true))
                {
                    $new_category = new RepositoryCategory();
                    $new_category->set_name($new_category_name);
                    $new_category->set_parent($parent_id);
                    $new_category->set_user_id($this->get_user_id());
                    $new_category->set_type(PersonalWorkspace :: WORKSPACE_TYPE);
                    if (! $new_category->create())
                    {
                        throw new \Exception(Translation :: get('CategoryCreationFailed'));
                    }
                    else
                    {
                        $category_id = $new_category->get_id();
                    }
                }
                else
                {
                    $category_id = $parent_id;
                }
                
                if (isset($_FILES[ContentObjectImportForm :: IMPORT_FILE_NAME]))
                {
                    $file = FileProperties :: from_upload($_FILES[ContentObjectImportForm :: IMPORT_FILE_NAME]);
                }
                else
                {
                    $file = null;
                }
                
                $parameters = ImportParameters :: factory(
                    $import_form->exportValue(ContentObjectImportForm :: PROPERTY_TYPE), 
                    $dummy_user_id, 
                    $category_id, 
                    $file, 
                    $values);
                $controller = ContentObjectImportController :: factory($parameters);
                $co_ids = $controller->run();
                
                $condition = new InCondition(
                    new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID), 
                    $co_ids);
                $content_objects = \Chamilo\Core\Repository\Storage\DataManager :: retrieve_active_content_objects(
                    ContentObject :: class_name(), 
                    $condition);
                $success = true;
                while ($content_object = $content_objects->next_result())
                {
                    $content_object->set_owner_id($dummy_user_id);
                    $content_object->update() ? $success : $success = false;
                }
                // $content_object = $import_form->import_content_object();
                
                if (! $co_ids || count($co_ids) < 1 || ! $success)
                {
                    $message = Translation :: get(
                        'ObjectNotImported', 
                        array('OBJECT' => Translation :: get('ContentObject')), 
                        Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get(
                        'ObjectImported', 
                        array('OBJECT' => Translation :: get('ContentObject')), 
                        Utilities :: COMMON_LIBRARIES);
                }
                
                $this->redirect(
                    $message, 
                    ! $co_ids || count($co_ids) < 1, 
                    array(Application :: PARAM_ACTION => self :: ACTION_BROWSE_TEMPLATES));
            }
            else
            {
                $html = array();
                
                $html[] = $this->render_header();
                $html[] = $import_form->toHtml();
                $html[] = $this->render_footer();
                
                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            BreadcrumbTrail :: get_instance()->add(
                new Breadcrumb($this->get_url(), Translation :: get('ChooseImportFormat')));
            
            $html = array();
            
            $html[] = $this->render_header();
            
            foreach ($this->get_types() as $type => $name)
            {
                $html[] = '<a href="' . $this->get_url(array(self :: PARAM_IMPORT_TYPE => $type)) . '">';
                $html[] = '<div class="create_block" style="background-image: url(' .
                     Theme :: getInstance()->getImagePath('Chamilo\Core\Repository', 'Import/' . $type) . ');">';
                $html[] = $name;
                $html[] = '</div>';
                $html[] = '</a>';
            }
            
            $html[] = $this->render_footer();
            
            return implode(PHP_EOL, $html);
        }
    }

    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('template_importer');
    }

    public function get_additional_parameters()
    {
        $parameters = array();
        if (Request :: get(self :: PARAM_IMPORT_TYPE))
        {
            $parameters[] = self :: PARAM_IMPORT_TYPE;
        }
        return $parameters;
    }

    public function get_types()
    {
        $types_folder = Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Repository\Common\Import');
        $folders = Filesystem :: get_directory_content($types_folder, Filesystem :: LIST_DIRECTORIES, false);
        $types = array();
        foreach ($folders as $folder)
        {
            $types[$folder] = Translation :: get('ImportType' . $folder);
        }
        return $types;
    }

    /**
     * Returns the admin breadcrumb generator
     * 
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new \Chamilo\Core\Admin\Core\BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
