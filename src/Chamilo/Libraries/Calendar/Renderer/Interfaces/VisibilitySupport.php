<?php
namespace Chamilo\Libraries\Calendar\Renderer\Interfaces;

/**
 * An interface which forces the implementing Application to provide a given set of methods
 * 
 * @package libraries\calendar\renderer
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
     * @return boolean
     */
    public function is_calendar_renderer_source_visible($source);

    /**
     * Return the additional Application data needed for the storage of the Visibility instance
     * 
     * @return string[]
     */
    public function get_calendar_renderer_visibility_data();
}