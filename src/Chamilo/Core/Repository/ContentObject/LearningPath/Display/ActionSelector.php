<?php

namespace Chamilo\Core\Repository\ContentObject\LearningPath\Display;

use Chamilo\Core\Repository\Selector\Renderer\SubButtonTypeSelectorRenderer;
use Chamilo\Core\Repository\Selector\TypeSelectorFactory;
use Chamilo\Libraries\Format\Structure\ActionBar\SplitDropdownButton;

/**
 * Extension on the ActionSelector to change some of the default behavior for the action selector
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ActionSelector extends \Chamilo\Core\Repository\Viewer\ActionSelector
{
    /**
     * Returns the dropdown button
     *
     * @param string $label
     * @param mixed $image
     *
     * @return SplitDropdownButton
     */
    public function getDropdownButton($label, $image)
    {
        return $this->getSingleCreationOptionDropdownButton($label, $image);
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Selector\TypeSelector
     */
    public function getTypeSelector()
    {
        if (!isset($this->typeSelector))
        {
            $typeSelectorFactory = new TypeSelectorFactory(
                $this->getAllowedContentObjectTypes(),
                $this->getUserIdentifier(), TypeSelectorFactory::MODE_FLAT_LIST, false
            );
            $this->typeSelector = $typeSelectorFactory->getTypeSelector();
        }

        return $this->typeSelector;
    }

//    /**
//     *
//     * @return \Chamilo\Core\Repository\Selector\Renderer\SubButtonTypeSelectorRenderer
//     */
//    public function getSubButtonTypeSelectorRenderer()
//    {
//        if (!isset($this->subButtonTypeSelectorRenderer))
//        {
//            $typeSelector = $this->getTypeSelector();
//            $createParameters = $this->getParameters();
//            $createParameters[\Chamilo\Core\Repository\Viewer\Manager::PARAM_ACTION] =
//                \Chamilo\Core\Repository\Viewer\Manager::ACTION_CREATOR;
//
//            $typeSelector->removeTypeSelectorItemByIndex(0);
//
//            $this->subButtonTypeSelectorRenderer = new SubButtonTypeSelectorRenderer(
//                $this->getApplication(),
//                $typeSelector,
//                $createParameters
//            );
//        }
//
//        return $this->subButtonTypeSelectorRenderer;
//    }
}