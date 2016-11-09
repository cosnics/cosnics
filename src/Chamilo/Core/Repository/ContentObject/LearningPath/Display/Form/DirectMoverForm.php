<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display\Form;

use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPath;
use Chamilo\Core\Repository\Common\Path\ComplexContentObjectPathNode;
use Chamilo\Core\Repository\ContentObject\LearningPath\Display\Manager;
use Chamilo\Core\Repository\ContentObject\LearningPath\Storage\DataClass\LearningPath;
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
     * @var ComplexContentObjectPath
     */
    protected $complexContentObjectPath;

    /**
     * @var ComplexContentObjectPathNode
     */
    protected $currentNode;

    /**
     * DirectMoverForm constructor.
     *
     * @param string $action
     * @param ComplexContentObjectPath $complexContentObjectPath
     * @param ComplexContentObjectPathNode $currentNode
     */
    public function __construct(
        $action, ComplexContentObjectPath $complexContentObjectPath, ComplexContentObjectPathNode $currentNode
    )
    {
        parent::__construct('direct_mover_form', 'post', $action);

        $this->complexContentObjectPath = $complexContentObjectPath;
        $this->currentNode = $currentNode;

        $this->buildForm();
    }

    /**
     * Builds the form
     */
    protected function buildForm()
    {
        $nodes = $this->complexContentObjectPath->get_nodes();
        $descendants = $this->currentNode->get_descendants();

        $parents = array();
        $positionsPerParent = array();

        foreach ($nodes as $node)
        {
            if ($node == $this->currentNode || in_array($node, $descendants))
            {
                continue;
            }

            /** @var ComplexContentObjectPathNode $node */
            $contentObject = $node->get_content_object();
            if ($contentObject instanceof LearningPath)
            {
                $title = str_repeat('--', count($node->get_parents())) . ' ' . $contentObject->get_title();
                $parents[$node->get_id()] = $title;

                $displayOrder = 1;

                $positionsPerParent[$node->get_id()][] = array(
                    self::PARAM_TITLE => $this->getTranslation('FirstItem'),
                    self::PARAM_DISPLAY_ORDER => $displayOrder
                );

                $displayOrder ++;

                $children = $node->get_children();
                foreach ($children as $child)
                {
                    if ($child == $this->currentNode || in_array($child, $descendants))
                    {
                        continue;
                    }

                    $positionsPerParent[$node->get_id()][] = array(
                        self::PARAM_TITLE => $this->getTranslation(
                            'AfterContentObject',
                            array('CONTENT_OBJECT' => $child->get_content_object()->get_title())
                        ),
                        self::PARAM_DISPLAY_ORDER => $displayOrder
                    );

                    $displayOrder ++;
                }
            }
        }

        $this->addElement(
            'select', Manager::PARAM_PARENT_ID, $this->getTranslation('NewParent'), $parents,
            array('id' => 'mover-parent-id', 'class' => 'form-control')
        );
        $this->addElement(
            'select', Manager::PARAM_DISPLAY_ORDER, $this->getTranslation('NewPosition'), array(),
            array('id' => 'mover-display-order', 'class' => 'form-control')
        );
        $this->addElement(
            'style_submit_button', self::PARAM_SUBMIT, $this->getTranslation('Move'),
            array('style' => 'margin-top: 20px;')
        );

        $this->addElement(
            'html', '<div id="positions-per-parent" data-positions="' .
            htmlspecialchars(json_encode($positionsPerParent)) . '"></div>'
        );

        $this->addElement(
            'html', ResourceManager::getInstance()->get_resource_html(
            Path::getInstance()->namespaceToFullPath(Manager::context(), true) . 'Resources/Javascript/DirectMover.js'
        )
        );
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