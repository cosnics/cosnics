<?php
namespace Chamilo\Core\Help;

use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Configuration\PlatformSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;

/**
 * $Id: help_manager.class.php 226 2009-11-13 14:44:03Z chellee $
 *
 * @package help.lib.help_manager
 */

/**
 * A user manager provides some functionalities to the admin to manage his users.
 * For each functionality a component is
 * available.
 */
abstract class Manager extends Application
{
    const PARAM_HELP_ITEM = 'help_item';
    const ACTION_UPDATE_HELP_ITEM = 'Updater';
    const ACTION_BROWSE_HELP_ITEMS = 'Browser';
    const DEFAULT_ACTION = self :: ACTION_BROWSE_HELP_ITEMS;

    public function __construct(ApplicationConfigurationInterface $applicationConfiguration)
    {
        parent:: __construct($applicationConfiguration);

        $this->checkAuthorization(Manager::context());
    }

    public function count_help_items($condition)
    {
        return DataManager :: count(HelpItem :: class_name(), new DataClassCountParameters($condition));
    }

    public function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return DataManager :: retrieves(
            HelpItem :: class_name(),
            new DataClassRetrievesParameters($condition, $count, $offset, $order_property));
    }

    public static function get_help_url($name)
    {
        $help_item = self :: get_help_item_by_name($name);
        if ($help_item)
            return '<a class="help" href="' . $help_item->get_url() . '" target="about:blank">' . Translation :: get(
                'Help') . '</a>';
    }

    public static function get_tool_bar_help_item($help_item)
    {
        $hide_empty_pages = PlatformSetting :: get('hide_empty_pages', self :: context());
        $help_item = self :: get_help_item_by_name($help_item[0], $help_item[1]);

        if ($help_item instanceof HelpItem && ($help_item->has_url() || $hide_empty_pages == '0'))
        {
            return new ToolbarItem(
                Translation :: get('Help'),
                Theme :: getInstance()->getCommonImagePath('Action/Help'),
                $help_item ? $help_item->get_url() : '',
                ToolbarItem :: DISPLAY_ICON_AND_LABEL,
                false,
                'help',
                'about:blank');
        }
        else
        {
            return false;
        }
    }

    private static function get_help_item_by_name($context, $identifier)
    {
        $user_id = Session :: get_user_id();
        $user = \Chamilo\Core\User\Storage\DataManager :: retrieve_by_id(User :: class_name(), (int) $user_id);

        $language = LocalSetting :: getInstance()->get('platform_language');

        $help_item = DataManager :: retrieve_help_item_by_context($context, $identifier, $language);

        $autocomplete_page = PlatformSetting :: get('autocomplete_missing_pages', self :: context());
        $autocomplete_languages = PlatformSetting :: get('autocomplete_all_languages', self :: context());

        if ($help_item instanceof HelpItem)
        {
            return $help_item;
        }
        elseif ($autocomplete_page)
        {
            if ($autocomplete_languages)
            {
                $installed_languages = \Chamilo\Configuration\Configuration :: get_instance()->getLanguages();

                foreach ($installed_languages as $iso_code => $installed_language)
                {
                    $language_item = DataManager :: retrieve_help_item_by_context(
                        $context,
                        $identifier,
                        $installed_language);

                    if (! $language_item instanceof HelpItem)
                    {
                        $language_item = new HelpItem();
                        $language_item->set_context($context);
                        $language_item->set_identifier($identifier);
                        $language_item->set_language($iso_code);
                        $language_item->create();
                    }

                    if ($installed_language == $language)
                    {
                        $help_item = $language_item;
                    }
                }
            }
            else
            {
                $help_item = new HelpItem();
                $help_item->set_context($context);
                $help_item->set_identifier($identifier);
                $help_item->set_language($language);
                $help_item->create();
            }

            return $help_item;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns the admin breadcrumb generator
     *
     * @return \libraries\format\BreadcrumbGeneratorInterface
     */
    public function get_breadcrumb_generator()
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail :: get_instance());
    }
}
