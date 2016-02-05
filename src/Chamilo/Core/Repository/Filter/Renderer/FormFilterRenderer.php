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
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
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
     * @var \libraries\format\FormValidator
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
     * @param string $url;
     */
    public function __construct(FilterData $filter_data, WorkspaceInterface $workspace, $user_id, $content_object_types,
        $url)
    {
        parent :: __construct($filter_data, $workspace);

        $this->user_id = $user_id;
        $this->content_object_types = $content_object_types;
        $this->form_validator = new FormValidator('advanced_filter_form', 'post', $url);
        $this->renderer = clone $this->form_validator->defaultRenderer();
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

    /*
     * (non-PHPdoc) @see \core\repository\FilterRenderer::render()
     */
    public function render()
    {
        $this->build();
        $this->add_footer();
        $this->set_defaults();

        $this->form_validator->accept($this->renderer);
        return $this->renderer->toHtml();
    }

    /**
     *
     * @return \libraries\format\FormValidator
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

    public function build()
    {
        $this->renderer->setFormTemplate(
            '<form {attributes}><div class="advanced_filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row">{element}</div>');

        // title
        $this->form_validator->addElement('category', Translation :: get('TextSearch'));
        $this->form_validator->addElement('text', FilterData :: FILTER_TEXT, null, 'class="full"');
        $this->form_validator->addElement('category');

        // category
        $this->form_validator->addElement('category', Translation :: get('Category'));
        $this->form_validator->addElement(
            'select',
            ContentObject :: PROPERTY_PARENT_ID,
            null,
            $this->get_categories(),
            'class="full"');
        $this->form_validator->addElement(
            'checkbox',
            FilterData :: FILTER_CATEGORY_RECURSIVE,
            null,
            Translation :: get('SearchRecursive'),
            'style="vertical-align: middle;"');
        $this->form_validator->addElement('category');

        // creation date
        $this->form_validator->addElement('category', Translation :: get('CreationDate'));
        $creation_date = array();
        $creation_date[] = $this->form_validator->createElement(
            'static',
            '',
            '',
            '<span style="display:inline-block; margin-right: 2px;">' . Translation :: get('From') . '</span>');
        $creation_date[] = $this->form_validator->createElement(
            'text',
            FilterData :: FILTER_FROM_DATE,
            Translation :: get('From'),
            'id="creation_date_from" style="width:60px;"');
        $creation_date[] = $this->form_validator->createElement(
            'static',
            '',
            '',
            '<span style="display:inline-block; margin-left: 2px; margin-right: 2px;">' . Translation :: get('To') .
                 '</span>');
        $creation_date[] = $this->form_validator->createElement(
            'text',
            FilterData :: FILTER_TO_DATE,
            Translation :: get('To'),
            'id="creation_date_to" style="width:60px;"');
        $this->form_validator->addGroup($creation_date, FilterData :: FILTER_CREATION_DATE);
        $this->form_validator->addElement('category');

        $this->renderer->setGroupElementTemplate('{element}', FilterData :: FILTER_CREATION_DATE);

        // modification date
        $this->form_validator->addElement('category', Translation :: get('ModificationDate'));
        $modification_date = array();
        $modification_date[] = $this->form_validator->createElement(
            'static',
            '',
            '',
            '<span style="display:inline-block; margin-right: 2px;">' . Translation :: get('From') . '</span>');
        $modification_date[] = $this->form_validator->createElement(
            'text',
            FilterData :: FILTER_FROM_DATE,
            Translation :: get('From'),
            'id="modification_date_from" style="width:60px;"');
        $modification_date[] = $this->form_validator->createElement(
            'static',
            '',
            '',
            '<span style="display:inline-block; margin-left: 2px; margin-right: 2px;">' . Translation :: get('To') .
                 '</span>');
        $modification_date[] = $this->form_validator->createElement(
            'text',
            FilterData :: FILTER_TO_DATE,
            Translation :: get('To'),
            'id="modification_date_to" style="width:60px;"');
        $this->form_validator->addGroup($modification_date, FilterData :: FILTER_MODIFICATION_DATE);
        $this->form_validator->addElement('category');

        $this->renderer->setGroupElementTemplate('{element}', FilterData :: FILTER_MODIFICATION_DATE);

        // type
        $this->form_validator->addElement('category', Translation :: get('ContentObjectType'));

        $typeSelectorFactory = new TypeSelectorFactory($this->get_content_object_types());
        $type_selector = $typeSelectorFactory->getTypeSelector();

        $select = $this->form_validator->addElement('select', FilterData :: FILTER_TYPE, null, array(), 'class="full"');

        foreach ($type_selector->as_tree() as $key => $type)
        {
            $select->addOption($type, $key);
        }

        $this->form_validator->addElement('category');

        // User view
        $user_views = $this->get_user_views();

        if (count($user_views) > 0)
        {
            $this->form_validator->addElement('category', Translation :: get('UserView'));
            $select = $this->form_validator->addElement(
                'select',
                FilterData :: FILTER_USER_VIEW,
                null,
                $user_views,
                'class="full"');
            $this->form_validator->addElement('category');
        }
    }

    function add_footer()
    {
        $this->form_validator->addElement(
            'html',
            ResourceManager :: get_instance()->get_resource_html(
                Path :: getInstance()->getJavascriptPath(Manager :: context(), true) . 'Search.js'));
        $this->form_validator->addElement(
            'style_button',
            'submit',
            Translation :: get('Search', array(), Utilities :: COMMON_LIBRARIES),
            null,
            null,
            'search');
    }

    /**
     *
     * @return string[]
     */
    private function get_categories()
    {
        $menu = new ContentObjectCategoryMenu($this->get_workspace());
        $renderer = new OptionsMenuRenderer();
        $menu->render($renderer, 'sitemap');

        return array(- 1 => '-- ' . Translation :: get('SelectACategory') . ' --') + $renderer->toArray();
    }

    /**
     *
     * @return string[]
     */
    private function get_user_views()
    {
        $condition = new EqualityCondition(
            new PropertyConditionVariable(UserView :: class_name(), UserView :: PROPERTY_USER_ID),
            new StaticConditionVariable($this->get_user_id()));
        $parameters = new DataClassRetrievesParameters($condition);
        $user_views = DataManager :: retrieves(UserView :: class_name(), $parameters);

        $user_view_options = array();
        $user_view_options[0] = '-- ' . Translation :: get('SelectAView') . ' --';

        while ($user_view = $user_views->next_result())
        {
            $user_view_options[$user_view->get_id()] = $user_view->get_name();
        }

        return $user_view_options;
    }

    public function set_defaults($defaults = array())
    {
        $filter_data = $this->get_filter_data();

        $defaults[FilterData :: FILTER_TEXT] = $filter_data->get_filter_property(FilterData :: FILTER_TEXT);
        $defaults[FilterData :: FILTER_CATEGORY] = $filter_data->get_filter_property(FilterData :: FILTER_CATEGORY);
        $defaults[FilterData :: FILTER_CATEGORY_RECURSIVE] = $filter_data->get_filter_property(
            FilterData :: FILTER_CATEGORY_RECURSIVE);
        $defaults[FilterData :: FILTER_TYPE] = $filter_data->get_filter_property(FilterData :: FILTER_TYPE);
        $defaults[FilterData :: FILTER_USER_VIEW] = $filter_data->get_filter_property(FilterData :: FILTER_USER_VIEW);

        $creation_date = $filter_data->get_filter_property(FilterData :: FILTER_CREATION_DATE);
        $modification_date = $filter_data->get_filter_property(FilterData :: FILTER_MODIFICATION_DATE);

        $defaults[FilterData :: FILTER_CREATION_DATE][FilterData :: FILTER_FROM_DATE] = $creation_date[FilterData :: FILTER_FROM_DATE];
        $defaults[FilterData :: FILTER_CREATION_DATE][FilterData :: FILTER_TO_DATE] = $creation_date[FilterData :: FILTER_TO_DATE];
        $defaults[FilterData :: FILTER_MODIFICATION_DATE][FilterData :: FILTER_FROM_DATE] = $modification_date[FilterData :: FILTER_FROM_DATE];
        $defaults[FilterData :: FILTER_MODIFICATION_DATE][FilterData :: FILTER_TO_DATE] = $modification_date[FilterData :: FILTER_TO_DATE];

        $this->form_validator->setDefaults($defaults);
    }

    /**
     *
     * @param \core\repository\filter\FilterData $filter_data
     * @param int $user_id
     * @param string[] $content_object_types
     * @param string $url;
     * @return \core\repository\filter\renderer\FormFilterRenderer
     */
    public static function factory(FilterData $filter_data, WorkspaceInterface $workspace, $user_id,
        $content_object_types, $url)
    {
        $class_name = $filter_data->get_context() . '\Filter\Renderer\FormFilterRenderer';
        return new $class_name($filter_data, $workspace, $user_id, $content_object_types, $url);
    }
}