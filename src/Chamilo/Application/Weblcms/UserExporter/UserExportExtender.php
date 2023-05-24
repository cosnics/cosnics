<?php
namespace Chamilo\Application\Weblcms\UserExporter;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface which defines an extender for the user exporter, which makes it possible to extend the data of an exported
 * user
 *
 * @author  Sven Vanpoucke - Hogeschool Gent
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface UserExportExtender
{

    public function export_headers(string $courseIdentifier): array;

    public function export_user(string $courseIdentifier, User $user): array;
}