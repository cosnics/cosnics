<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Storage\DataManager;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\DataClassParameters;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use MediawikiParser;
use MediawikiParserContext;

/**
 * @package Chamilo\Core\Repository\ContentObject\Wiki\Display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    public const ACTION_ACCESS_DETAILS = 'AccessDetails';
    public const ACTION_BROWSE_WIKI = 'WikiBrowser';
    public const ACTION_COMPARE = 'Comparer';
    public const ACTION_CREATE_PAGE = 'WikiPageCreator';
    public const ACTION_DISCUSS = 'WikiDiscuss';
    public const ACTION_HISTORY = 'WikiHistory';
    public const ACTION_PAGE_STATISTICS = 'PageStatistics';
    public const ACTION_SET_AS_HOMEPAGE = 'WikiHomepageSetter';
    public const ACTION_STATISTICS = 'Statistics';
    public const ACTION_VERSION_DELETE = 'VersionDeleter';
    public const ACTION_VERSION_REVERT = 'VersionReverter';
    public const ACTION_VIEW_WIKI = 'Viewer';
    public const ACTION_VIEW_WIKI_PAGE = 'WikiItemViewer';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_VIEW_WIKI;

    public const PARAM_WIKI_ID = 'wiki_id';
    public const PARAM_WIKI_PAGE_ID = 'wiki_page_id';
    public const PARAM_WIKI_VERSION_ID = 'wiki_version_id';

    /**
     * @var \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    private $buttonToolBarRenderer;

    /**
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar $buttonToolBar
     * @param \Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage $complex_wiki_page
     */
    public function addDisplayActions(ButtonToolBar $buttonToolBar, ComplexWikiPage $complex_wiki_page = null)
    {
        $displayAction = $this->get_action();

        if ($displayAction != self::ACTION_CREATE_PAGE && $complex_wiki_page)
        {
            $buttonGroup = new ButtonGroup([], ['btn-group-vertical']);
            $buttonToolBar->addButtonGroup($buttonGroup);

            $classes = ($displayAction == self::ACTION_VIEW_WIKI_PAGE ? ['btn-primary', 'disabled'] : []);
            $read_url = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                ]
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('WikiArticle'), new FontAwesomeGlyph('desktop', [], null, 'fas'), $read_url,
                    Button::DISPLAY_ICON_AND_LABEL, null, $classes
                )
            );

            $classes = ($displayAction == self::ACTION_DISCUSS ? ['btn-primary', 'disabled'] : []);
            $discuss_url = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_DISCUSS,
                    'wiki_publication' => $this->getRequest()->query->get('wiki_publication'),
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                ]
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('WikiDiscuss'), new FontAwesomeGlyph('comment', [], null, 'fas'), $discuss_url,
                    Button::DISPLAY_ICON_AND_LABEL, null, $classes
                )
            );

            $classes = ($displayAction == self::ACTION_PAGE_STATISTICS ? ['btn-primary', 'disabled'] : []);
            $statistics_url = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_PAGE_STATISTICS,
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                ]
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('WikiStatistics'), new FontAwesomeGlyph(
                    'chart-bar', [], null, 'fas'
                ), $statistics_url, Button::DISPLAY_ICON_AND_LABEL, null, $classes
                )
            );

            $buttonGroup = new ButtonGroup([], ['btn-group-vertical']);
            $buttonToolBar->addButtonGroup($buttonGroup);

            $classes =
                ($displayAction == self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM ? ['btn-primary', 'disabled'] : []);
            $edit_url = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                ]
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('WikiEdit'), new FontAwesomeGlyph(
                    'edit', [], null, 'fas'
                ), $edit_url, Button::DISPLAY_ICON_AND_LABEL, null, $classes
                )
            );

            $classes = (($displayAction == self::ACTION_HISTORY) || ($displayAction == self::ACTION_VIEW_WIKI_PAGE &&
                $this->getRequest()->query->get(self::PARAM_WIKI_VERSION_ID)) ? ['btn-primary disabled'] : []);
            $history_url = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_HISTORY,
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                ]
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('WikiHistory'), new FontAwesomeGlyph(
                    'history', [], null, 'fas'
                ), $history_url, Button::DISPLAY_ICON_AND_LABEL, null, $classes
                )
            );

            $classes =
                ($displayAction == self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM ? ['btn-primary disabled'] : []);
            $delete_url = $this->get_url(
                [
                    self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                ]
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('WikiDelete'), new FontAwesomeGlyph(
                    'times', [], null, 'fas'
                ), $delete_url, Button::DISPLAY_ICON_AND_LABEL,
                    Translation::get('ConfirmChosenAction', [], StringUtilities::LIBRARIES), $classes
                )
            );
        }
    }

    public function getButtonToolBarRenderer(ComplexWikiPage $complexWikiPage = null)
    {
        if (!isset($this->buttonToolBarRenderer))
        {
            $buttonToolBar = new ButtonToolBar($this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_WIKI]));
            $buttonToolBar->addClass('btn-action-toolbar-vertical');

            $buttonGroup = new ButtonGroup([], ['btn-group-vertical']);
            $buttonToolBar->addButtonGroup($buttonGroup);

            $buttonGroup->addButton(
                new Button(
                    Translation::get('AddWikiPage'), new FontAwesomeGlyph('plus'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE_PAGE]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $this->addDisplayActions($buttonToolBar, $complexWikiPage);

            $buttonGroup = new ButtonGroup([], ['btn-group-vertical']);
            $buttonToolBar->addButtonGroup($buttonGroup);

            $buttonGroup->addButton(
                new Button(
                    Translation::get('MainPage'), new FontAwesomeGlyph('home'), $this->get_url(
                    [
                        self::PARAM_ACTION => self::ACTION_VIEW_WIKI,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => null
                    ]
                ), Button::DISPLAY_ICON_AND_LABEL
                )
            );

            $buttonGroup->addButton(
                new Button(
                    Translation::get('Contents'), new FontAwesomeGlyph('folder'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_BROWSE_WIKI]),
                    ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );
            $buttonGroup->addButton(
                new Button(
                    Translation::get('Statistics'), new FontAwesomeGlyph('chart-bar'),
                    $this->get_url([self::PARAM_ACTION => self::ACTION_STATISTICS]), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $this->buttonToolBarRenderer = new ButtonToolBarRenderer($buttonToolBar);
        }

        return $this->buttonToolBarRenderer;
    }

    public function get_breadcrumbtrail()
    {
        $trail = $this->getBreadcrumbTrail();
        $trail->add(
            new Breadcrumb(
                $this->get_url([self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT]),
                $this->get_root_content_object()->get_title()
            )
        );
        switch ($this->getRequest()->query->get(self::PARAM_ACTION))
        {
            case self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT :
                break;
            case self::ACTION_CREATE_PAGE :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url([self::PARAM_ACTION => self::ACTION_CREATE_PAGE]),
                        Translation::get('CreateWikiPage')
                    )
                );
                break;
            case self::ACTION_UPDATE_CONTENT_OBJECT :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url([self::PARAM_ACTION => self::ACTION_UPDATE_CONTENT_OBJECT]),
                        Translation::get('Edit', null, StringUtilities::LIBRARIES)
                    )
                );
                break;
            case self::ACTION_PAGE_STATISTICS :
                $complex_wiki_page_id =
                    $this->getRequest()->query->get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
                $complex_wiki_page = DataManager::retrieve_by_id(
                    ComplexContentObjectItem::class, $complex_wiki_page_id
                );
                $wiki_page = $complex_wiki_page->get_ref_object();
                $trail->add(new Breadcrumb(null, $wiki_page->get_title()));
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_PAGE_STATISTICS,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->getRequest()->query->get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            ]
                        ), Translation::get('Reporting')
                    )
                );
                break;
            case self::ACTION_ACCESS_DETAILS :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_ACCESS_DETAILS,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->getRequest()->query->get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            ]
                        ), Translation::get('Reporting')
                    )
                );
                break;
            case self::ACTION_HISTORY :
            case self::ACTION_VIEW_WIKI_PAGE :
            case self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->getRequest()->query->get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            ]
                        ), $this->get_content_object_from_complex_id(
                        $this->getRequest()->query->get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)
                    )->get_title()
                    )
                );
                break;
            case self::ACTION_DISCUSS :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            [
                                self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->getRequest()->query->get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            ]
                        ), $this->get_content_object_from_complex_id(
                        $this->getRequest()->query->get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)
                    )->get_title()
                    )
                );
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            [
                                self::PARAM_ACTION => $this->getRequest()->query->get(self::PARAM_ACTION),
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->getRequest()->query->get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            ]
                        ), Translation::get('WikiDiscuss')
                    )
                );
                break;
        }

        return $trail;
    }

    private function get_content_object_from_complex_id($complex_id)
    {
        $complex_content_object_item = DataManager::retrieve_by_id(
            ComplexContentObjectItem::class, $complex_id
        );

        return DataManager::retrieve_by_id(
            ContentObject::class, $complex_content_object_item->get_ref()
        );
    }

    public function get_publication()
    {
        return $this->get_parent()->get_publication();
    }

    public static function get_wiki_homepage($wiki_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class, ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($wiki_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ComplexWikiPage::class, ComplexWikiPage::PROPERTY_IS_HOMEPAGE),
            new StaticConditionVariable(1)
        );
        $parameters = new DataClassParameters(condition: new AndCondition($conditions), count: 1, offset: 0);
        $complex_wiki_homepage = DataManager::retrieve_complex_content_object_items(
            ComplexWikiPage::class, $parameters
        );

        return $complex_wiki_homepage->current();
    }

    public static function is_wiki_locked($wiki_id)
    {
        $wiki = DataManager::retrieve_by_id(ContentObject::class, $wiki_id);

        return $wiki->get_locked() == 1;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage|null $complexWikiPage
     *
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     */
    public function renderMenu(ComplexWikiPage $complexWikiPage = null)
    {
        $html = [];

        $wikiGlyph = $this->get_root_content_object()->getGlyph(
            IdentGlyph::SIZE_BIG, true, ['fa-6x']
        );

        $html[] = '<div class="panel panel-default">';
        $html[] = '<div class="panel-body text-center">';
        $html[] = $wikiGlyph->render();
        $html[] = '</div>';
        $html[] = '</div>';

        $links = $this->get_root_content_object()->get_links();

        if (!empty($links))
        {
            $object_renderer = new ContentObjectResourceRenderer($links);
            $links = $object_renderer->run();
            $parser = new MediawikiParser(
                new MediawikiParserContext($this->get_root_content_object(), '', $links, $this->get_parameters())
            );

            $html[] = '<div class="panel panel-default">';
            $html[] = '<div class="panel-body">';
            $html[] = $parser->parse();
            $html[] = '</div>';
            $html[] = '</div>';
        }

        $html[] = $this->getButtonToolBarRenderer($complexWikiPage)->render();

        return implode(PHP_EOL, $html);
    }

    /**
     * @return string
     */
    public function render_footer(): string
    {
        $html = [];

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    public function render_header(string $pageTitle = '', ?ComplexWikiPage $complex_wiki_page = null): string
    {
        $html = [];

        $html[] = parent::render_header($pageTitle);

        $html[] = '<div class="row">';

        $html[] = '<div class="col-md-2">';
        $html[] = $this->renderMenu($complex_wiki_page);
        $html[] = '</div>';

        $html[] = '<div class="col-md-10">';

        return implode(PHP_EOL, $html);
    }
}
