<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
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
     * PrintableFormatRenderer constructor.
     *
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService $automaticNumberingService
     * @param \Twig\Environment $twig
     */
    public function __construct(
        \Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService $automaticNumberingService,
        \Twig\Environment $twig
    )
    {
        $this->automaticNumberingService = $automaticNumberingService;
        $this->twig = $twig;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree $tree
     *
     * @param string $viewerUrl
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render(Tree $tree, string $viewerUrl)
    {
        return $this->twig->render(
            'Chamilo\Core\Repository\ContentObject\LearningPath\Display:PrintableFormat.html.twig',
            [
                'TREE_TITLES' => $this->prepareTreeTitlesForRendering($tree->getRoot()),
                'LEARNING_PATH_TITLE' => $tree->getRoot()->getContentObject()->get_title(),
                'TREE_NODES' => $this->prepareTreeNodesForRendering($tree, $viewerUrl)
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

            if($treeNode->hasChildNodes())
            {
                $childTitles = $this->prepareTreeTitlesForRendering($treeNode);
            }

            $treeTitles[$this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($treeNode)] = $childTitles;
        }

        return $treeTitles;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree $tree
     *
     * @param string $viewerUrl
     *
     * // TODO: volgtijdelijkheid blokkeren
     * // TODO: inline resources
     * // TODO:
     * @return array
     */
    protected function prepareTreeNodesForRendering(Tree $tree, string $viewerUrl)
    {
        $treeNodesContent = [];

        foreach($tree->getTreeNodes() as $treeNode)
        {
            $treeNodeViewUrl = str_replace('__TREE_NODE_ID__' , $treeNode->getId(), $viewerUrl);
            $qrCode = new QrCode($treeNodeViewUrl);
            $qrCode->setWriterByName('png');
            $qrCode->setSize('200');

            $treeNodesContent[] = [
                'title' => $this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($treeNode),
                'content' => $treeNode->getContentObject()->get_description(),
                'url' => str_replace('__TREE_NODE_ID__' , $treeNode->getId(), $viewerUrl),
                'qr_code' => $base64 = 'data:image/png;base64,' . base64_encode($qrCode->writeString())
            ];
        }

        return $treeNodesContent;
    }
}