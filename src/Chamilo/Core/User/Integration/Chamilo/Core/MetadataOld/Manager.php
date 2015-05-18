<?php
namespace Chamilo\Core\User\Integration\Chamilo\Core\MetadataOld;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * The manager of this package.
 *
 * @package user\integration\core\metadata
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'metadata_action';
}
