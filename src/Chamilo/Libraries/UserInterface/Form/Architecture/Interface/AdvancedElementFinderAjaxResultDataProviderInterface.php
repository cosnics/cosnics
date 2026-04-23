<?php
namespace Chamilo\Libraries\UserInterface\Form\Architecture\Interface;

use Chamilo\Libraries\UserInterface\Form\Architecture\Domain\AdvancedElementFinder\AdvancedElementFinderElements;

/**
 * Helper class to build an ajax result for an advanced element finder ajax feed
 *
 * @package Chamilo\Libraries\UserInterface\Form\Architecture\Interface
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AdvancedElementFinderAjaxResultDataProviderInterface
{
    /**
     * @param \Chamilo\Libraries\UserInterface\Form\Architecture\Domain\AdvancedElementFinder\AdvancedElementFinderElements $advancedElementFinderElements
     */
    public function generateElements(AdvancedElementFinderElements $advancedElementFinderElements);

    /**
     * @return int
     */
    public function getTotalNumberOfElements(): int;
}