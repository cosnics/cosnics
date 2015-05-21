<?php
namespace Chamilo\Configuration\Category\Interfaces;

/**
 * Marker interface for category manager implementations that support the impact view
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ImpactViewSupport
{

    public function render_impact_view($selected_category_ids = array());

    public function has_impact($selected_category_ids = array());
}
