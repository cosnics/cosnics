<?php
namespace Chamilo\Core\Home\Renderer\Type\Basic;

use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Block;
use Chamilo\Core\Home\Storage\DataClass\Column;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Home\Renderer\Type\Basic
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class ColumnRenderer
{

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @var \Chamilo\Core\Home\Service\HomeService
     */
    private $homeService;

    /**
     *
     * @var \Chamilo\Core\Home\Storage\DataClass\Column
     */
    private $column;

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     * @param \Chamilo\Core\Home\Storage\DataClass\Column $column
     */
    public function __construct(Application $application, HomeService $homeService, Column $column)
    {
        $this->application = $application;
        $this->homeService = $homeService;
        $this->column = $column;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $column = $this->getColumn();
        $html = [];

        $html[] = '<div class="col-xs-12 col-md-' . $column->getWidth() . ' portal-column" data-tab-id="' .
            $column->getParentId() . '" data-element-id="' . $column->getId() . '" data-element-width="' .
            $column->getWidth() . '">';

        $blocks = $this->getHomeService()->getElements(
            $this->getApplication()->getUser(), Block::class, $column->getId()
        );

        foreach ($blocks as $block)
        {
            $blockRendererFactory = new BlockRendererFactory($this->getApplication(), $this->getHomeService(), $block);
            $blockRenderer = $blockRendererFactory->getRenderer();

            if ($blockRenderer->isVisible())
            {
                $html[] = $blockRenderer->toHtml();
            }
        }

        $hasMultipleColumns = $this->getHomeService()->tabByUserAndIdentifierHasMultipleColumns(
            $this->getApplication()->getUser(), $column->getParentId()
        );

        $html[] = $this->renderEmptyColumn($column->getId(), (count($blocks) > 0), !$hasMultipleColumns);

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Storage\DataClass\Column
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     *
     * @param \Chamilo\Core\Home\Storage\DataClass\Column $column
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Service\HomeService
     */
    public function getHomeService()
    {
        return $this->homeService;
    }

    /**
     *
     * @param \Chamilo\Core\Home\Service\HomeService $homeService
     */
    public function setHomeService($homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     *
     * @param integer $columnId
     * @param boolean $isEmpty
     *
     * @return string
     */
    public function renderEmptyColumn($columnId, $isEmpty = false, $isOnlyColumn = false)
    {
        $html = [];

        $html[] = '<div class="panel panel-warning portal-column-empty ' . ($isEmpty ? 'hidden' : 'show') . '">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<div class="pull-right">';
        $html[] =
            '<a href="#" class="portal-action portal-action-column-delete ' . ($isOnlyColumn ? 'hidden' : 'show') .
            '" data-column-id="' . $columnId . '" title="' . Translation::get('Delete') . '">';

        $glyph = new FontAwesomeGlyph('times', [], null, 'fas');

        $html[] = $glyph->render() . '</a>';
        $html[] = '</div>';
        $html[] = '<h3 class="panel-title">' . Translation::get('EmptyColumnTitle') . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = Translation::get('EmptyColumnBody');
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}