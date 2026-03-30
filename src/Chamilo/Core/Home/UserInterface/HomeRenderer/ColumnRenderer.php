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
readonly class ColumnRenderer
{
    public function __construct(
        protected HomeService $homeService, protected Translator $translator,
        protected BlockRendererRegistry $blockRendererFactory
    )
    {
    }

    /**
     * @throws \Chamilo\Libraries\Storage\Architecture\Exception\StorageMethodException
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\NoSuchClassException
     */
    public function render(Element $column, ?User $user = null): string
    {
        $html = [];

        $html[] = '<div class="col-12 col-md-' . $column->getWidth() . ' " data-tab-id="' . $column->getParentId() .
            '" data-element-id="' . $column->getId() . '" data-element-width="' . $column->getWidth() . '">';

        $blocks = $this->homeService->findElementsByTypeAndParentIdentifier(
            Element::TYPE_BLOCK, $column->getId()
        );

        foreach ($blocks as $block) {
            $blockRenderer = $this->blockRendererFactory->getRendererForElement($block);
            $html[] = $blockRenderer->render($block, $user);
        }

        $html[] = $this->renderEmptyColumn($blocks->isEmpty());

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function renderEmptyColumn(bool $isEmpty = false): string
    {
        $html = [];

        $html[] = '<div class="card text-bg-warning mb-3 ' . ($isEmpty ? '' : 'd-none') . '">';
        $html[] = '<div class="card-header">';
        $html[] =
            '<h5 class="panel-title">' . $this->translator->trans('EmptyColumnTitle', [], Manager::CONTEXT) . '</h5>';
        $html[] = '</div>';
        $html[] = '<div class="card-body">';
        $html[] = $this->translator->trans('EmptyColumnBody', [], Manager::CONTEXT);
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}