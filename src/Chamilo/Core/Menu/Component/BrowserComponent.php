<?php
namespace Chamilo\Core\Menu\Component;

use Chamilo\Core\Menu\Manager;
use Chamilo\Core\Menu\Menu\ItemMenu;
use Chamilo\Core\Menu\Storage\DataClass\ApplicationItem;
use Chamilo\Core\Menu\Storage\DataClass\CategoryItem;
use Chamilo\Core\Menu\Storage\DataClass\Item;
use Chamilo\Core\Menu\Storage\DataClass\LinkItem;
use Chamilo\Core\Menu\Table\Item\ItemBrowserTable;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Table\Interfaces\TableSupport;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Core\Menu\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class BrowserComponent extends Manager implements DelegateComponent, TableSupport
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        $this->getRightsService()->isUserAllowedToAccessComponent($this->getUser());

        $table = new ItemBrowserTable(
            $this, $this->getTranslator(), $this->getItemService(), $this->getRightsService(),
            $this->getParentIdentifier()
        );

        $html = array();

        $html[] = $this->render_header();

        $html[] = $this->getButtonToolbarRenderer()->render();

        $html[] = '<div class="row">';
        $html[] = '<div class="col-xs-12 col-lg-2">';
        $html[] = $this->getMenu()->renderAsTree();
        $html[] = '</div>';

        $html[] = '<div class="col-xs-12 col-lg-10">';
        $html[] = $table->render();
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     * @param \Chamilo\Libraries\Format\Structure\BreadcrumbTrail $breadcrumbtrail
     */
    public function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('menu_browser');
    }

    /**
     * @return \Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $translator = $this->getTranslator();

            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $commonActions->addButton(
                new Button(
                    $translator->trans('AddApplicationItem', [], 'Chamilo\Core\Menu'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Menu', 'Types/' . Item::TYPE_APPLICATION),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_CREATE, self::PARAM_TYPE => ApplicationItem::class_name()
                        )
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('AddCategoryItem', [], 'Chamilo\Core\Menu'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Menu', 'Types/' . Item::TYPE_CATEGORY),
                    $this->get_url(
                        array(
                            self::PARAM_ACTION => self::ACTION_CREATE, self::PARAM_TYPE => CategoryItem::class_name()
                        )
                    ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            $commonActions->addButton(
                new Button(
                    $translator->trans('AddLinkItem', [], 'Chamilo\Core\Menu'),
                    Theme::getInstance()->getImagePath('Chamilo\Core\Menu', 'Types/' . Item::TYPE_LINK), $this->get_url(
                    array(self::PARAM_ACTION => self::ACTION_CREATE, self::PARAM_TYPE => LinkItem::class_name())
                ), ToolbarItem::DISPLAY_ICON_AND_LABEL
                )
            );

            if ($this->getRightsService()->areRightsEnabled())
            {
                $toolActions->addButton(
                    new Button(

                        $translator->trans('Rights', [], Utilities::COMMON_LIBRARIES),
                        Theme::getInstance()->getCommonImagePath('Action/Rights'),
                        $this->get_url(array(self::PARAM_ACTION => self::ACTION_RIGHTS)),
                        ToolbarItem::DISPLAY_ICON_AND_LABEL
                    )
                );
            }

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    /**
     * @return \Chamilo\Core\Menu\Menu\ItemMenu
     */
    public function getMenu()
    {
        $urlFormat = new Redirect([self::PARAM_ACTION => self::ACTION_BROWSE, self::PARAM_PARENT => '__ITEM__']);

        return new ItemMenu(
            $this->getItemService(), $this->getTranslator(), $urlFormat->getUrl(), $this->getParentIdentifier()
        );
    }

    /**
     * @return integer
     */
    public function getParentIdentifier()
    {
        if (!isset($this->parentIdentifier))
        {
            $this->parentIdentifier = $this->getRequest()->query->get(self::PARAM_PARENT, 0);
        }

        return $this->parentIdentifier;
    }

    /**
     * @return string[]
     */
    public function get_additional_parameters()
    {
        return array(Manager::PARAM_ITEM);
    }

    /**
     * @param string $tableClassName
     *
     * @return void
     */
    public function get_table_condition($tableClassName)
    {
    }
}
