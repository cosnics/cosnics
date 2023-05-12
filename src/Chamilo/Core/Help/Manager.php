<?php
namespace Chamilo\Core\Help;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * @package help.lib.help_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage his users.
 * For each functionality a component is
 * available.
 */
abstract class Manager extends Application
{
    public const ACTION_BROWSE_HELP_ITEMS = 'Browser';
    public const ACTION_UPDATE_HELP_ITEM = 'Updater';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_BROWSE_HELP_ITEMS;

    public const PARAM_HELP_ITEM = 'help_item';

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent::__construct($applicationConfiguration);

        $this->checkAuthorization(Manager::CONTEXT);
    }

    public function count_help_items($condition)
    {
        return DataManager::count(HelpItem::class, new DataClassCountParameters($condition));
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    public function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return DataManager::retrieves(
            HelpItem::class, new DataClassRetrievesParameters($condition, $count, $offset, $order_property)
        );
    }
}
