<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;

/**
 * $Id: importer.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib.repository_manager.component
 */
class ImporterComponent extends Manager
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $type = Request :: get(self :: PARAM_IMPORT_TYPE);

        if ($type)
        {
            $import_form = ContentObjectImportForm :: factory(
                $type,
                $this->getWorkspace(),
                $this,
                'post',
                $this->get_url(array(self :: PARAM_IMPORT_TYPE => $type)));

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
                    $this->get_user_id(),
                    $category_id,
                    $file,
                    $values);
                $controller = ContentObjectImportController :: factory($parameters);
                $controller->run();

                $messages = $controller->get_messages_for_url();

                Session :: register(self :: PARAM_MESSAGES, $messages);

                $parameters = array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CONTENT_OBJECTS);

                $this->simple_redirect($parameters);
            }
            else
            {
                BreadcrumbTrail :: get_instance()->add(
                    new Breadcrumb(
                        $this->get_url(),
                        Translation :: get(
                            'ImportType',
                            array(
                                'TYPE' => Translation :: get(
                                    'ImportType' . StringUtilities :: getInstance()->createString($type)->upperCamelize())))));

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
                     Theme :: getInstance()->getImagePath(Manager :: package(), 'Import/' . $type) . ');">';
                $html[] = $name;
                $html[] = '</div>';
                $html[] = '</a>';
            }

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_types()
    {
        $types_folder = Path :: getInstance()->namespaceToFullPath('Chamilo\Core\Repository\Common\Import');
        $folders = Filesystem :: get_directory_content($types_folder, Filesystem :: LIST_DIRECTORIES, false);
        $folder_types = array();
        $sort_types = array();

        foreach ($folders as $folder)
        {
            $class = '\Chamilo\Core\Repository\Common\Import\\' . $folder . '\\' . $folder .
                 'ContentObjectImportController';

            if (class_exists($class) && $class :: is_available())
            {
                $folder_types[$folder] = Translation :: get('ImportType' . $folder);
                $sort_types[$folder] = strtolower(Translation :: get('ImportType' . $folder));
            }
        }

        asort($sort_types);

        $types = array();

        foreach ($sort_types as $key => $value)
        {
            $types[$key] = $folder_types[$key];
        }

        return $types;
    }
}
