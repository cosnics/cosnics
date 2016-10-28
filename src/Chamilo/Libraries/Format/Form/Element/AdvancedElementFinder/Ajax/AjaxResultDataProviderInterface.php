<?php

namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax;

use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;

/**
 * Helper class to build an ajax result for an advanced element finder ajax feed
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AjaxResultDataProviderInterface
{
    /**
     * Generates the elements for the advanced element finder
     *
     * @param AdvancedElementFinderElements $advancedElementFinderElements
     */
    public function generateElements(AdvancedElementFinderElements $advancedElementFinderElements);

    /**
     * Returns the number of total elements (without the offset)
     *
     * @return mixed
     */
    public function getTotalNumberOfElements();
}