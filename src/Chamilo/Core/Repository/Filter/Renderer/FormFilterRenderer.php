<?php
namespace Chamilo\Core\Repository\Filter\Renderer;

use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\FilterRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Menu\ContentObjectCategoryMenu;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Core\Repository\UserView\Storage\DataClass\UserView;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Menu\OptionsMenuRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Repository\Filter\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class FormFilterRenderer extends FilterRenderer
{

    /**
     *
     * @var int
     */
    private $user_id;

    /**
     *
     * @var string[]
     */
    private $content_object_types;

    /**
     *
     * @var \Chamilo\Libraries\Format\Form\FormValidator
     */
    private $form_validator;

    /**
     *
     * @var \HTML_QuickForm_Renderer_Default
     */
    private $renderer;

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     * @param int $user_id
     * @param string[] $content_object_types
     * @param string $url ;
     */
    public function __construct(
        FilterData $filter_data, WorkspaceInterface $workspace, $user_id, $content_object_types, $url
    )
    {
        parent::__construct($filter_data, $workspace);

        $this->user_id = $user_id;
        $this->content_object_types = $content_object_types;
        $this->form_validator = new FormValidator('advanced_filter_form', FormValidator::FORM_METHOD_POST, $url);
        $this->renderer = clone $this->form_validator->defaultRenderer();
    }

    public function render()
    {
        $this->build();
        $this->add_footer();
        $this->set_defaults();

        $this->form_validator->accept($this->renderer);

        return $this->renderer->toHtml();
    }

    function add_footer()
    {
        $this->form_validator->addElement(
            'html', ResourceManager::getInstance()->getResourceHtml(
            Path::getInstance()->getJavascriptPath(Manager::context(), true) . 'Search.js'
        )
        );
        $this->form_validator->addElement(
            'style_button', 'submit', Translation::get('Search', array(), Utilities::COMMON_LIBRARIES), null, null,
            new FontAwesomeGlyph('search')
        );
    }

    public function build()
    {
        $this->renderer->setFormTemplate('<form {attributes}>{content}</form>');
        $this->renderer->setElementTemplate(
            '<div class="form-group form-group-sm"><label>{label}</label>{element}</div>'
        );

        // title
        $this->form_validator->addElement(
            'text', FilterData::FILTER_TEXT, Translation::get('TextSearch'), 'class="form-control input-sm"'
        );

        // category

        $categories = $this->get_categories();

        if (count($categories) > 2)
        {
            $this->form_validator->addElement(
                'select', ContentObject::PROPERTY_PARENT_ID, Translation::get('Category'), $categories,
                'class="form-control input-sm"'
            );
            $this->form_validator->addElement(
                'checkbox', FilterData::FILTER_CATEGORY_RECURSIVE, null, Translation::get('SearchRecursive')
            );

            $this->renderer->setElementTemplate('{element}', FilterData::FILTER_CATEGORY_RECURSIVE);
        }

        // creation date
        $creationGroup = array();

        $creationGroup[] = $this->form_validator->createElement(
            'text', FilterData::FILTER_FROM_DATE, Translation::get('DateFrom'),
            'id="creation_date_from" class="form-control input-sm input-date"'
        );

        $creationToName = FilterData::FILTER_CREATION_DATE . '[' . FilterData::FILTER_TO_DATE . ']';

        $creationGroup[] = $this->form_validator->createElement(
            'text', FilterData::FILTER_TO_DATE, Translation::get('DateTo'),
            'id="creation_date_to" class="form-control input-sm input-date"'
        );

        $this->form_validator->addGroup(
            $creationGroup, FilterData::FILTER_CREATION_DATE, Translation::get('CreationDate'), null
        );

        $this->renderer->setElementTemplate(
            '<div class="form-group form-inline"><label>{label}</label><div>{element}</div></div>',
            FilterData::FILTER_CREATION_DATE
        );

        $this->renderer->setGroupElementTemplate(
            '<div class="input-group input-group-date">
        <span class="input-group-addon input-group-filter-date input-sm">{label}</span>{element}</div>',
            FilterData::FILTER_CREATION_DATE
        );

        // modification date
        $modificationGroup = array();

        $modificationGroup[] = $this->form_validator->createElement(
            'text', FilterData::FILTER_FROM_DATE, Translation::get('DateFrom'),
            'id="modification_date_from" class="form-control input-sm input-date"'
        );

        $modificationGroup[] = $this->form_validator->createElement(
            'text', FilterData::FILTER_TO_DATE, Translation::get('DateTo'),
            'id="modification_date_to" class="form-control input-sm input-date"'
        );

        $this->form_validator->addGroup(
            $modificationGroup, FilterData::FILTER_MODIFICATION_DATE, Translation::get('ModificationDate'), null
        );

        $this->renderer->setElementTemplate(
            '<div class="form-group form-inline"><label>{label}</label><div>{element}</div></div>',
            FilterData::FILTER_MODIFICATION_DATE
        );

        $this->renderer->setGroupElementTemplate(
            '<div class="input-group input-group-date">
        <span class="input-group-addon input-group-filter-date input-sm">{label}</span>{element}</div>',
            FilterData::FILTER_MODIFICATION_DATE
        );

        // type

        $typeSelectorFactory = new TypeSelectorFactory($this->get_content_object_types());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        $select = $this->form_validator->addElement(
            'select', FilterData::FILTER_TYPE, Translation::get('ContentObjectType'), array(),
            'class="form-control input-sm"'
        );

        foreach ($type_selector->as_tree() as $key => $type)
        {
            $select->addOption($type, $key);
        }

        // User view
        $user_views = $this->get_user_views();

        if (count($user_views) > 1)
        {
            $select = $this->form_validator->addElement(
                'select', FilterData::FILTER_USER_VIEW, Translation::get('UserView'), $user_views,
                'class="form-control input-sm"'
            );
        }
    }

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     * @param int $user_id
     * @param string[] $content_object_types
     * @param string $url ;
     *
     * @return \core\repository\filter\renderer\FormFilterRenderer
     */
    public static function factory(
        FilterData $filter_data, WorkspaceInterface $workspace, $user_id, $content_object_types, $url
    )
    {
        $class_name = $filter_data->get_context() . '\Filter\Renderer\FormFilterRenderer';

        return new $class_name($filter_data, $workspace, $user_id, $content_object_types, $url);
    }

    /*
     * (non-PHPdoc) @see \core\repository\FilterRenderer::render()
     */

    /**
     *
     * @return string[]
     */
    private function get_categories()
    {
        $menu = new ContentObjectCategoryMenu($this->get_workspace());
        $renderer = new OptionsMenuRenderer();
        $menu->render($renderer, 'sitemap');

        return array(- 1 => '-- ' . Translation::get('SelectACategory') . ' --') + $renderer->toArray();
    }

    /**
     *
     * @return string[]
     */
    public function get_content_object_types()
    {
        return $this->content_object_types;
    }

    /**
     *
     * @param string[] $content_object_types
     */
    public function set_content_object_types($content_object_types)
    {
        $this->content_object_types = $content_object_types;
    }

    /**
     *
     * @return \Chamilo\Libraries\Format\Form\FormValidator
     */
    public function get_form_validitor()
    {
        return $this->form_validator;
    }

    /**
     *
     * @return HTML_QuickForm_Renderer_Default
     */
    public function get_renderer()
    {
        return $this->renderer;
    }

    /**
     *
     * @return int
     */
    public function get_user_id()
    {
        return $this->user_id;
    }

    /**
     *
     * @param int $user_id
     */
    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     *
     * @return string[]
     */
    private function get_user_views()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserView::class, UserView::PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id())
        );
        $parameters = new DataClassRetrievesParameters($condition);
        $user_views = DataManager::retrieves(UserView::class, $parameters);

        $user_view_options = array();
        $user_view_options[0] = '-- ' . Translation::get('SelectAView') . ' --';

        while ($user_view = $user_views->next_result())
        {
            $user_view_options[$user_view->get_id()] = $user_view->get_name();
        }

        return $user_view_options;
    }

    public function set_defaults($defaults = array())
    {
        $filter_data = $this->get_filter_data();

        $defaults[FilterData::FILTER_TEXT] = $filter_data->get_filter_property(FilterData::FILTER_TEXT);
        $defaults[FilterData::FILTER_CATEGORY] = $filter_data->get_filter_property(FilterData::FILTER_CATEGORY);
        $defaults[FilterData::FILTER_CATEGORY_RECURSIVE] = $filter_data->get_filter_property(
            FilterData::FILTER_CATEGORY_RECURSIVE
        );
        $defaults[FilterData::FILTER_TYPE] = $filter_data->get_filter_property(FilterData::FILTER_TYPE);
        $defaults[FilterData::FILTER_USER_VIEW] = $filter_data->get_filter_property(FilterData::FILTER_USER_VIEW);

        $creation_date = $filter_data->get_filter_property(FilterData::FILTER_CREATION_DATE);
        $modification_date = $filter_data->get_filter_property(FilterData::FILTER_MODIFICATION_DATE);

        $defaults[FilterData::FILTER_CREATION_DATE][FilterData::FILTER_FROM_DATE] =
            $creation_date[FilterData::FILTER_FROM_DATE];
        $defaults[FilterData::FILTER_CREATION_DATE][FilterData::FILTER_TO_DATE] =
            $creation_date[FilterData::FILTER_TO_DATE];
        $defaults[FilterData::FILTER_MODIFICATION_DATE][FilterData::FILTER_FROM_DATE] =
            $modification_date[FilterData::FILTER_FROM_DATE];
        $defaults[FilterData::FILTER_MODIFICATION_DATE][FilterData::FILTER_TO_DATE] =
            $modification_date[FilterData::FILTER_TO_DATE];

        $this->form_validator->setDefaults($defaults);
    }
}