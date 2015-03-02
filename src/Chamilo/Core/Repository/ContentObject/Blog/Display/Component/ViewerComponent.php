<?php
namespace Chamilo\Core\Repository\ContentObject\Blog\Display\Component;

use Chamilo\Core\Repository\ContentObject\BlogItem\Storage\DataClass\BlogItem;
use Chamilo\Core\Repository\ContentObject\Blog\Display\Component\Viewer\BlogLayout;
use Chamilo\Core\Repository\ContentObject\Blog\Display\Manager;
use Chamilo\Libraries\Architecture\Interfaces\DelegateComponent;
use Chamilo\Libraries\Format\Structure\ActionBarRenderer;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * $Id: blog_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 *
 * @package repository.lib.complex_display.blog.component
 */
class ViewerComponent extends Manager implements DelegateComponent
{

    private $action_bar;

    public function run()
    {
        $this->action_bar = $this->get_action_bar();
        $blog = $this->get_root_content_object();
        $trail = BreadcrumbTrail :: get_instance();
        $blog_layout = BlogLayout :: factory($this, $blog);

        $html = array();

        $html[] = $this->render_header();
        $html[] = $this->action_bar->as_html();
        $html[] = $blog_layout->as_html();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    public function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->get_parent()->is_allowed_to_add_child())
        {
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('CreateItem', null, Utilities :: COMMON_LIBRARIES),
                    Theme :: getInstance()->getCommonImagesPath() . 'action_create.png',
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self :: PARAM_TYPE => BlogItem :: class_name())),
                    ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        return $action_bar;
    }
}
