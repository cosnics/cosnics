<?php
namespace Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Component;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\Common\Rendition\ContentObjectRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Display\Manager;
use Chamilo\Core\Repository\ContentObject\FrequentlyAskedQuestions\Storage\DataClass\FrequentlyAskedQuestions;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTab;
use Chamilo\Libraries\Format\Tabs\DynamicVisualTabsRenderer;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * Default viewer component that handles the visualization of the portfolio item or folder
 *
 * @package repository\content_object\portfolio\display
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ViewerComponent extends Manager
{

    /**
     *
     * @var \Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath
     */
    private $path;

    private $tabs_renderer;

    /**
     * Executes this component
     */
    public function run()
    {
        $this->path = $this->get_root_content_object()->get_complex_content_object_path();

        $html = array();
        $html[] = $this->render_header();

        $html[] = $this->show_child_node($this->path->get_root());
        $html[] = ResourceManager :: getInstance()->get_resource_html(
            Path :: getInstance()->getJavascriptPath(self :: package(), true) . 'Faq.js');
        $html[] = $this->render_footer();

        return implode(PHP_EOL, $html);
    }

    private function show_child_node(ComplexContentObjectPathNode $root_node)
    {
        $html = array();
        $class = 'faq-item';
        if ($root_node->is_root())
            $class .= ' faq-item-first';
        $html[] = '<ul id="faq-item-' . $root_node->get_content_object()->get_id() . '" class="' . $class . '" data_id="' .
             $root_node->get_content_object()->get_id() . '">';

        $html[] = '<li>';
        $content = array();
        $icon = Theme :: getInstance()->getImagePath($root_node->get_content_object()->package(), 'Logo/16');

        $content[] = '<div class="faq-item-title"><img src="' . $icon . '"></img>' .
             $root_node->get_content_object()->get_title() . '</div>';
        $content[] = ContentObjectRenditionImplementation :: launch(
            $root_node->get_content_object(),
            ContentObjectRendition :: FORMAT_HTML,
            ContentObjectRendition :: VIEW_DESCRIPTION,
            $this);

        $tabs_renderer = new DynamicVisualTabsRenderer('faq');
        $this->displayTab($tabs_renderer, $root_node);
        $tabs_renderer->set_content(implode(PHP_EOL, $content));
        $html[] = $tabs_renderer->render();
        $html[] = '</li>';

        foreach ($root_node->get_children() as $child_node)
        {
            $html[] = $this->show_child_node($child_node);

            if ($child_node->is_last_child())
            {
                break;
            }
        }

        $html[] = '</ul>';

        return implode(PHP_EOL, $html);
    }

    private function displayTab($tabs_renderer, ComplexContentObjectPathNode $root_node)
    {
        $tabs_renderer->add_tab(
            new DynamicVisualTab(
                self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                Translation :: get('Delete'),
                Theme :: getInstance()->getImagePath(
                    Manager :: package(),
                    'Tab/' . self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                        self :: PARAM_STEP => $root_node->get_id())),
                $this->get_action() == self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM,
                true,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        $tabs_renderer->add_tab(
            new DynamicVisualTab(
                self :: ACTION_MOVE,
                Translation :: get('Move'),
                Theme :: getInstance()->getImagePath(Manager :: package(), 'Tab/' . self :: ACTION_MOVE),
                $this->get_url(
                    array(self :: PARAM_ACTION => self :: ACTION_MOVE, self :: PARAM_STEP => $root_node->get_id())),
                $this->get_action() == self :: ACTION_MOVE,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        if (! $root_node->is_last_child())
        {
            $tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_SORT,
                    Translation :: get('MoveDown'),
                    Theme :: getInstance()->getImagePath(
                        Manager :: package(),
                        'Tab/' . self :: ACTION_SORT . self :: SORT_DOWN),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_SORT,
                            self :: PARAM_SORT => self :: SORT_DOWN,
                            self :: PARAM_STEP => $root_node->get_id())),
                    $this->get_action() == self :: ACTION_SORT,
                    false,
                    DynamicVisualTab :: POSITION_RIGHT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        else
        {
            $tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_SORT,
                    Translation :: get('MoveDownNotAvailable'),
                    Theme :: getInstance()->getImagePath(
                        Manager :: package(),
                        'Tab/' . self :: ACTION_SORT . self :: SORT_DOWN . 'Na'),
                    null,
                    false,
                    false,
                    DynamicVisualTab :: POSITION_RIGHT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }

        if (! $root_node->is_first_child())
        {
            $tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_SORT,
                    Translation :: get('MoveUp'),
                    Theme :: getInstance()->getImagePath(
                        Manager :: package(),
                        'Tab/' . self :: ACTION_SORT . self :: SORT_UP),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_SORT,
                            self :: PARAM_SORT => self :: SORT_UP,
                            self :: PARAM_STEP => $root_node->get_id())),
                    $this->get_action() == self :: ACTION_SORT,
                    false,
                    DynamicVisualTab :: POSITION_RIGHT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
        else
        {
            $tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_SORT,
                    Translation :: get('MoveUpNotAvailable'),
                    Theme :: getInstance()->getImagePath(
                        Manager :: package(),
                        'Tab/' . self :: ACTION_SORT . self :: SORT_UP . 'Na'),
                    null,
                    false,
                    false,
                    DynamicVisualTab :: POSITION_RIGHT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }

        $tabs_renderer->add_tab(
            new DynamicVisualTab(
                self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                Translation :: get('Update'),
                Theme :: getInstance()->getImagePath(
                    Manager :: package(),
                    'Tab/' . self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM),
                $this->get_url(
                    array(
                        self :: PARAM_ACTION => self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                        self :: PARAM_STEP => $root_node->get_id())),
                $this->get_action() == self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM,
                false,
                DynamicVisualTab :: POSITION_RIGHT,
                DynamicVisualTab :: DISPLAY_BOTH_SELECTED));

        if ($root_node->get_content_object() instanceof FrequentlyAskedQuestions)
        {
            $tabs_renderer->add_tab(
                new DynamicVisualTab(
                    self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    Translation :: get('Add'),
                    Theme :: getInstance()->getImagePath(
                        Manager :: package(),
                        'Tab/' . self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM),
                    $this->get_url(
                        array(
                            self :: PARAM_ACTION => self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                            self :: PARAM_STEP => $root_node->get_id())),
                    $this->get_action() == self :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    false,
                    DynamicVisualTab :: POSITION_RIGHT,
                    DynamicVisualTab :: DISPLAY_BOTH_SELECTED));
        }
    }
}
