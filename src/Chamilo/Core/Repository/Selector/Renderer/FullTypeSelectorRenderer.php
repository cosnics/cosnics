<?php
namespace Chamilo\Core\Repository\Selector\Renderer;

use Chamilo\Core\Repository\Selector\TypeSelector;
use Chamilo\Core\Repository\Selector\TypeSelectorRenderer;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @author Hans De Bisschop
 */
class FullTypeSelectorRenderer extends TypeSelectorRenderer
{

    /**
     *
     * @param Application $parent
     * @param TypeSelector $type_selector
     * @param LinkTypeSelectorOption[] $additional_links
     * @param boolean $use_general_statistics
     * @param string $postback_url
     */
    public function __construct(Application $parent, TypeSelector $type_selector, $additional_links = array(),
        $use_general_statistics = false, $postback_url = null)
    {
        $this->form_renderer = new FormTypeSelectorRenderer($parent, $type_selector, $postback_url);
        $this->tabs_renderer = new TabsTypeSelectorRenderer(
            $parent,
            $type_selector,
            $additional_links,
            $use_general_statistics);
    }

    /**
     * Render the form and tabs
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = $this->form_renderer->render();
        $html[] = $this->tabs_renderer->render();

        return implode(PHP_EOL, $html);
    }
}