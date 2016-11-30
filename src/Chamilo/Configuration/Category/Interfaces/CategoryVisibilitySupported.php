<?php
namespace Chamilo\Configuration\Category\Interfaces;

/**
 * Marker interface for categories that support changing their visibility.
 * 
 * @author Tom Goethals
 */
interface CategoryVisibilitySupported
{

    public function set_visibility($visibility);

    /**
     *
     * @return True if the category is visible for everyone.
     */
    public function get_visibility();

    /**
     * Simply toggles the visibility true <-> false.
     */
    public function toggle_visibility();
}
