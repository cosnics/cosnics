<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form;

use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Service\AutomaticNumberingService;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Libraries\Format\Form\FormValidator;

/**
 * Form to display the possible places for direct movement
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DirectMoverForm extends FormValidator
{
    public const PARAM_DISPLAY_ORDER = 'displayOrder';

    public const PARAM_TITLE = 'title';

    /**
     * @var AutomaticNumberingService
     */
    protected $automaticNumberingService;

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * DirectMoverForm constructor.
     *
     * @param string $action
     * @param Tree $tree
     * @param TreeNode $treeNode
     * @param AutomaticNumberingService $automaticNumberingService
     */
    public function __construct(
        $action, Tree $tree, TreeNode $treeNode, AutomaticNumberingService $automaticNumberingService
    )
    {
        parent::__construct('direct_mover_form', self::FORM_METHOD_POST, $action);

        $this->tree = $tree;
        $this->treeNode = $treeNode;
        $this->automaticNumberingService = $automaticNumberingService;

        $this->buildForm();
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $nodes = $this->tree->getTreeNodes();
        $descendants = $this->treeNode->getDescendantNodes();

        $parents = [];
        $positionsPerParent = [];

        foreach ($nodes as $node)
        {
            if ($node === $this->treeNode || in_array($node, $descendants))
            {
                continue;
            }

            $contentObject = $node->getContentObject();
            if ($contentObject instanceof Section || $node->isRootNode())
            {
                $title = $this->automaticNumberingService->getAutomaticNumberedTitleForTreeNode($node);

                $title = str_repeat('--', count($node->getParentNodes())) . ' ' . $title;
                $parents[$node->getId()] = $title;

                $displayOrder = 1;

                $positionsPerParent[$node->getId()][] = [
                    self::PARAM_TITLE => $this->getTranslation('FirstItem'),
                    self::PARAM_DISPLAY_ORDER => $displayOrder
                ];

                $displayOrder ++;

                $children = $node->getChildNodes();
                foreach ($children as $child)
                {
                    if ($child == $this->treeNode || in_array($child, $descendants))
                    {
                        continue;
                    }

                    $positionsPerParent[$node->getId()][] = [
                        self::PARAM_TITLE => $this->getTranslation(
                            'AfterContentObject', ['CONTENT_OBJECT' => $child->getContentObject()->get_title()]
                        ),
                        self::PARAM_DISPLAY_ORDER => $displayOrder
                    ];

                    $displayOrder ++;
                }
            }
        }

        $this->addElement(
            'select', Manager::PARAM_PARENT_ID, $this->getTranslation('NewParent'), $parents,
            ['id' => 'mover-parent-id', 'class' => 'form-control']
        );
        $this->addElement(
            'select', Manager::PARAM_DISPLAY_ORDER, $this->getTranslation('NewPosition'), [],
            ['id' => 'mover-display-order', 'class' => 'form-control']
        );
        $this->addElement(
            'style_submit_button', self::PARAM_SUBMIT, $this->getTranslation('Move'), ['style' => 'margin-top: 20px;']
        );

        $this->addElement(
            'html',
            '<div id="positions-per-parent" data-positions="' . htmlspecialchars(json_encode($positionsPerParent)) .
            '"></div>'
        );

        $this->addElement(
            'html', $this->getResourceManager()->getResourceHtml(
            $this->getWebPathBuilder()->namespaceToFullPath(Manager::CONTEXT) . 'Resources/Javascript/DirectMover.js'
        )
        );
    }
}