<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: content_object_import_form.class.php 204 2009-11-13 12:51:30Z kariboe $
 *
 * @package repository.lib
 */

/**
 * A form to import a ContentObject.
 */
abstract class ContentObjectImportForm extends FormValidator
{
    const PROPERTY_TYPE = 'type';
    const NEW_CATEGORY = 'new_category';
    const IMPORT_FILE_NAME = 'content_object_file';

    private $workspace;

    public $application;

    private $show_categories;

    /**
     * Constructor.
     *
     * @param $form_name string The name to use in the form tag.
     * @param $method string The method to use ('post' or 'get').
     * @param $action string The URL to which the form should be submitted.
     */
    public function __construct(WorkspaceInterface $workspace, $application, $method = 'post', $action = null,
        $show_categories = true)
    {
        parent :: __construct('import', $method, $action);

        $this->workspace = $workspace;
        $this->application = $application;
        $this->show_categories = $show_categories;

        $this->build_basic_form();
        $this->add_footer();
        $this->setDefaults();
    }

    /**
     * Gets the categories defined in the user's repository.
     *
     * @return array The categories.
     */
    public function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu($this->workspace, $this->get_application()->get_user_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Builds a form to import an object.
     */
    public function build_basic_form()
    {
        $this->addElement('hidden', self :: PROPERTY_TYPE);

        $category_group = array();

        $category_group[] = $this->createElement(
            'select',
            ContentObject :: PROPERTY_PARENT_ID,
            Translation :: get('CategoryTypeName'),
            $this->get_categories(),
            array('id' => 'parent_id'));

        $category_group[] = $this->createElement(
            'image',
            'add_category',
            Theme :: getInstance()->getCommonImagePath('Action/Add'),
            array('id' => 'add_category', 'style' => 'display:none'));

        $this->addGroup($category_group, null, Translation :: get('CategoryTypeName'));

        $group = array();
        $group[] = $this->createElement('static', null, null, '<div id="' . self :: NEW_CATEGORY . '">');
        $group[] = $this->createElement('static', null, null, Translation :: get('AddNewCategory'));
        $group[] = $this->createElement('text', self :: NEW_CATEGORY);
        $group[] = $this->createElement('static', null, null, '</div>');

        $this->addGroup($group);
    }

    public function add_footer()
    {
        $buttons = array();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'import_button',
            Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES),
            array('id' => 'import_button'),
            null,
            'import');

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'Import.js'));
    }

    public function get_application()
    {
        return $this->application;
    }

    /**
     *
     * @param unknown $type
     * @param WorkspaceInterface $workspace
     * @param unknown $application
     * @param unknown $method
     * @param unknown $action
     * @param string $show_categories
     * @throws \Exception
     * @return \Chamilo\Core\Repository\Form\ContentObjectImportForm
     */
    public static function factory($type, WorkspaceInterface $workspace, $application, $method, $action = null,
        $show_categories = true)
    {
        $class = Manager :: package() . '\Common\Import\\' .
             StringUtilities :: getInstance()->createString($type)->upperCamelize() . '\\' .
             (string) StringUtilities :: getInstance()->createString($type)->upperCamelize() . 'ContentObjectImportForm';

        if (! class_exists($class))
        {
            throw new \Exception(Translation :: get('UnknownImportType', array('TYPE' => $type)));
        }

        return new $class($workspace, $application, $method, $action, $show_categories);
    }
}
