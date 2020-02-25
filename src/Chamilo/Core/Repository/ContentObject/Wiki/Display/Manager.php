<?php
namespace Chamilo\Core\Repository\ContentObject\Wiki\Display;

use Chamilo\Core\Repository\Common\ContentObjectResourceRenderer;
use Chamilo\Core\Repository\ContentObject\WikiPage\Storage\DataClass\ComplexWikiPage;
use Chamilo\Core\Repository\Storage\DataClass\ComplexContentObjectItem;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Structure\ActionBar\ActionBarSearchForm;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use MediawikiParser;
use MediawikiParserContext;

require_once Path::getInstance()->getPluginPath() . 'wiki/mediawiki_parser.class.php';
require_once Path::getInstance()->getPluginPath() . 'wiki/mediawiki_parser_context.class.php';

/**
 *
 * @package repository.lib.complex_display.assessment
 */

/**
 * This tool allows a user to publish assessments in his or her course.
 */
abstract class Manager extends \Chamilo\Core\Repository\Display\Manager
{
    const PARAM_WIKI_ID = 'wiki_id';
    const PARAM_WIKI_PAGE_ID = 'wiki_page_id';
    const PARAM_WIKI_VERSION_ID = 'wiki_version_id';
    const ACTION_BROWSE_WIKI = 'WikiBrowser';
    const ACTION_VIEW_WIKI = 'Viewer';
    const ACTION_VIEW_WIKI_PAGE = 'WikiItemViewer';
    const ACTION_CREATE_PAGE = 'WikiPageCreator';
    const ACTION_SET_AS_HOMEPAGE = 'WikiHomepageSetter';
    const ACTION_DISCUSS = 'WikiDiscuss';
    const ACTION_HISTORY = 'WikiHistory';
    const ACTION_PAGE_STATISTICS = 'PageStatistics';
    const ACTION_STATISTICS = 'Statistics';
    const ACTION_COMPARE = 'Comparer';
    const ACTION_ACCESS_DETAILS = 'AccessDetails';
    const ACTION_VERSION_REVERT = 'VersionReverter';
    const ACTION_VERSION_DELETE = 'VersionDeleter';
    const DEFAULT_ACTION = self::ACTION_VIEW_WIKI;

    private $search_form;

    /**
     *
     * @param \Chamilo\Libraries\Platform\ChamiloRequest $request
     * @param string $user
     * @param string $parent
     */
    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->search_form = new ActionBarSearchForm($this->get_url());
    }

    public static function is_wiki_locked($wiki_id)
    {
        $wiki = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(ContentObject::class_name(), $wiki_id);

        return $wiki->get_locked() == 1;
    }

    public static function get_wiki_homepage($wiki_id)
    {
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(
                ComplexContentObjectItem::class_name(), ComplexContentObjectItem::PROPERTY_PARENT
            ), new StaticConditionVariable($wiki_id)
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(ComplexWikiPage::class_name(), ComplexWikiPage::PROPERTY_IS_HOMEPAGE),
            new StaticConditionVariable(1)
        );
        $parameters = new DataClassRetrievesParameters(new AndCondition($conditions), 1, 0);
        $complex_wiki_homepage = \Chamilo\Core\Repository\Storage\DataManager::retrieve_complex_content_object_items(
            ComplexWikiPage::class_name(), $parameters
        );

        return $complex_wiki_homepage->next_result();
    }

    public function get_breadcrumbtrail()
    {
        $trail = BreadcrumbTrail::getInstance();
        $trail->add(
            new Breadcrumb(
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT)),
                $this->get_root_content_object()->get_title()
            )
        );
        switch (Request::get(self::PARAM_ACTION))
        {
            case self::ACTION_VIEW_COMPLEX_CONTENT_OBJECT :
                break;
            case self::ACTION_CREATE_PAGE :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE_PAGE)),
                        Translation::get('CreateWikiPage')
                    )
                );
                break;
            case self::ACTION_UPDATE_CONTENT_OBJECT :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_UPDATE_CONTENT_OBJECT)),
                        Translation::get('Edit', null, Utilities::COMMON_LIBRARIES)
                    )
                );
                break;
            case self::ACTION_PAGE_STATISTICS :
                $complex_wiki_page_id = Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
                $complex_wiki_page = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
                    ComplexContentObjectItem::class_name(), $complex_wiki_page_id
                );
                $wiki_page = $complex_wiki_page->get_ref_object();
                $trail->add(new Breadcrumb(null, $wiki_page->get_title()));
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_PAGE_STATISTICS,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request::get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            )
                        ), Translation::get('Reporting')
                    )
                );
                break;
            case self::ACTION_ACCESS_DETAILS :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_ACCESS_DETAILS,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request::get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            )
                        ), Translation::get('Reporting')
                    )
                );
                break;
            case self::ACTION_VIEW_WIKI_PAGE :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request::get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            )
                        ), $this->get_content_object_from_complex_id(
                        Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)
                    )->get_title()
                    )
                );
                break;
            case self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request::get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            )
                        ), $this->get_content_object_from_complex_id(
                        Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)
                    )->get_title()
                    )
                );
                break;
            case self::ACTION_DISCUSS :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request::get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            )
                        ), $this->get_content_object_from_complex_id(
                        Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)
                    )->get_title()
                    )
                );
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => Request::get(self::PARAM_ACTION),
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request::get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            )
                        ), Translation::get('WikiDiscuss')
                    )
                );
                break;
            case self::ACTION_HISTORY :
                $trail->add(
                    new Breadcrumb(
                        $this->get_url(
                            array(
                                self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                                self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request::get(
                                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID
                                )
                            )
                        ), $this->get_content_object_from_complex_id(
                        Request::get(self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID)
                    )->get_title()
                    )
                );

                break;
        }

        return $trail;
    }

    private function get_content_object_from_complex_id($complex_id)
    {
        $complex_content_object_item = \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ComplexContentObjectItem::class_name(), $complex_id
        );

        return \Chamilo\Core\Repository\Storage\DataManager::retrieve_by_id(
            ContentObject::class_name(), $complex_content_object_item->get_ref()
        );
    }

    public function get_publication()
    {
        return $this->get_parent()->get_publication();
    }

    public function render_header(ComplexWikiPage $complex_wiki_page = null)
    {
        $html = array();

        $html[] = parent::render_header(null, false);

        // The general menu
        $html[] = '<div class="wiki-menu">';

        $html[] = '<div class="wiki-menu-section">';
        $toolbar = new Toolbar(Toolbar::TYPE_VERTICAL);
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('MainPage'), new FontAwesomeGlyph('home'), $this->get_url(
                array(
                    self::PARAM_ACTION => self::ACTION_VIEW_WIKI,
                    self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => null
                )
            ), ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Contents'), new FontAwesomeGlyph('folder'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_BROWSE_WIKI)),
                ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('Statistics'), new FontAwesomeGlyph('bar-chart'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_STATISTICS)),
                ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );
        $html[] = $toolbar->as_html();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $links = $this->get_root_content_object()->get_links();

        if (!empty($links))
        {
            $object_renderer = new ContentObjectResourceRenderer($this, $links);
            $links = $object_renderer->run();
            $parser = new MediawikiParser(
                new MediawikiParserContext($this->get_root_content_object(), '', $links, $this->get_parameters())
            );

            $html[] = '<div class="wiki-menu-section">';
            $html[] = $parser->parse();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
        }

        $html[] = '<div class="wiki-menu-section">';
        $toolbar = new Toolbar(Toolbar::TYPE_VERTICAL);
        $toolbar->add_item(
            new ToolbarItem(
                Translation::get('AddWikiPage'), new FontAwesomeGlyph('plus'),
                $this->get_url(array(self::PARAM_ACTION => self::ACTION_CREATE_PAGE)),
                ToolbarItem::DISPLAY_ICON_AND_LABEL
            )
        );
        $html[] = $toolbar->as_html();
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '</div>';

        // The main content pane
        $html[] = '<div class="wiki-pane">';
        $html[] = '<div class="wiki-pane-actions-bar">';

        $display_action = $this->get_action();

        if ($display_action != self::ACTION_CREATE_PAGE)
        {
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-left">';
            if ($complex_wiki_page)
            {
                $read_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_VIEW_WIKI_PAGE,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                    )
                );
                $discuss_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_DISCUSS,
                        'wiki_publication' => Request::get('wiki_publication'),
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                    )
                );
                $statistics_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_PAGE_STATISTICS,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                    )
                );

                $html[] = '<li><a' . ($this->get_action() != self::ACTION_DISCUSS &&
                    $this->get_action() != self::ACTION_PAGE_STATISTICS ? ' class="current"' : '') . ' href="' .
                    $read_url . '">' . Translation::get('WikiArticle') . '</a></li>';
                $html[] =
                    '<li><a' . ($this->get_action() == self::ACTION_DISCUSS ? ' class="current"' : '') . ' href="' .
                    $discuss_url . '">' . Translation::get('WikiDiscuss') . '</a></li>';
                $html[] = '<li><a' . ($this->get_action() == self::ACTION_PAGE_STATISTICS ? ' class="current"' : '') .
                    ' href="' . $statistics_url . '">' . Translation::get('WikiStatistics') . '</a></li>';
            }
            else
            {
                $html[] = '<li><a class="current" href="#">' . Translation::get('WikiArticle') . '</a></li>';
            }

            $html[] = '</ul>';

            $html[] = '<div class="wiki-pane-actions wiki-pane-actions-right wiki-pane-search">';
            $html[] = $this->search_form->as_html();
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';

            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-right">';

            if ($complex_wiki_page)
            {
                $delete_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                    )
                );
                $history_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_HISTORY,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                    )
                );
                $edit_url = $this->get_url(
                    array(
                        self::PARAM_ACTION => self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        self::PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_wiki_page->get_id()
                    )
                );

                $complex_wiki_page_properties = $complex_wiki_page->get_ref_object()->get_properties();

                $html[] = '<li><a' .
                    (in_array($this->get_action(), array(self::ACTION_VIEW_WIKI, self::ACTION_VIEW_WIKI_PAGE)) &&
                    !Request::get(self::PARAM_WIKI_VERSION_ID) ? ' class="current"' : '') . ' href="' . $read_url .
                    '">' . Translation::get('WikiRead') . '</a></li>';
                $html[] = '<li><a' .
                    ($this->get_action() == self::ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM ? ' class="current"' : '') .
                    ' href="' . $edit_url . '">' . Translation::get('WikiEdit') . '</a></li>';
                $html[] = '<li><a' . (($this->get_action() == self::ACTION_HISTORY) ||
                    ($this->get_action() == self::ACTION_VIEW_WIKI_PAGE && Request::get(self::PARAM_WIKI_VERSION_ID)) ?
                        ' class="current"' : '') . ' href="' . $history_url . '">' . Translation::get('WikiHistory') .
                    '</a></li>';
                $html[] = '<li><a' .
                    ($this->get_action() == self::ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM ? ' class="current"' : '') .
                    ' href="' . $delete_url . '" onClick="return confirm(\'' . Translation::get(
                        'WikiDeleteConfirm', array(
                        'WIKIPAGENAME' => $complex_wiki_page_properties['default_properties']['title'],
                        'WIKINAME' => $this->get_root_content_object()->get_title()
                    ), __NAMESPACE__
                    ) . '\')">' . Translation::get('WikiDelete') . '</a></li>';
            }
            else
            {
                $html[] = '<li><a class="current" href="#">' . Translation::get('WikiRead') . '</a></li>';
            }

            $html[] = '</ul>';
        }
        else
        {
            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-left">';
            $html[] = '<li><a class="current" href="#">' . Translation::get('AddWikiPage') . '</a></li>';
            $html[] = '</ul>';

            $html[] = '<ul class="wiki-pane-actions wiki-pane-actions-right">';

            $repository_viewer_action = Request::get(\Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION);
            $creator_url = $this->get_url(
                array(
                    \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager::ACTION_CREATOR
                )
            );
            $browser_url = $this->get_url(
                array(
                    \Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION => \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER
                )
            );
            $html[] = '<li><a' .
                (($repository_viewer_action == \Chamilo\Core\Repository\Viewer\Manager::ACTION_CREATOR ||
                    is_null($repository_viewer_action)) ? ' class="current"' : '') . ' href="' . $creator_url . '">' .
                Translation::get(
                    'WikiPageNew'
                ) . '</a></li>';
            $html[] = '<li><a' . ($repository_viewer_action == \Chamilo\Core\Repository\Viewer\Manager::ACTION_BROWSER ?
                    ' class="current"' : '') . ' href="' . $browser_url . '">' . Translation::get('WikiPageSelect') .
                '</a></li>';

            if ($repository_viewer_action == \Chamilo\Core\Repository\Viewer\Manager::ACTION_VIEWER)
            {
                $html[] = '<li><a class="current" href="#">' . Translation::get('WikiPagePreview') . '</a></li>';
            }

            $html[] = '</ul>';
        }

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '<div class="wiki-pane-content">';

        return implode(PHP_EOL, $html);
    }

    public function render_footer()
    {
        $html = array();

        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="wiki-pane-top"><a href=#top>' . Theme::getInstance()->getCommonImage(
                'Action/AjaxAdd', 'png', Translation::get('BackToTop')
            ) . '</a></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = parent::render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_search_form()
    {
        return $this->search_form;
    }
}
