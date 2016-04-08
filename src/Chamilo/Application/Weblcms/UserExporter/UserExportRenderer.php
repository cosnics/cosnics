<?php
namespace Chamilo\Application\Weblcms\UserExporter;

/**
 * Interface which defines a renderer to render the exported users array
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserExportRenderer
{

    /**
     * Renders the exported users
     * 
     * @param array $headers
     * @param array $users
     *
     * @return mixed
     */
    public function render(array $headers, array $users);
}