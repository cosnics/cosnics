<?php
namespace Chamilo\Core\Repository\Component;

use Chamilo\Core\Repository\Common\Renderer\ContentObjectRenderer;
use Chamilo\Core\Repository\Filter\FilterData;
use Chamilo\Core\Repository\Filter\FilterDataButtonSearchForm;
use Chamilo\Core\Repository\Filter\Renderer\ConditionFilterRenderer;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Service\TemplateRegistrationConsulter;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\DropdownButton;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\SubButton;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\Condition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\NotCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package repository.lib.repository_manager.component
 */

/**
 * Default repository manager component which allows the user to browse through the different categories and content
 * objects in the repository.
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        if (!$this->getWorkspaceRightsService()->canViewContentObjects($this->getUser(), $this->getWorkspace()))
        {
            throw new NotAllowedException();
        }

        $trail = BreadcrumbTrail::getInstance();

        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $filterData = FilterData::getInstance($this->getWorkspace());
            $filterData->set_filter_property(FilterData::FILTER_TEXT, $query);

            $trail->add(
                new Breadcrumb(
                    $this->get_url(),
                    $this->getTranslator()->trans('SearchResultsFor', [], StringUtilities::LIBRARIES) . ' ' . $query
                )
            );
        }

        $filterData = FilterData::getInstance($this->getWorkspace());
        if (is_null($filterData->get_category()))
        {
            $filterData->set_filter_property(FilterData::FILTER_CATEGORY, 0);
        }

        $html = [];

        $html[] = $this->render_header();
        $html[] = $this->getButtonToolbarRenderer()->render();
        $html[] = $this->getRenderer()->render($this->getContentObjectCondition());
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getAdditionalParameters(array $additionalParameters = []): array
    {
        $additionalParameters[] = self::PARAM_RENDERER;
        $additionalParameters[] = ContentObject::PROPERTY_PARENT_ID;
        $additionalParameters[] = \Chamilo\Configuration\Category\Manager::PARAM_CATEGORY_ID;

        return parent::getAdditionalParameters($additionalParameters);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();
            $stringUtilities = $this->getStringUtilities();
            $buttonToolbar = new ButtonToolBar($this->get_url());

            if ($this->hasFilterType())
            {
                $filter_type = $this->getFilterType();
                $template_registration =
                    $this->getTemplateRegistrationConsulter()->getTemplateRegistrationByIdentifier((int) $filter_type);

                $buttonToolbar->addItem(
                    new Button(
                        $translator->trans(
                            'CreateObjectType',
                            ['TYPE' => $template_registration->get_template()->translate('TypeName')], Manager::CONTEXT
                        ), new FontAwesomeGlyph('plus'), $this->get_url(
                        [
                            Application::PARAM_ACTION => self::ACTION_CREATE_CONTENT_OBJECTS,
                            TypeSelector::PARAM_SELECTION => $filter_type
                        ]
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL, null, ['btn-primary']
                    )
                );
            }

            $buttonToolbar->addItem(
                new Button(
                    $translator->trans('ManageCategories', [], Manager::CONTEXT), new FontAwesomeGlyph('folder'),
                    $this->get_url([Application::PARAM_ACTION => self::ACTION_MANAGE_CATEGORIES]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addItem(
                new Button(
                    $translator->trans('ExportCategory', [], Manager::CONTEXT), new FontAwesomeGlyph('download'),
                    $this->get_url(
                        [
                            Application::PARAM_ACTION => self::ACTION_EXPORT_CONTENT_OBJECTS,
                            FilterData::FILTER_CATEGORY => FilterData::getInstance($this->getWorkspace())
                                ->get_filter_property(
                                    FilterData::FILTER_CATEGORY
                                )
                        ]
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $renderers = ContentObjectRenderer::getAvailableRendererTypes();

            if (count($renderers) > 1)
            {
                switch ($this->getCurrentRendererType())
                {
                    case ContentObjectRenderer::TYPE_TABLE:
                        $glyph = 'table';
                        break;
                    case ContentObjectRenderer::TYPE_GALLERY:
                        $glyph = 'image';
                        break;
                    case ContentObjectRenderer::TYPE_SLIDESHOW:
                        $glyph = 'play-circle';
                        break;
                    default:
                        $glyph = 'table';
                }

                $viewActions = new DropdownButton(
                    $translator->trans($this->getCurrentRendererType() . 'View', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph($glyph)
                );
                $buttonToolbar->addItem($viewActions);

                foreach ($renderers as $renderer)
                {
                    if ($this->getCurrentRendererType() != $renderer)
                    {
                        $action = $this->get_url([self::PARAM_RENDERER => $renderer]);
                        $isActive = false;
                    }
                    else
                    {
                        $action = '';
                        $isActive = true;
                    }

                    $viewActions->addSubButton(
                        new SubButton(
                            $translator->trans(
                                $stringUtilities->createString($renderer)->upperCamelize()->toString() . 'View', [],
                                StringUtilities::LIBRARIES
                            ), null, $action, Button::DISPLAY_LABEL, null, [], null, $isActive
                        )
                    );
                }
            }

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer(
                $buttonToolbar,
                new FilterDataButtonSearchForm($this->get_url(), FilterData::getInstance($this->getWorkspace()))
            );
        }

        return $this->buttonToolbarRenderer;
    }

    public function getContentObjectCondition(): AndCondition
    {
        $conditions = [];
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_STATE),
            new StaticConditionVariable(ContentObject::STATE_NORMAL)
        );

        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_OWNER_ID),
            new StaticConditionVariable($this->getUser()->getId())
        );

        $types = DataManager::get_active_helper_types();

        foreach ($types as $type)
        {
            $conditions[] = new NotCondition(
                new EqualityCondition(
                    new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TYPE),
                    new StaticConditionVariable($type)
                )
            );
        }

        $filter_condition_renderer = ConditionFilterRenderer::factory(
            FilterData::getInstance($this->getWorkspace()), $this->getWorkspace()
        );

        $filter_condition = $filter_condition_renderer->render();

        if ($filter_condition instanceof Condition)
        {
            $conditions[] = $filter_condition;
        }

        return new AndCondition($conditions);
    }

    public function getCurrentRendererType(): string
    {
        $availableRenderers = ContentObjectRenderer::getAvailableRendererTypes();
        $renderer = $this->getRequest()->query->get(self::PARAM_RENDERER);

        if ($renderer && in_array($renderer, $availableRenderers))
        {
            return $renderer;
        }
        else
        {
            return $availableRenderers[0];
        }
    }

    /**
     * @return int
     */
    public function getFilterType()
    {
        return TypeSelector::get_selection();
    }

    protected function getRenderer(): ContentObjectRenderer
    {
        $rendererTypeClassName = 'Chamilo\Core\Repository\Common\Renderer\Type\\' . $this->getCurrentRendererType() .
            'ContentObjectRenderer';

        return $this->getService($rendererTypeClassName);
    }

    /**
     * @return \Chamilo\Core\Repository\Service\TemplateRegistrationConsulter
     */
    public function getTemplateRegistrationConsulter()
    {
        return $this->getService(TemplateRegistrationConsulter::class);
    }

    /**
     * @return bool
     */
    public function hasFilterType()
    {
        $filter_type = $this->getFilterType();

        return isset($filter_type);
    }
}
