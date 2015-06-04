<?php
namespace Chamilo\Core\Repository\Preview\Component;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\Preview\Manager;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

class RenditionComponent extends Manager
{
    const PARAM_FORMAT = 'format';
    const PARAM_VIEW = 'view';

    /**
     * Executes this controller
     */
    public function run()
    {
        $tabs = new DynamicVisualTabsRenderer('display');

        $views = array(
            ContentObjectRendition :: VIEW_FULL,
            ContentObjectRendition :: VIEW_PREVIEW,
            ContentObjectRendition :: VIEW_THUMBNAIL,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            ContentObjectRendition :: VIEW_SHORT,
            ContentObjectRendition :: VIEW_INLINE,
            ContentObjectRendition :: VIEW_FORM
        );

        foreach ($views as $view)
        {
            $tabs->add_tab(
                new DynamicVisualTab(
                    $view,
                    Translation :: get('View' . StringUtilities :: getInstance()->createString($view)->upperCamelize()),
                    Theme :: getInstance()->getImagePath('Chamilo\Core\Repository\Preview', 'View/' . StringUtilities :: getInstance()->createString($view)->upperCamelize()),
                    $this->get_url(array(self :: PARAM_FORMAT => $this->get_format(), self :: PARAM_VIEW => $view)),
                    $this->get_view() == $view));
        }

        $display = ContentObjectRenditionImplementation :: factory(
            $this->get_content_object(),
            $this->get_format(),
            $this->get_view(),
            $this);

        $tabs->set_content($display->render());

        $html = array();

        $html[] = $this->render_header();
        $html[] = $tabs->render();
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \core\repository\ContentObject
     */
    public function get_root_content_object()
    {
        return $this->get_content_object();
    }

    public function get_format()
    {
        return Request :: get(self :: PARAM_FORMAT, ContentObjectRendition :: FORMAT_HTML);
    }

    public function get_view()
    {
        return Request :: get(self :: PARAM_VIEW, ContentObjectRendition :: VIEW_FULL);
    }
}
