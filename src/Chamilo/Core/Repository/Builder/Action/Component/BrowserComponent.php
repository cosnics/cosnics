<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\Builder\Interfaces\MenuSupport;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * 
 * @package repository.lib.complex_builder.component
 */
class BrowserComponent extends Manager implements DelegateComponent
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function run()
    {
        $this->get_complex_content_object_breadcrumbs();
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository builder');
        
        if ($this->get_complex_content_object_item())
        {
            $content_object = $this->get_complex_content_object_item()->get_ref_object();
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }
        $html = array();
        
        $html[] = $this->render_header();
        
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();
        
        if ($this->buttonToolbarRenderer)
        {
            $html[] = '<br />';
            $html[] = $this->buttonToolbarRenderer->render();
        }
        
        $html[] = ContentObjectRenditionImplementation :: launch(
            $this->get_root_content_object(), 
            ContentObjectRendition :: FORMAT_HTML, 
            ContentObjectRendition :: VIEW_FULL, 
            $this);
        
        $html[] = '<br />';
        $html[] = $this->get_creation_links($content_object);
        $html[] = '<div class="clear">&nbsp;</div><br />';
        
        if ($this->get_parent() instanceof MenuSupport)
        {
            $html[] = '<div style="width: 18%; overflow: auto; float: left;">';
            $html[] = $this->get_complex_content_object_menu();
            $html[] = '</div>';
            $html[] = '<div style="width: 80%; float: right;">';
        }
        else
        {
            $html[] = '<div>';
        }
        $html[] = $this->get_complex_content_object_table_html();
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        
        $html[] = $this->render_footer();
        
        return implode(PHP_EOL, $html);
    }

    /**
     * Builds the actionbar
     * 
     * @param ContentObject $content_object
     *
     * @return ButtonToolBarRenderer
     */
    public function getButtonToolbarRenderer($content_object)
    {
        $buttonToolbar = parent :: getButtonToolbarRenderer($content_object);
        
        if (! isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
        }
        $commonActions = new ButtonGroup();
        
        $preview_url = \Chamilo\Core\Repository\Manager :: get_preview_content_object_url($content_object);
        
        $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
        $commonActions->addButton(
            new Button(
                Translation :: get('Preview', null, Utilities :: COMMON_LIBRARIES), 
                Theme :: getInstance()->getCommonImagePath('Action/Preview'), 
                $this->get_parent()->get_preview_content_object_url(), 
                ToolbarItem :: DISPLAY_ICON_AND_LABEL, 
                false, 
                $onclick, 
                '_blank'));
        
        $buttonToolbar->addButtonGroup($commonActions);
        return new ButtonToolBarRenderer($buttonToolbar);
    }
}
