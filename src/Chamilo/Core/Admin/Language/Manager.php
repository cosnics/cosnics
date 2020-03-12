<?php
namespace Chamilo\Core\Admin\Language;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
abstract class Manager extends Application
{
    const ACTION_EXPORT = 'Exporter';
    const ACTION_IMPORT = 'Importer';

    const DEFAULT_ACTION = self::ACTION_IMPORT;

    const PARAM_ACTION = 'language_action';
}
