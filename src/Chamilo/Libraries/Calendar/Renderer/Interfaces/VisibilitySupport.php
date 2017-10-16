<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

/**
 * An interface which forces the implementing Application to provide a given set of methods
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
interface VisibilitySupport
{

    /**
     * Check whether the given source is visible for the user
     *
     * @param string $source
     * @param integer $userIdentifier
     * @return boolean
     */
    public function isSourceVisible($source, $userIdentifier = null);

    /**
     * Return the additional Application data needed for the storage of the Visibility instance
     *
     * @return string[]
     */
    public function getVisibilityData();

    /**
     * Return the executable application containing the VisibilityComponent which will be called when setting a source
     * visible or invisible
     *
     * @return string
     */
    public function getVisibilityContext();
}