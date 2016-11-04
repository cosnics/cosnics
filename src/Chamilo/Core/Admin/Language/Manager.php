<?php
namespace Chamilo\Core\Admin\Language;

use Chamilo\Libraries\Architecture\Application\Application;

/**
 * $Id: package_manager.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 *
 * @package admin.lib.package_manager
 * @author Hans De Bisschop
 */
abstract class Manager extends Application
{
    const PARAM_ACTION = 'language_action';
    const ACTION_BROWSE = 'Browser';
    const ACTION_EXPORT = 'Exporter';
    const ACTION_IMPORT = 'Importer';
    const DEFAULT_ACTION = self :: ACTION_BROWSE;
}
