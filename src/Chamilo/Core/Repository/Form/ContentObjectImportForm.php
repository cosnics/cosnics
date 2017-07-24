<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Core\Repository\Common\Import\ImportFormParameters;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
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

    /**
     *
     * @var ImportFormParameters
     */
    protected $importFormParameters;

    /**
     * Constructor.
     *
     * @param ImportFormParameters $importFormParameters
     *
     * @throws \Exception
     */
    public function __construct(ImportFormParameters $importFormParameters)
    {
        parent::__construct('import', $importFormParameters->getMethod(), $importFormParameters->getAction());

        $this->importFormParameters = $importFormParameters;

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
        $categorymenu = new ContentObjectCategoryMenu(
            $this->importFormParameters->getWorkspace(),
            $this->get_application()->get_user_id());
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * Builds a form to import an object.
     */
    public function build_basic_form()
    {
        $this->addElement('hidden', self::PROPERTY_TYPE);

        $category_group = array();

        $category_group[] = $this->createElement(
            'select',
            ContentObject::PROPERTY_PARENT_ID,
            Translation::get('CategoryTypeName'),
            $this->get_categories(),
            array('id' => 'parent_id'));

        if (! $this->implementsDropZoneSupport())
        {
            $category_group[] = $this->createElement(
                'image',
                'add_category',
                Theme::getInstance()->getCommonImagePath('Action/Add'),
                array('id' => 'add_category', 'style' => 'display:none'));
        }

        $this->addGroup($category_group, null, Translation::get('CategoryTypeName'));

        if (! $this->implementsDropZoneSupport())
        {
            $group = array();
            $group[] = $this->createElement('static', null, null, '<div id="' . self::NEW_CATEGORY . '">');
            $group[] = $this->createElement('static', null, null, Translation::get('AddNewCategory'));
            $group[] = $this->createElement('text', self::NEW_CATEGORY);
            $group[] = $this->createElement('static', null, null, '</div>');

            $this->addGroup($group);
        }
    }

    public function add_footer()
    {
        $buttons = array();

        $buttons[] = $this->createElement(
            'style_submit_button',
            'import_button',
            Translation::get('Import', null, Utilities::COMMON_LIBRARIES),
            array('id' => 'import_button'),
            null,
            'import');

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement(
            'html',
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->getJavascriptPath('Chamilo\Core\Repository', true) . 'Import.js'));
    }

    public function get_application()
    {
        return $this->importFormParameters->getApplication();
    }

    /**
     *
     * @param ImportFormParameters $importFormParameters
     *
     * @return ContentObjectImportForm
     * @throws \Exception
     */
    public static function factory(ImportFormParameters $importFormParameters)
    {
        $class = Manager::package() . '\Common\Import\\' .
             StringUtilities::getInstance()->createString($importFormParameters->getImportFormType())->upperCamelize() .
             '\\' . (string) StringUtilities::getInstance()->createString($importFormParameters->getImportFormType())->upperCamelize() .
             'ContentObjectImportForm';

        if (! class_exists($class))
        {
            throw new UserException(
                Translation::getInstance()->getTranslation(
                    'UnknownImportType',
                    array('TYPE' => $importFormParameters->getImportFormType())));
        }

        return new $class($importFormParameters);
    }

    /**
     *
     * @return boolean
     */
    protected function implementsDropZoneSupport()
    {
        return false;
    }
}
