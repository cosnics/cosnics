<?php
namespace Chamilo\Core\Repository\Viewer\Component;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportImplementation;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Form\ContentObjectImportForm;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataClass\RepositoryCategory;
use Chamilo\Core\Repository\Viewer\Manager;
use Chamilo\Core\Repository\Workspace\PersonalWorkspace;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Properties\FileProperties;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class ImporterComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        if ($this->get_user()->is_anonymous_user())
        {
            throw new NotAllowedException();
        }

        $type = Request :: get(self :: PARAM_IMPORT_TYPE);

        if ($type)
        {
            $import_form = ContentObjectImportForm :: factory(
                $type,
                new PersonalWorkspace($this->get_user()),
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
                    $new_category->set_type_id($this->get_user_id());
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
                    new PersonalWorkspace($this->get_user()),
                    $category_id,
                    $file,
                    $values);
                $controller = ContentObjectImportController :: factory($parameters);
                $content_object_ids = $controller->run();
                $filtered_content_object_ids = $this->filter_content_object_ids($content_object_ids);

                $messages = $controller->get_messages_for_url();

                Session :: register(Application :: PARAM_MESSAGES, $messages);

                if (! $controller->has_messages(ContentObjectImportController :: TYPE_ERROR))
                {
                    if (count($filtered_content_object_ids) == 1)
                    {
                        if (count($content_object_ids) > 1)
                        {
                            $messages = Session :: retrieve(Application :: PARAM_MESSAGES);
                            $messages[Application :: PARAM_MESSAGE][] = Translation :: get(
                                'MultipleObjectsImportedButOneUseable');
                            $messages[Application :: PARAM_MESSAGE_TYPE][] = ContentObjectImportController :: TYPE_ERROR;
                            Session :: register(Application :: PARAM_MESSAGES, $messages);
                        }

                        $this->simple_redirect(
                            array(
                                self :: PARAM_ACTION => self :: ACTION_VIEWER,
                                self :: PARAM_ID => $filtered_content_object_ids[0]));
                    }
                    elseif (count($filtered_content_object_ids) == 0)
                    {
                        if (count($content_object_ids) > 0)
                        {
                            $messages = Session :: retrieve(Application :: PARAM_MESSAGES);
                            $message = (count($content_object_ids) == 1 ? 'ObjectImportedButNoneUseable' : 'ObjectsImportedButNoneUseable');
                            $messages[Application :: PARAM_MESSAGE][] = Translation :: get($message);
                            $messages[Application :: PARAM_MESSAGE_TYPE][] = ContentObjectImportController :: TYPE_ERROR;
                            Session :: register(Application :: PARAM_MESSAGES, $messages);
                        }

                        $this->simple_redirect();
                    }
                    else
                    {
                        Session :: register(self :: PARAM_IMPORTED_CONTENT_OBJECT_IDS, $content_object_ids);
                        $this->simple_redirect(array(self :: PARAM_ACTION => self :: ACTION_IMPORTED_SELECTER));
                    }
                }
                else
                {
                    $this->simple_redirect();
                }

            /**
             * TODO: Do something with the result here 1.
             * If the import produces no errors AND only 1 object was
             * returned: select it 2. If the import produces no errors AND results in multiple objects: show selection
             * table ALWAYS filter on allowed types (since CPO can result in multiple objects of multiple types)
             */
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
                                    'ImportType' . StringUtilities :: getInstance()->createString($type)->upperCamelize(),
                                    null,
                                    \Chamilo\Core\Repository\Manager :: context())),
                            \Chamilo\Core\Repository\Manager :: context())));

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

            foreach ($this->get_import_types() as $type => $name)
            {

                $html[] = '<a href="' . $this->get_url(array(self :: PARAM_IMPORT_TYPE => $type)) . '">';
                $html[] = '<div class="create_block" style="background-image: url(' .
                     Theme :: getInstance()->getImagePath(
                        \Chamilo\Core\Repository\Manager :: context(),
                        'Import/' . StringUtilities :: getInstance()->createString($type)->upperCamelize()->__toString()) .
                     ');">';
                $html[] = $name;
                $html[] = '</div>';
                $html[] = '</a>';
            }

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    public function get_import_types()
    {
        $import_types = array();

        foreach ($this->get_types() as $type)
        {
            $object_import_types = ContentObjectImportImplementation :: get_types_for_object(
                ClassnameUtilities :: getInstance()->getNamespaceParent($type, 3));

            foreach ($object_import_types as $object_import_type)
            {
                if (! array_key_exists($object_import_type, $import_types))
                {
                    $import_types[$object_import_type] = Translation :: get(
                        'ImportType' .
                             (string) StringUtilities :: getInstance()->createString($object_import_type)->upperCamelize(),
                            null,
                            \Chamilo\Core\Repository\Manager :: context());
                }
            }
        }

        return $import_types;
    }

    public function filter_content_object_ids($content_object_ids)
    {
        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_ID),
            $content_object_ids);
        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject :: class_name(), ContentObject :: PROPERTY_TYPE),
            $this->get_types());
        $condition = new AndCondition($conditions);

        $property = ContentObject :: PROPERTY_ID;
        $parameters = new DataClassDistinctParameters($condition, $property);
        return \Chamilo\Core\Repository\Storage\DataManager :: distinct(ContentObject :: class_name(), $parameters);
    }
}
