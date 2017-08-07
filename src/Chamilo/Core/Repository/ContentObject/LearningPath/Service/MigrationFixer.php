<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Fixes the learning paths that were not fully migrated due to corrupt data
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class MigrationFixer
{
    /**
     * @var LearningPathService
     */
    protected $learningPathService;

    /**
     * @var TreeNodeDataService
     */
    protected $treeNodeDataService;

    /**
     * @var ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * Cache for the sections that are converted from learning paths
     *
     * @var Section[]
     */
    protected $sectionFromLearningPathCache;

    /**
     * @param LearningPathService $learningPathService
     * @param TreeNodeDataService $treeNodeDataService
     * @param ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        LearningPathService $learningPathService, TreeNodeDataService $treeNodeDataService,
        ContentObjectRepository $contentObjectRepository
    )
    {
        ini_set('memory_limit', - 1);

        $this->learningPathService = $learningPathService;
        $this->treeNodeDataService = $treeNodeDataService;
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * Migrates the old learning paths to the new structure
     *
     * @param OutputInterface $output
     */
    public function migrateLearningPaths(OutputInterface $output)
    {
        $learningPathIds = ['1087652'];

        foreach ($learningPathIds as $learningPathId)
        {
            $output->writeln(sprintf('[%s] Fixing learning path', $learningPathId));

            /** @var LearningPath $learningPath */
            $learningPath = $this->contentObjectRepository->findById($learningPathId);

            $learningPathTree = $this->learningPathService->getTree($learningPath);
            $rootNode = $learningPathTree->getRoot();
            $descendantNodes = $rootNode->getDescendantNodes();

            foreach ($descendantNodes as $descendantNode)
            {
                /** Remove first because this object will be recreated during copy action with section */
                $this->treeNodeDataService->deleteTreeNodeData($descendantNode->getTreeNodeData());

                $this->convertLearningPathToSection(
                    $output, $learningPath, $descendantNode, $descendantNode->getParentNode()->getTreeNodeData()
                );
            }
        }
    }

    /**
     * Converts a learning path to a section and recursively scans if the learning path contains
     * other (sub) learning paths to convert
     *
     * @param OutputInterface $output
     * @param LearningPath $rootLearningPath
     * @param TreeNode $subLearningPathTreeNode
     * @param TreeNodeData $parentTreeNodeData
     */
    protected function convertLearningPathToSection(
        OutputInterface $output, LearningPath $rootLearningPath,
        TreeNode $subLearningPathTreeNode, TreeNodeData $parentTreeNodeData
    )
    {
        $subLearningPath = $subLearningPathTreeNode->getContentObject();
        if (!$subLearningPath instanceof LearningPath)
        {
            return;
        }

        $section = $this->getOrCreateSectionForLearningPath($subLearningPath);

        $output->writeln(
            sprintf(
                '[%s] Found child LearningPath. Converting LearningPath %s to a new Section %s',
                $rootLearningPath->getId(), $subLearningPath->getId(), $section->getId()
            )
        );

        $sectionTreeNodeData = $subLearningPathTreeNode->getTreeNodeData();
        $oldDisplayOrder = $sectionTreeNodeData->getDisplayOrder();

        $sectionTreeNodeData->setLearningPathId((int) $rootLearningPath->getId());
        $sectionTreeNodeData->setParentTreeNodeDataId((int) $parentTreeNodeData->getId());
        $sectionTreeNodeData->setContentObjectId((int) $section->getId());
        $sectionTreeNodeData->setId(0);

        $this->treeNodeDataService->createTreeNodeData($sectionTreeNodeData);

        $sectionTreeNodeData->setDisplayOrder($oldDisplayOrder);
        $this->treeNodeDataService->updateTreeNodeData($sectionTreeNodeData);

        $output->writeln(
            sprintf(
                '[%s] Created new TreeNodeData %s for Section %s', $rootLearningPath->getId(),
                $sectionTreeNodeData->getId(), $section->getId()
            )
        );

        $subLearningPathTree = $this->learningPathService->getTree($subLearningPath);
        $subLearningPathRootNode = $subLearningPathTree->getRoot();
        $subLearningPathDescendantNodes = $subLearningPathRootNode->getDescendantNodes();

        foreach ($subLearningPathDescendantNodes as $subLearningPathDescendantNode)
        {
            $treeNodeData = $subLearningPathDescendantNode->getTreeNodeData();

            $treeNodeData->setLearningPathId((int) $rootLearningPath->getId());
            $treeNodeData->setParentTreeNodeDataId((int) $sectionTreeNodeData->getId());
            $treeNodeData->setId(0);

            $this->treeNodeDataService->createTreeNodeData($treeNodeData);

            $output->writeln(
                sprintf(
                    '[%s] Created new TreeNodeData %s for child ContentObject %s',
                    $rootLearningPath->getId(), $treeNodeData->getId(),
                    $subLearningPathDescendantNode->getContentObject()->getId()
                )
            );

            $this->convertLearningPathToSection(
                $output, $rootLearningPath, $subLearningPathDescendantNode, $treeNodeData
            );
        }
    }

    /**
     * Creates or retrieves a section from the cache for the given learning path
     *
     * @param LearningPath $learningPath
     *
     * @return Section
     *
     * @throws \Exception
     */
    protected function getOrCreateSectionForLearningPath(LearningPath $learningPath)
    {
        if (!array_key_exists($learningPath->getId(), $this->sectionFromLearningPathCache))
        {
            $section = new Section();

            $section->set_title($learningPath->get_title());
            $section->set_description($learningPath->get_description());
            $section->set_creation_date($learningPath->get_creation_date());
            $section->set_owner_id($learningPath->get_owner_id());

            if (!$this->contentObjectRepository->create($section))
            {
                throw new \Exception('Could not create a new section');
            }

            echo "Create Section " . $section->getId() . PHP_EOL;

            $this->sectionFromLearningPathCache[$learningPath->getId()] = $section;
        }

        return $this->sectionFromLearningPathCache[$learningPath->getId()];
    }
}