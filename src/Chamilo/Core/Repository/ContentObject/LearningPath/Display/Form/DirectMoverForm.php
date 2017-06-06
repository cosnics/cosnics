<?php
namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\Tree;
use Chamilo\Core\Repository\ContentObject\LearningPath\Domain\TreeNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
use Chamilo\Core\Repository\ContentObject\Section\Storage\DataClass\Section;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Form\FormValidator;
use Chamilo\Libraries\Format\Utilities\ResourceManager;
use Chamilo\Libraries\Platform\Translation;

/**
 * Form to display the possible places for direct movement
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DirectMoverForm extends FormValidator
{
    const PARAM_TITLE = 'title';
    const PARAM_DISPLAY_ORDER = 'displayOrder';

    /**
     *
     * @var Tree
     */
    protected $tree;

    /**
     *
     * @var TreeNode
     */
    protected $treeNode;

    /**
     * DirectMoverForm constructor.
     * 
     * @param string $action
     * @param Tree $tree
     * @param TreeNode $treeNode
     */
    public function __construct($action, Tree $tree,
        TreeNode $treeNode)
    {
        parent::__construct('direct_mover_form', 'post', $action);
        
        $this->tree = $tree;
        $this->treeNode = $treeNode;
        
        $this->buildForm();
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $nodes = $this->tree->getTreeNodes();
        $descendants = $this->treeNode->getDescendantNodes();
        
        $parents = array();
        $positionsPerParent = array();
        
        foreach ($nodes as $node)
        {
            if ($node == $this->treeNode || in_array($node, $descendants))
            {
                continue;
            }
            
            $contentObject = $node->getContentObject();
            if ($contentObject instanceof Section || $node->isRootNode())
            {
                $title = str_repeat('--', count($node->getParentNodes())) . ' ' . $contentObject->get_title();
                $parents[$node->getId()] = $title;
                
                $displayOrder = 1;
                
                $positionsPerParent[$node->getId()][] = array(
                    self::PARAM_TITLE => $this->getTranslation('FirstItem'), 
                    self::PARAM_DISPLAY_ORDER => $displayOrder);
                
                $displayOrder ++;
                
                $children = $node->getChildNodes();
                foreach ($children as $child)
                {
                    if ($child == $this->treeNode || in_array($child, $descendants))
                    {
                        continue;
                    }
                    
                    $positionsPerParent[$node->getId()][] = array(
                        self::PARAM_TITLE => $this->getTranslation(
                            'AfterContentObject', 
                            array('CONTENT_OBJECT' => $child->getContentObject()->get_title())),
                        self::PARAM_DISPLAY_ORDER => $displayOrder);
                    
                    $displayOrder ++;
                }
            }
        }
        
        $this->addElement(
            'select', 
            Manager::PARAM_PARENT_ID, 
            $this->getTranslation('NewParent'), 
            $parents, 
            array('id' => 'mover-parent-id', 'class' => 'form-control'));
        $this->addElement(
            'select', 
            Manager::PARAM_DISPLAY_ORDER, 
            $this->getTranslation('NewPosition'), 
            array(), 
            array('id' => 'mover-display-order', 'class' => 'form-control'));
        $this->addElement(
            'style_submit_button', 
            self::PARAM_SUBMIT, 
            $this->getTranslation('Move'), 
            array('style' => 'margin-top: 20px;'));
        
        $this->addElement(
            'html', 
            '<div id="positions-per-parent" data-positions="' . htmlspecialchars(json_encode($positionsPerParent)) .
                 '"></div>');
        
        $this->addElement(
            'html', 
            ResourceManager::getInstance()->get_resource_html(
                Path::getInstance()->namespaceToFullPath(Manager::context(), true) .
                     'Resources/Javascript/DirectMover.js'));
    }

    /**
     * Helper functionality for translations
     * 
     * @param string $variable
     * @param array $parameters
     *
     * @return string
     */
    protected function getTranslation($variable, $parameters = array())
    {
        return Translation::getInstance()->getTranslation($variable, $parameters, Manager::context());
    }
}