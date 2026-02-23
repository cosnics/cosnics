<?php
namespace Chamilo\Core\Home\UserInterface\HomeRenderer;

use Chamilo\Core\Home\Architecture\Domain\BlockRendererRegistry;
use Chamilo\Core\Home\Manager;
use Chamilo\Core\Home\Service\HomeService;
use Chamilo\Core\Home\Storage\DataClass\Element;
use Chamilo\Core\User\Storage\DataClass\User;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Core\Home\UserInterface\HomeRenderer
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
class ColumnRenderer
{
    protected BlockRendererRegistry $blockRendererFactory;

    protected HomeService $homeService;

    protected Translator $translator;

    public function __construct(
        HomeService $homeService, Translator $translator, BlockRendererRegistry $blockRendererFactory
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

        $html[] = '<div class="col-xs-12 col-md-' . $column->getWidth() . ' " data-tab-id="' . $column->getParentId() .
            '" data-element-id="' . $column->getId() . '" data-element-width="' . $column->getWidth() . '">';

        $blocks = $this->getHomeService()->findElementsByTypeAndParentIdentifier(
            Element::TYPE_BLOCK, $column->getId()
        );

        foreach ($blocks as $block) {
            $blockRenderer = $this->getBlockRendererFactory()->getRendererForElement($block);
            $html[] = $blockRenderer->render($block, $user);
        }

        $html[] = $this->renderEmptyColumn($blocks->isEmpty());

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getBlockRendererFactory(): BlockRendererRegistry
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

    public function renderEmptyColumn(bool $isEmpty = false): string
    {
        $translator = $this->getTranslator();

        $html = [];

        $html[] = '<div class="card text-bg-warning mb-3 ' . ($isEmpty ? '' : 'd-none') . '">';
        $html[] = '<div class="card-header">';
        $html[] = '<h5 class="panel-title">' . $translator->trans('EmptyColumnTitle', [], Manager::CONTEXT) . '</h5>';
        $html[] = '</div>';
        $html[] = '<div class="card-body">';
        $html[] = $translator->trans('EmptyColumnBody', [], Manager::CONTEXT);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}