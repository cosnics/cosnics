<?php
namespace Chamilo\Core\Repository\Form;

use Chamilo\Core\Repository\Common\Import\ImportFormParameters;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib
 */

/**
 * A form to import a ContentObject.
 */
abstract class ContentObjectImportForm extends FormValidator
{
    public const IMPORT_FILE_NAME = 'content_object_file';

    public const NEW_CATEGORY = 'new_category';

    public const PROPERTY_TYPE = 'type';

    /**
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

    public function add_footer()
    {
        $buttons = [];

        $buttons[] = $this->createElement(
            'style_submit_button', 'import_button', Translation::get('Import', null, StringUtilities::LIBRARIES),
            ['id' => 'import_button'], null, new FontAwesomeGlyph('import')
        );

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->getJavascriptPath('Chamilo\Core\Repository') . 'Import.js'
        )
        );
    }

    /**
     * Builds a form to import an object.
     */
    public function build_basic_form()
    {
        $this->addElement('hidden', self::PROPERTY_TYPE);

        $this->addElement(
            'select', ContentObject::PROPERTY_PARENT_ID, Translation::get('CategoryTypeName'), $this->get_categories(),
            ['class' => 'form-control', 'id' => 'parent_id']
        );

        if (!$this->implementsDropZoneSupport())
        {
            $category_group = [];

            $category_group[] = $this->createElement('static', null, null, '<div class="input-group">');

            $category_group[] = $this->createElement(
                'static', null, null,
                '<span class="input-group-addon">' . Translation::get('AddNewCategory') . '</span>'
            );

            $category_group[] = $this->createElement('text', self::NEW_CATEGORY, null, ['class' => 'form-control']);
            $category_group[] = $this->createElement('static', null, null, '</div>');

            $this->addGroup($category_group, 'category_form_group', null, ' ', false);
        }
    }

    /**
     * @param ImportFormParameters $importFormParameters
     *
     * @return ContentObjectImportForm
     * @throws \Exception
     */
    public static function factory(ImportFormParameters $importFormParameters)
    {
        $class = Manager::CONTEXT . '\Common\Import\\' .
            StringUtilities::getInstance()->createString($importFormParameters->getImportFormType())->upperCamelize() .
            '\\' . (string) StringUtilities::getInstance()->createString($importFormParameters->getImportFormType())
                ->upperCamelize() . 'ContentObjectImportForm';

        if (!class_exists($class))
        {
            throw new UserException(
                Translation::getInstance()->getTranslation(
                    'UnknownImportType', ['TYPE' => $importFormParameters->getImportFormType()]
                )
            );
        }

        return new $class($importFormParameters);
    }

    public function get_application()
    {
        return $this->importFormParameters->getApplication();
    }

    /**
     * Gets the categories defined in the user's repository.
     *
     * @return array The categories.
     */
    public function get_categories()
    {
        $categorymenu = new ContentObjectCategoryMenu(
            $this->importFormParameters->getWorkspace(), $this->get_application()->get_user_id()
        );
        $renderer = new OptionsMenuRenderer();
        $categorymenu->render($renderer, 'sitemap');

        return $renderer->toArray();
    }

    /**
     * @return bool
     */
    protected function implementsDropZoneSupport()
    {
        return false;
    }
}
