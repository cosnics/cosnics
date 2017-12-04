<?php
namespace Chamilo\Libraries\Calendar\Interfaces;

use Chamilo\Libraries\Calendar\Event\EventSource;

/**
 *
 * @package Chamilo\Libraries\Calendar\Interfaces
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface VisibilitySupport
{

    /**
     * Check whether the given source is visible for the user
     *
     * @param \Chamilo\Libraries\Calendar\Event\EventSource $source
     * @return boolean
     */
    public function isSourceVisible(EventSource $source);

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