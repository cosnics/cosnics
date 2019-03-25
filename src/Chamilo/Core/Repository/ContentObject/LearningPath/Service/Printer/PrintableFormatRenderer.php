<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service\Printer;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Page\Storage\DataClass\Page;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\User\Storage\DataClass\User;
use Endroid\QrCode\QrCode;

/**
 * Class PrintableFormatRenderer
 *
 * @package Chamilo\Core\Repository\ContentObject\LearningPath\Service
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class PrintableFormatRenderer
{
    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService
     */
    protected $automaticNumberingService;

    /**
     * @var \Twig\Environment
     */
    protected $twig;
    /**
     * @var \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Printer\PrintableResourceRenderer
     */
    private $printableResourceRenderer;

    /**
     * PrintableFormatRenderer constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService $automaticNumberingService
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Printer\PrintableResourceRenderer $printableResourceRenderer
     * @param \Twig\Environment $twig
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService $automaticNumberingService,
        PrintableResourceRenderer $printableResourceRenderer,
        \Twig\Environment $twig
    )
    {
        $this->automaticNumberingService = $automaticNumberingService;
        $this->twig = $twig;
        $this->printableResourceRenderer = $printableResourceRenderer;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree $tree
     * @param string $viewerUrl
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService $trackingService
     * @param bool $canAuditLearningPath
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(
        LearningPath $learningPath, User $user, Tree $tree, string $viewerUrl, TrackingService $trackingService,
        bool $canAuditLearningPath, string $contextTitle
    )
    {
        return $this->twig->render(
            'Chamilo\Core\Repository\ContentObject\LearningPath\Display:PrintableFormat.html.twig',
            [
                'CONTEXT_TITLE' => $contextTitle,
                'TREE_TITLES' => $this->prepareTreeTitlesForRendering($tree->getRoot()),
                'LEARNING_PATH_TITLE' => $tree->getRoot()->getContentObject()->get_title(),
                'TREE_NODES' => $this->prepareTreeNodesForRendering(
                    $learningPath, $user, $tree, $viewerUrl, $trackingService, $canAuditLearningPath
                )
            ]
        );
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode $parentNode
     *
     * @return array
     */
    protected function prepareTreeTitlesForRendering(TreeNode $parentNode)
    {
        $treeTitles = [];

        foreach ($parentNode->getChildNodes() as $treeNode)
        {
            $childTitles = [];

            if ($treeNode->hasChildNodes())
            {
                $childTitles = $this->prepareTreeTitlesForRendering($treeNode);
            }

            $treeTitles[$this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($treeNode)] =
                $childTitles;
        }

        return $treeTitles;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath $learningPath
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree $tree
     * @param string $viewerUrl
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\Tracking\TrackingService $trackingService
     * @param bool $canAuditLearningPath
     *
     * @return array
     */
    protected function prepareTreeNodesForRendering(
        LearningPath $learningPath, User $user, Tree $tree, string $viewerUrl, TrackingService $trackingService,
        bool $canAuditLearningPath
    )
    {
        $treeNodesContent = [];

        foreach ($tree->getTreeNodes() as $treeNode)
        {
            $treeNodeViewUrl = str_replace('__TREE_NODE_ID__', $treeNode->getId(), $viewerUrl);
            $qrCode = new QrCode($treeNodeViewUrl);
            $qrCode->setWriterByName('png');
            $qrCode->setSize('200');

            $description = $this->printableResourceRenderer->renderResourcesInContent(
                $treeNode->getContentObject()->get_description()
            );

            $canViewContentObjectOffline = $this->canViewContentObjectOffline($treeNode->getContentObject());

            $treeNodesContent[] = [
                'title' => $this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($treeNode),
                'type' => $treeNode->getContentObject()->get_type_string(),
                'content' => $description,
                'url' => str_replace('__TREE_NODE_ID__', $treeNode->getId(), $viewerUrl),
                'can_view_offline' => $canViewContentObjectOffline,
                'blocked' => !$canAuditLearningPath &&
                    $trackingService->isCurrentTreeNodeBlocked($learningPath, $user, $treeNode),
                'qr_code' => $base64 = 'data:image/png;base64,' . base64_encode($qrCode->writeString())
            ];
        }

        return $treeNodesContent;
    }

    /**
     * @param \Chamilo\Core\Repository\Storage\DataClass\ContentObject $contentObject
     *
     * @return bool
     */
    protected function canViewContentObjectOffline(ContentObject $contentObject)
    {
        return $contentObject instanceof LearningPath ||
            $contentObject instanceof Page ||
            $contentObject instanceof Section;
    }
}