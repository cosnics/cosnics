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
 * DETECTION QUERY:
 *
 *  SELECT * FROM `repository_learning_path_tree_node_data` TND
    JOIN repository_content_object CO on CO.id = TND.content_object_id
    WHERE TND.learning_path_id <> TND.content_object_id AND CO.type LIKE '%LearningPath';
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
        $learningPathIds = [
            1087652, 279355, 691640, 882822, 882825, 1016831, 1043099, 1076360, 1076367, 1076380, 1076381, 1076386,
            1076388, 1076392, 1079962, 1083210, 1083240, 1083242, 1083959, 1083963, 1083965, 1085545, 1085623, 1086718,
            1088367, 1088372, 1088408, 1089779, 1089919, 1090041, 1090082, 1090992, 1090994, 1091029, 1091042, 1091140,
            1091200, 1091642, 1091953, 1092078, 1092081, 1092083, 1092423, 1092997, 1093606, 1094536, 1094576, 1094741,
            1094750, 1094752, 1268236, 1496937, 1528930, 1858877, 1858925, 1858988, 1859051, 1859090, 1859119, 1859152,
            1859191, 1859216, 1859217, 1860787, 1860821, 1860823, 1861010, 1861044, 1861046, 1861587, 1861621, 1861623,
            2327689
        ];

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

        $sectionTreeNodeData = $subLearningPathTreeNode->getTreeNodeData();
        $sectionTreeNodeData->remove_listener(0);

        /** Remove first because this object will be recreated during copy action with section */
        $this->treeNodeDataService->deleteTreeNodeData($sectionTreeNodeData);

        $section = $this->getOrCreateSectionForLearningPath($subLearningPath);

        $output->writeln(
            sprintf(
                '[%s] Found child LearningPath. Converting LearningPath %s to a new Section %s',
                $rootLearningPath->getId(), $subLearningPath->getId(), $section->getId()
            )
        );

        $sectionTreeNodeData->setLearningPathId((int) $rootLearningPath->getId());
        $sectionTreeNodeData->setParentTreeNodeDataId((int) $parentTreeNodeData->getId());
        $sectionTreeNodeData->setContentObjectId((int) $section->getId());
        $sectionTreeNodeData->setId(0);

        $this->treeNodeDataService->createTreeNodeData($sectionTreeNodeData);

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
            $treeNodeData->remove_listener(0);

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