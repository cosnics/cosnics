<?php
namespace Chamilo\Core\Repository\Viewer\Component;

use Chamilo\Core\Repository\Common\Import\ContentObjectImportController;
use Chamilo\Core\Repository\Common\Import\ContentObjectImportImplementation;
use Chamilo\Core\Repository\Common\Import\ImportFormParameters;
use Chamilo\Core\Repository\Common\Import\ImportParameters;
use Chamilo\Core\Repository\Common\Import\ImportTypeSelector;
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
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassDistinctParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\InCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Storage\DataClass\Property\DataClassProperties;

class ImporterComponent extends Manager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    public function run()
    {
        $this->checkAuthorization(\Chamilo\Core\Repository\Manager::context());

        if ($this->get_maximum_select() == 1)
        {
            throw new NotAllowedException();
        }

        $type = Request::get(self::PARAM_IMPORT_TYPE);

        if ($type)
        {
            $importFormParameters = new ImportFormParameters(
                $type, $this->getWorkspace(), $this, $this->get_url(array(self::PARAM_IMPORT_TYPE => $type)), 'post',
                true, $this->get_maximum_select()
            );

            $importForm = ContentObjectImportForm::factory($importFormParameters);

            $this->addPublishButtonToImportForm($importForm);

            if ($importForm->validate())
            {
                $values = $importForm->exportValues();
                $parent_id = $values[ContentObject::PROPERTY_PARENT_ID];
                $new_category_name = $values[ContentObjectImportForm::NEW_CATEGORY];

                if (!StringUtilities::getInstance()->isNullOrEmpty($new_category_name, true))
                {
                    $new_category = new RepositoryCategory();
                    $new_category->set_name($new_category_name);
                    $new_category->set_parent($parent_id);
                    $new_category->set_type_id($this->getWorkspace()->getId());
                    $new_category->set_type($this->getWorkspace()->getWorkspaceType());

                    if (!$new_category->create())
                    {
                        throw new \Exception(Translation::get('CategoryCreationFailed'));
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

                if (isset($_FILES[ContentObjectImportForm::IMPORT_FILE_NAME]))
                {
                    $file = FileProperties::from_upload($_FILES[ContentObjectImportForm::IMPORT_FILE_NAME]);
                }
                else
                {
                    $file = null;
                }

                $parameters = ImportParameters::factory(
                    $importForm->exportValue(ContentObjectImportForm::PROPERTY_TYPE), $this->get_user_id(),
                    $this->getWorkspace(), $category_id, $file, $values
                );

                $controller = ContentObjectImportController::factory($parameters);
                $content_object_ids = $controller->run();

                $filtered_content_object_ids = $this->filter_content_object_ids($content_object_ids);
                $messages = $controller->get_messages_for_url();

                Session::register(Application::PARAM_MESSAGES, $messages);

                if (!$controller->has_messages(ContentObjectImportController::TYPE_ERROR))
                {
                    if (count($filtered_content_object_ids) == 1)
                    {
                        if (count($content_object_ids) > 1)
                        {
                            $messages = Session::retrieve(Application::PARAM_MESSAGES);
                            $messages[Application::PARAM_MESSAGE][] = Translation::get(
                                'MultipleObjectsImportedButOneUseable'
                            );
                            $messages[Application::PARAM_MESSAGE_TYPE][] = ContentObjectImportController::TYPE_ERROR;
                            Session::register(Application::PARAM_MESSAGES, $messages);
                        }

                        $this->simple_redirect(array(self::PARAM_ID => $filtered_content_object_ids[0]));
                    }
                    elseif (count($filtered_content_object_ids) == 0)
                    {
                        if (count($content_object_ids) > 0)
                        {
                            $messages = Session::retrieve(Application::PARAM_MESSAGES);
                            $message = (count($content_object_ids) == 1 ? 'ObjectImportedButNoneUseable' :
                                'ObjectsImportedButNoneUseable');
                            $messages[Application::PARAM_MESSAGE][] = Translation::get($message);
                            $messages[Application::PARAM_MESSAGE_TYPE][] = ContentObjectImportController::TYPE_ERROR;
                            Session::register(Application::PARAM_MESSAGES, $messages);
                        }

                        $this->simple_redirect();
                    }
                    else
                    {
                        $this->simple_redirect(array(self::PARAM_ID => $content_object_ids));
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
                BreadcrumbTrail::getInstance()->add(
                    new Breadcrumb(
                        $this->get_url(), Translation::get(
                        'ImportType', array(
                            'TYPE' => Translation::get(
                                'ImportType' . StringUtilities::getInstance()->createString($type)->upperCamelize(),
                                null, \Chamilo\Core\Repository\Manager::context()
                            )
                        ), \Chamilo\Core\Repository\Manager::context()
                    )
                    )
                );

                $html = array();

                $html[] = $this->render_header();
                $html[] = $importForm->toHtml();
                $html[] = $this->render_footer();

                return implode(PHP_EOL, $html);
            }
        }
        else
        {
            BreadcrumbTrail::getInstance()->add(
                new Breadcrumb($this->get_url(), Translation::get('ChooseImportFormat'))
            );

            $html = array();

            $html[] = $this->render_header();

            $importTypeSelector = new ImportTypeSelector($this->get_parameters(), $this->get_types());

            $html[] = $importTypeSelector->renderTypeSelector();

//            foreach ($this->get_import_types() as $type => $name)
//            {
//
//                $html[] = '<a href="' . $this->get_url(array(self::PARAM_IMPORT_TYPE => $type)) . '">';
//                $html[] =
//                    '<div class="create_block" style="background-image: url(' . Theme::getInstance()->getImagePath(
//                        \Chamilo\Core\Repository\Manager::context(),
//                        'Import/' . StringUtilities::getInstance()->createString($type)->upperCamelize()->__toString()
//                    ) . ');">';
//                $html[] = $name;
//                $html[] = '</div>';
//                $html[] = '</a>';
//            }

            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Form\ContentObjectImportForm $importForm
     */
    public function addPublishButtonToImportForm($importForm)
    {
        $buttons = $importForm->getElement('buttons');

        $buttonElements = $buttons->getElements();
        $buttonElements[] = $importForm->createElement(
            'style_submit_button', 'publish', Translation::get('Publish', null, Utilities::COMMON_LIBRARIES),
            array('id' => 'publish-button', 'class' => 'hidden'), null, new FontAwesomeGlyph('plus')
        );

        $buttons->setElements($buttonElements);

        $importForm->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->getJavascriptPath(self::package(), true) . 'ImporterComponent.js'
        )
        );
    }

    public function get_import_types()
    {
        $import_types = array();

        foreach ($this->get_types() as $type)
        {
            $object_import_types = ContentObjectImportImplementation::get_types_for_object(
                ClassnameUtilities::getInstance()->getNamespaceParent($type, 3)
            );

            foreach ($object_import_types as $object_import_type)
            {
                if (!array_key_exists($object_import_type, $import_types))
                {
                    $import_types[$object_import_type] = Translation::get(
                        'ImportType' .
                        (string) StringUtilities::getInstance()->createString($object_import_type)->upperCamelize(),
                        null, \Chamilo\Core\Repository\Manager::context()
                    );
                }
            }
        }

        return $import_types;
    }

    public function filter_content_object_ids($content_object_ids)
    {
        $conditions = array();
        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_ID), $content_object_ids
        );
        $conditions[] = new InCondition(
            new PropertyConditionVariable(ContentObject::class_name(), ContentObject::PROPERTY_TYPE), $this->get_types()
        );
        $condition = new AndCondition($conditions);

        $parameters = new DataClassDistinctParameters(
            $condition, new DataClassProperties(
                array(new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_ID))
            )
        );

        return \Chamilo\Core\Repository\Storage\DataManager::distinct(ContentObject::class_name(), $parameters);
    }

    public function getWorkspace()
    {
        if (!isset($this->currentWorkspace))
        {
            $this->currentWorkspace = new PersonalWorkspace($this->get_user());
        }

        return $this->currentWorkspace;
    }
}
