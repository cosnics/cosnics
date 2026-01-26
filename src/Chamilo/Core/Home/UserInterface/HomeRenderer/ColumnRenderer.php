<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Architecture\Domain\BlockRendererCollection;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\Renderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ColumnRenderer
{

    protected BlockRendererCollection $blockRendererFactory;

    protected HomeService $homeService;

    protected Translator $translator;

    public function __construct(
        HomeService $homeService, Translator $translator, BlockRendererCollection $blockRendererFactory
    )
    {
        $this->homeService = $homeService;
        $this->translator = $translator;
        $this->blockRendererFactory = $blockRendererFactory;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     */
    public function render(Element $column, ?User $user = null): string
    {
        $html = [];

        $html[] = '<div class="col-xs-12 col-md-' . $column->getWidth() . ' portal-column" data-tab-id="' .
            $column->getParentId() . '" data-element-id="' . $column->getId() . '" data-element-width="' .
            $column->getWidth() . '">';

        $blocks = $this->getHomeService()->findElementsByTypeAndParentIdentifier(
            Element::TYPE_BLOCK, $column->getId()
        );

        foreach ($blocks as $block)
        {
            $blockRenderer = $this->getBlockRendererFactory()->getRendererForElement($block);
            $html[] = $blockRenderer->render($block, $user);
        }

        $hasMultipleColumns = $this->getHomeService()->tabByIdentifierHasMultipleColumns(
            $column->getParentId()
        );

        $html[] = $this->renderEmptyColumn($column->getId(), (count($blocks) > 0), !$hasMultipleColumns);

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getBlockRendererFactory(): BlockRendererCollection
    {
        return $this->blockRendererFactory;
    }

    public function getHomeService(): HomeService
    {
        return $this->homeService;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function renderEmptyColumn(string $columnId, bool $isEmpty = false, $isOnlyColumn = false): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<div class="panel panel-warning portal-column-empty ' . ($isEmpty ? 'hidden' : 'show') . '">';
        $html[] = '<div class="panel-heading">';
        $html[] = '<div class="pull-right">';
        $html[] =
            '<a href="#" class="portal-action portal-action-column-delete ' . ($isOnlyColumn ? 'hidden' : 'show') .
            '" data-column-id="' . $columnId . '" title="' . $translator->trans('Delete', [], Manager::CONTEXT) . '">';

        $glyph = new FontAwesomeGlyph('times', [], null, 'fas');

        $html[] = $glyph->render() . '</a>';
        $html[] = '</div>';
        $html[] = '<h3 class="panel-title">' . $translator->trans('EmptyColumnTitle', [], Manager::CONTEXT) . '</h3>';
        $html[] = '</div>';
        $html[] = '<div class="panel-body">';
        $html[] = $translator->trans('EmptyColumnBody', [], Manager::CONTEXT);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}