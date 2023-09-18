<?php
namespace Chamilo\Core\Admin\Announcement\Component;

use Chamilo\Core\Admin\Announcement\Manager;
use Chamilo\Core\Admin\Announcement\Storage\DataClass\Publication;
use Chamilo\Core\Admin\Announcement\Table\PublicationTableRenderer;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Breadcrumb\BreadcrumbLessComponentInterface;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonSearchForm;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\RequestTableParameterValuesCompiler;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\ComparisonCondition;
use Chamilo\Libraries\Storage\Query\Condition\ContainsCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Condition\OrCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\StringUtilities;

class BrowserComponent extends Manager implements BreadcrumbLessComponentInterface
{
    public const FILTER_THIS_MONTH = 'month';
    public const FILTER_THIS_WEEK = 'week';
    public const FILTER_TODAY = 'today';

    public const PARAM_FILTER = 'filter';
    public const PARAM_PUBLICATION_TYPE = 'publication_type';

    public const TYPE_ALL = 'all';
    public const TYPE_FOR_ME = 'for_me';
    public const TYPE_FROM_ME = 'from_me';

    private ButtonToolBarRenderer $buttonToolbarRenderer;

    /**
     * @throws \QuickformException
     * @throws \Exception
     */
    public function run()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $publicationsTable = $this->get_publications_html();

        $html = [];

        $html[] = $this->renderHeader();
        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $publicationsTable;
        $html[] = $this->renderFooter();

        return implode(PHP_EOL, $html);
    }

    public function getButtonToolbarRenderer(): ButtonToolBarRenderer
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();
            $urlGenerator = $this->getUrlGenerator();

            $searchUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => self:: ACTION_BROWSE
                ]
            );

            $buttonToolbar = new ButtonToolBar($searchUrl);
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            if ($this->getUser()->getPlatformAdmin())
            {
                $createUrl = $urlGenerator->fromParameters(
                    [
                        Application::PARAM_CONTEXT => Manager::CONTEXT,
                        Manager::PARAM_ACTION => self:: ACTION_CREATE
                    ]
                );

                $commonActions->addButton(
                    new Button(
                        $translator->trans('Publish', [], StringUtilities::LIBRARIES),
                        new FontAwesomeGlyph('share-square'), $createUrl, ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $browseUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => self:: ACTION_BROWSE
                ]
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('ShowAll', [], StringUtilities::LIBRARIES), new FontAwesomeGlyph('folder'),
                    $browseUrl, ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $browseTodayUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => self:: ACTION_BROWSE,
                    self::PARAM_FILTER => self::FILTER_TODAY
                ]
            );

            $toolActions->addButton(
                new Button(
                    $translator->trans('ShowToday', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('calendar-day', [], null, 'fas'), $browseTodayUrl,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $browseThisWeekUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => self:: ACTION_BROWSE,
                    self::PARAM_FILTER => self::FILTER_THIS_WEEK
                ]
            );

            $toolActions->addButton(
                new Button(
                    $translator->trans('ShowThisWeek', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('calendar-week', [], null, 'fas'), $browseThisWeekUrl,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $browseThisMonthUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => self:: ACTION_BROWSE,
                    self::PARAM_FILTER => self::FILTER_THIS_MONTH
                ]
            );

            $toolActions->addButton(
                new Button(
                    $translator->trans('ShowThisMonth', [], StringUtilities::LIBRARIES),
                    new FontAwesomeGlyph('calendar-alt', [], null, 'fas'), $browseThisMonthUrl,
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    public function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return $this->getService(LinkTabsRenderer::class);
    }

    /**
     * @throws \QuickformException
     */
    public function getPublicationCondition(): ?AndCondition
    {
        $conditions = [];

        if ($this->getType() != self::TYPE_ALL)
        {
            $publisher_id = $this->getUser()->getId();

            $conditions[] = new EqualityCondition(
                new PropertyConditionVariable(Publication::class, Publication::PROPERTY_PUBLISHER_ID),
                new StaticConditionVariable($publisher_id)
            );
        }

        $searchCondition = $this->getSearchCondition();

        if ($searchCondition instanceof OrCondition)
        {
            $conditions[] = $searchCondition;
        }

        $filter = $this->getRequest()->query->get(self::PARAM_FILTER);

        switch ($filter)
        {
            case self::FILTER_TODAY :
                $time = mktime(0, 0, 0, (int) date('m', time()), (int) date('d', time()), (int) date('Y', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_MODIFICATION_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
            case self::FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_MODIFICATION_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
            case self::FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, (int) date('m', time()), 1, (int) date('Y', time()));
                $conditions[] = new ComparisonCondition(
                    new PropertyConditionVariable(Publication::class, Publication::PROPERTY_MODIFICATION_DATE),
                    ComparisonCondition::GREATER_THAN_OR_EQUAL, new StaticConditionVariable($time)
                );
                break;
        }

        if ($conditions)
        {
            return new AndCondition($conditions);
        }
        else
        {
            return null;
        }
    }

    public function getPublicationTableRenderer(): PublicationTableRenderer
    {
        return $this->getService(PublicationTableRenderer::class);
    }

    public function getRequestTableParameterValuesCompiler(): RequestTableParameterValuesCompiler
    {
        return $this->getService(RequestTableParameterValuesCompiler::class);
    }

    /**
     * @throws \QuickformException
     */
    public function getSearchCondition(): ?OrCondition
    {
        $query = $this->buttonToolbarRenderer->getSearchForm()->getQuery();

        if (isset($query) && $query != '')
        {
            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_TITLE), $query
            );

            $conditions[] = new ContainsCondition(
                new PropertyConditionVariable(ContentObject::class, ContentObject::PROPERTY_DESCRIPTION), $query
            );

            return new OrCondition($conditions);
        }

        return null;
    }

    public function getType(): string
    {
        $type = $this->getRequest()->query->get(self::PARAM_PUBLICATION_TYPE);

        if (!$type)
        {
            if ($this->getUser()->isPlatformAdmin())
            {
                $type = self::TYPE_ALL;
            }
            else
            {
                $type = self::TYPE_FOR_ME;
            }
        }

        return $type;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function get_publications_html(): string
    {
        $translator = $this->getTranslator();
        $urlGenerator = $this->getUrlGenerator();
        $searchQuery = $this->getButtonToolbarRenderer()->getSearchForm()->getQuery();

        $type = $this->getType();

        $tabs = new TabsCollection();

        if ($this->getUser()->isPlatformAdmin())
        {
            $publicationLinkUrl = $urlGenerator->fromParameters(
                [
                    Application::PARAM_CONTEXT => Manager::CONTEXT,
                    Manager::PARAM_ACTION => self:: ACTION_BROWSE,
                    self::PARAM_PUBLICATION_TYPE => self::TYPE_ALL,
                    ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY => $searchQuery
                ]
            );

            $tabs->add(
                new LinkTab(
                    self::TYPE_ALL, $translator->trans('AllPublications', [], Manager::CONTEXT),
                    new FontAwesomeGlyph('globe', ['fa-lg'], null, 'fas'), $publicationLinkUrl, $type == self::TYPE_ALL
                )
            );
        }

        $publicationLinkUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => self:: ACTION_BROWSE,
                self::PARAM_PUBLICATION_TYPE => self::TYPE_FOR_ME,
                ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY => $searchQuery
            ]
        );

        $tabs->add(
            new LinkTab(
                self::TYPE_FROM_ME, $translator->trans('PublishedForMe', [], Manager::CONTEXT),
                new FontAwesomeGlyph('share-square', ['fa-lg'], null, 'fas'), $publicationLinkUrl,
                $type == self::TYPE_FOR_ME
            )
        );

        $publicationLinkUrl = $urlGenerator->fromParameters(
            [
                Application::PARAM_CONTEXT => Manager::CONTEXT,
                Manager::PARAM_ACTION => self:: ACTION_BROWSE,
                self::PARAM_PUBLICATION_TYPE => self::TYPE_FROM_ME,
                ButtonSearchForm::PARAM_SIMPLE_SEARCH_QUERY => $searchQuery
            ]
        );

        $tabs->add(
            new LinkTab(
                self::TYPE_FROM_ME, $translator->trans('MyPublications', [], Manager::CONTEXT),
                new FontAwesomeGlyph('user', ['fa-lg'], null, 'fas'), $publicationLinkUrl, $type == self::TYPE_FROM_ME
            )
        );

        return $this->getLinkTabsRenderer()->render($tabs, $this->renderPublicationTable());
    }

    /**
     * @throws \TableException
     * @throws \ReflectionException
     * @throws \Chamilo\Libraries\Format\Table\Exception\InvalidPageNumberException
     * @throws \QuickformException
     * @throws \Exception
     */
    protected function renderPublicationTable(): string
    {
        $publicationTableRenderer = $this->getPublicationTableRenderer();

        $totalNumberOfItems = match ($this->getType())
        {
            BrowserComponent::TYPE_FROM_ME, BrowserComponent::TYPE_ALL => $this->getPublicationService()
                ->countPublications($this->getPublicationCondition()),
            default => $this->getPublicationService()->countVisiblePublicationsForUserIdentifier(
                $this->getUser()->getId(), $this->getPublicationCondition()
            ),
        };

        $tableParameterValues = $this->getRequestTableParameterValuesCompiler()->determineParameterValues(
            $publicationTableRenderer->getParameterNames(), $publicationTableRenderer->getDefaultParameterValues(),
            $totalNumberOfItems
        );

        $publications = match ($this->getType())
        {
            BrowserComponent::TYPE_FROM_ME, BrowserComponent::TYPE_ALL => $this->getPublicationService()
                ->findPublicationRecords(
                    $this->getPublicationCondition(), $tableParameterValues->getNumberOfItemsPerPage(),
                    $tableParameterValues->getOffset(),
                    $publicationTableRenderer->determineOrderBy($tableParameterValues)
                ),
            default => $this->getPublicationService()->findVisiblePublicationRecordsForUserIdentifier(
                $this->getUser()->getId(), $this->getPublicationCondition(),
                $tableParameterValues->getNumberOfItemsPerPage(), $tableParameterValues->getOffset(),
                $publicationTableRenderer->determineOrderBy($tableParameterValues)
            ),
        };

        return $publicationTableRenderer->render($tableParameterValues, $publications);
    }
}
