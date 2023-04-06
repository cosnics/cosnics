<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Test\Unit\Service;

use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\TreeNodeData;
use Chamilo\Core\Repository\ContentObject\LearningPath\Test\Helper\TreeTestDataGenerator;
use Chamilo\Libraries\Architecture\Test\TestCases\ChamiloTestCase;

/**
 * Tests the AutomaticNumberingService class
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class AutomaticNumberingServiceTest extends ChamiloTestCase
{

    /**
     *
     * @var AutomaticNumberingService
     */
    protected $automaticNumberingService;

    /**
     *
     * @var Tree
     */
    protected $tree;

    /**
     *
     * @var LearningPath[] | Section[] | Page[] | ContentObject[]
     */
    protected $contentObjects;

    /**
     *
     * @var TreeNodeData[]
     */
    protected $treeNodesData;

    /**
     *
     * @var TreeNode[]
     */
    protected $treeNodes;

    /**
     * Setup before each test
     * - Learning Path A - ID: 1
     * - Section A - ID: 2
     * - Page 1 - ID: 6
     * - Section B - ID: 3
     * - Section C - ID: 4
     * - Section D - ID: 5
     * - Page 2 - ID: 7
     */
    protected function setUp(): void    {
        $treeTestDataGenerator = new TreeTestDataGenerator();

        $this->tree = $treeTestDataGenerator->getTree();
        $this->contentObjects = $treeTestDataGenerator->getContentObjects();
        $this->treeNodesData = $treeTestDataGenerator->getTreeNodesData();
        $this->treeNodes = $treeTestDataGenerator->getTreeNodes();

        $this->automaticNumberingService = new AutomaticNumberingService();

        /** @var LearningPath $learningPath */
        $learningPath = $this->contentObjects[1];
        $learningPath->setAutomaticNumbering(LearningPath::AUTOMATIC_NUMBERING_DIGITS);
    }

    public function testGetAutomaticNumberingForTreeNode()
    {
        $this->assertEquals(
            '1.1.',
            $this->automaticNumberingService->getAutomaticNumberingForTreeNode($this->treeNodes[6]));
    }

    public function testGetAutomaticNumberedTitleForTreeNode()
    {
        $treeNode = $this->treeNodes[7];
        $treeNode->getContentObject()->set_title('Test Title 001');

        $this->assertEquals(
            '3.1.1. Test Title 001',
            $this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($this->treeNodes[7]));
    }

    public function testGetAutomaticNumberingForTreeNodeWithoutAutomaticNumbering()
    {
        /** @var LearningPath $learningPath */
        $learningPath = $this->contentObjects[1];
        $learningPath->setAutomaticNumbering(LearningPath::AUTOMATIC_NUMBERING_NONE);

        $this->assertNull($this->automaticNumberingService->getAutomaticNumberingForTreeNode($this->treeNodes[6]));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testGetAutomaticNumberingForTreeNodeNotInTree()
    {
        $treeNodeData = new TreeNodeData();
        $treeNodeData->setId(50);
        $treeNode = new TreeNode($this->tree, null, $treeNodeData);

        $this->automaticNumberingService->getAutomaticNumberingForTreeNode($treeNode);
    }
}