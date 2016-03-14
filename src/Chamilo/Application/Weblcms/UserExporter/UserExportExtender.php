<?php
namespace Chamilo\Application\Weblcms\UserExporter;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface which defines an extender for the user exporter, which makes it possible to extend the data of an exported
 * user
 * 
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface UserExportExtender
{

    /**
     * Exports additional headers
     * 
     * @return array
     */
    public function export_headers();

    /**
     * Exports additional data for a given user
     * 
     * @param User $user
     *
     * @return array
     */
    public function export_user(User $user);
}