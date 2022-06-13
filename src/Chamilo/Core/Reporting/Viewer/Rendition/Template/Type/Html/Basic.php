<?php
namespace Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Html;

use Chamilo\Core\Reporting\Viewer\Manager;
use Chamilo\Core\Reporting\Viewer\NoBlockTabsAllowed;
use Chamilo\Core\Reporting\Viewer\Rendition\Block\BlockRenditionImplementation;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\TemplateRendition;
use Chamilo\Core\Reporting\Viewer\Rendition\Template\Type\Html;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\DependencyInjection\DependencyInjectionContainerBuilder;
use Chamilo\Libraries\Format\Structure\ActionBar\Button;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonGroup;
use Chamilo\Libraries\Format\Structure\ActionBar\ButtonToolBar;
use Chamilo\Libraries\Format\Structure\ActionBar\Renderer\ButtonToolBarRenderer;
use Chamilo\Libraries\Format\Structure\Breadcrumb;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\IdentGlyph;
use Chamilo\Libraries\Format\Structure\Glyph\NamespaceIdentGlyph;
use Chamilo\Libraries\Format\Tabs\Link\LinkTab;
use Chamilo\Libraries\Format\Tabs\Link\LinkTabsRenderer;
use Chamilo\Libraries\Format\Tabs\TabsCollection;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @author Hans De Bisschop & Magali Gillard
 * @package reporting.viewer
 */
class Basic extends Html
{

    /**
     *
     * @var ButtonToolBarRenderer
     */
    private $buttonToolbarRenderer;

    public function render()
    {
        $this->buttonToolbarRenderer = $this->getButtonToolbarRenderer();

        $html = [];

        $html[] = $this->buttonToolbarRenderer->render();
        $html[] = $this->render_block();

        return implode(PHP_EOL, $html);
    }

    public function determine_current_block_view($current_block_id)
    {
        $current_block = $this->get_template()->get_block($current_block_id);
        $selected_view = $this->get_context()->get_current_view();
        $available_views = $current_block->get_views();

        if ($selected_view[$current_block_id])
        {
            if (in_array($selected_view[$current_block_id], $available_views))
            {
                return $selected_view[$current_block_id];
            }
            else
            {
                return $available_views[0];
            }
        }
        else
        {
            return $available_views[0];
        }
    }

    /**
     * Returns the correct namespace for a given block
     *
     * @param $block
     *
     * @return string
     */
    protected function getBlockNamespace($block)
    {
        $classNameUtilities = ClassnameUtilities::getInstance();
        $namespace = $classNameUtilities->getNamespaceParent($block->context());

        while (strrpos($namespace, 'Reporting') !== false &&
            (strlen($namespace) - strrpos($namespace, 'Reporting')) != 9)
        {
            $namespace = $classNameUtilities->getNamespaceParent($namespace);
        }

        return $namespace;
    }

    public function getButtonToolbarRenderer()
    {
        if (!isset($this->buttonToolbarRenderer))
        {
            $buttonToolbar = new ButtonToolBar();
            $commonActions = new ButtonGroup();
            $toolActions = new ButtonGroup();

            $parameters = $this->get_context()->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_VIEW;

            if (!$this->get_template() instanceof NoBlockTabsAllowed)
            {
                if ($this->show_all())
                {
                    $parameters[Manager::PARAM_SHOW_ALL] = null;

                    $toolActions->addButton(
                        new Button(
                            Translation::get('ShowOne'), new FontAwesomeGlyph('clone', [], null, 'fas'),
                            $this->get_context()->get_url($parameters)
                        )
                    );
                }
                elseif ($this->get_template()->count_blocks() > 1)
                {
                    $parameters[Manager::PARAM_SHOW_ALL] = 1;

                    $toolActions->addButton(
                        new Button(
                            Translation::get('ShowAll'), new FontAwesomeGlyph('desktop', [], null, 'fas'),
                            $this->get_context()->get_url($parameters)
                        )
                    );
                }
            }

            $parameters = $this->get_context()->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_SAVE;
            $parameters[Manager::PARAM_SHOW_ALL] = 1;
            $parameters[Manager::PARAM_FORMAT] = TemplateRendition::FORMAT_XLSX;

            $commonActions->addButton(
                new Button(
                    Translation::get('ExportToExcel'), new FontAwesomeGlyph('file-excel', [], null, 'fas'),
                    $this->get_context()->get_url($parameters)
                )
            );

            $parameters = $this->get_context()->get_parameters();
            $parameters[Manager::PARAM_ACTION] = Manager::ACTION_SAVE;
            $parameters[Manager::PARAM_SHOW_ALL] = 1;
            $parameters[Manager::PARAM_FORMAT] = TemplateRendition::FORMAT_PDF;
            $commonActions->addButton(
                new Button(
                    Translation::get('ExportToPdf'), new FontAwesomeGlyph('file-pdf', [], null, 'fas'),
                    $this->get_context()->get_url($parameters)
                )
            );

            $buttonToolbar->addButtonGroup($commonActions);
            $buttonToolbar->addButtonGroup($toolActions);

            $this->buttonToolbarRenderer = new ButtonToolBarRenderer($buttonToolbar);
        }

        return $this->buttonToolbarRenderer;
    }

    protected function getLinkTabsRenderer(): LinkTabsRenderer
    {
        return DependencyInjectionContainerBuilder::getInstance()->createContainer()->get(
            LinkTabsRenderer::class
        );
    }

    public function render_block()
    {
        if ($this->show_all())
        {
            $this->get_context()->set_parameter(Manager::PARAM_SHOW_ALL, 1);
            $this->get_context()->set_parameter(Manager::PARAM_VIEWS, $this->get_context()->get_current_view());

            $html = [];

            foreach ($this->get_template()->get_blocks() as $key => $block)
            {
                $title = $block->get_title();

                $html[] = '<h2>';

                $glyph = new NamespaceIdentGlyph(
                    $this->getBlockNamespace($block) . '\Block\\' .
                    ClassnameUtilities::getInstance()->getClassnameFromObject($block), false, false, false, null, []
                );

                $html[] = $glyph->render();
                $html[] = $title;
                $html[] = '</h2>';
                $html[] = BlockRenditionImplementation::launch(
                    $this, $block, $this->get_format(), $this->determine_current_block_view($key)
                );
            }

            return implode(PHP_EOL, $html);
        }
        else
        {
            $current_block_id = $this->determine_current_block_id();
            $current_block = $this->get_template()->get_block($current_block_id);

            $rendered_block = BlockRenditionImplementation::launch(
                $this, $current_block, $this->get_format(), $this->determine_current_block_view($current_block_id)
            );

            if ($this->get_template()->count_blocks() > 1)
            {
                $tabs = new TabsCollection();
                $context_parameters = $this->get_context()->get_parameters();
                $trail = BreadcrumbTrail::getInstance();

                foreach ($this->get_template()->get_blocks() as $key => $block)
                {
                    $block_parameters = array_merge($context_parameters, array(Manager::PARAM_BLOCK_ID => $key));

                    $is_current_block = $key == $this->determine_current_block_id();

                    $title = $block->get_title();

                    if ($is_current_block)
                    {
                        $trail->add(new Breadcrumb($this->get_context()->get_url($block_parameters), $title));
                    }

                    $glyph = new NamespaceIdentGlyph(
                        $this->getBlockNamespace($block) . '\Block\\' .
                        ClassnameUtilities::getInstance()->getClassnameFromObject($block), true, false, false,
                        IdentGlyph::SIZE_SMALL, []
                    );

                    $tabs->add(
                        new LinkTab(
                            $key, $title, $glyph, $this->get_context()->get_url($block_parameters), $is_current_block
                        )
                    );
                }

                return $this->getLinkTabsRenderer()->render($tabs, $rendered_block);
            }
            else
            {
                return $rendered_block;
            }
        }
    }
}
