<?php
namespace Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax;

use Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements;

/**
 * Helper class to build an ajax result for an advanced element finder ajax feed
 *
 * @package Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\Ajax
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface AjaxResultDataProviderInterface
{

    /**
     * Generates the elements for the advanced element finder
     *
     * @param \Chamilo\Libraries\Format\Form\Element\AdvancedElementFinder\AdvancedElementFinderElements $advancedElementFinderElements
     */
    public function generateElements(AdvancedElementFinderElements $advancedElementFinderElements);

    /**
     * Returns the number of total elements (without the offset)
     *
     * @return integer
     */
    public function getTotalNumberOfElements();
}