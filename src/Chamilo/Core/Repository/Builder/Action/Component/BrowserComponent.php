<?php
namespace Chamilo\Core\Repository\Builder\Action\Component;

use Chamilo\Core\Repository\Builder\Action\Manager;
use Chamilo\Core\Repository\Builder\Interfaces\MenuSupport;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_builder.component
 */
class BrowserComponent extends Manager implements DelegateComponent
{

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

        $action_bar = $this->get_action_bar($content_object);

        if ($action_bar)
        {
            $html[] = '<br />';
            $html[] = $action_bar->as_html();
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
     * @return ActionbarRenderer
     */
    public function get_action_bar($content_object)
    {
        $action_bar = parent :: get_action_bar($content_object);

        if (! $action_bar)
        {
            $action_bar = new ActionBarRenderer();
        }

        $preview_url = \Chamilo\Core\Repository\Manager :: get_preview_content_object_url($content_object);

        $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
        $action_bar->add_common_action(
            new ToolbarItem(
                Translation :: get('Preview', null, Utilities :: COMMON_LIBRARIES),
                Theme :: getInstance()->getCommonImagesPath() . 'action_preview.png',
                $this->get_parent()->get_preview_content_object_url(),
                ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                false,
                $onclick,
                '_blank'));

        return $action_bar;
    }
}
