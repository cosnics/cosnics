<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Core\Home\Architecture\Interfaces\AnonymousBlockInterface;
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

    protected BlockRendererFactory $blockRendererFactory;

    protected HomeService $homeService;

    protected Translator $translator;

    public function __construct(
        HomeService $homeService, Translator $translator, BlockRendererFactory $blockRendererFactory
    )
    {
        $this->homeService = $homeService;
        $this->translator = $translator;
        $this->blockRendererFactory = $blockRendererFactory;
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Exception\DataClassNoResultException
     * @throws \QuickformException
     */
    public function render(Element $column, bool $isGeneralMode = false, ?User $user = null): string
    {
        $html = [];

        $html[] = '<div class="col-xs-12 col-md-' . $column->getWidth() . ' portal-column" data-tab-id="' .
            $column->getParentId() . '" data-element-id="' . $column->getId() . '" data-element-width="' .
            $column->getWidth() . '">';

        $blocks = $this->getHomeService()->findElementsByTypeUserAndParentIdentifier(
            Element::TYPE_BLOCK, $user, $column->getId()
        );

        foreach ($blocks as $block)
        {
            $blockRenderer = $this->getBlockRendererFactory()->getRenderer($block);

            if ($blockRenderer instanceof AnonymousBlockInterface || $blockRenderer->isVisible($block, $user))
            {
                $html[] = $blockRenderer->render($block, $isGeneralMode, $user);
            }
        }

        $hasMultipleColumns = $this->getHomeService()->tabByUserAndIdentifierHasMultipleColumns(
            $column->getParentId(), $user
        );

        $html[] = $this->renderEmptyColumn($column->getId(), (count($blocks) > 0), !$hasMultipleColumns);

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getBlockRendererFactory(): BlockRendererFactory
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