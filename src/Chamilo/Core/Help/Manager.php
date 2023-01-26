<?php
namespace Chamilo\Core\Help;

use Chamilo\Configuration\Configuration;
use Chamilo\Core\Admin\Core\BreadcrumbGenerator;
use Chamilo\Core\Help\Storage\DataClass\HelpItem;
use Chamilo\Core\Help\Storage\DataManager;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\Application\ApplicationConfigurationInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbGeneratorInterface;
use Chamilo\Libraries\Format\Structure\BreadcrumbTrail;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Format\Structure\ToolbarItem;
use Chamilo\Libraries\Platform\Configuration\LocalSetting;
use Chamilo\Libraries\Platform\Session\Session;
use Chamilo\Libraries\Storage\Parameters\DataClassCountParameters;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Translation\Translation;

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

        $this->checkAuthorization(Manager::context());
    }

    public function count_help_items($condition)
    {
        return DataManager::count(HelpItem::class, new DataClassCountParameters($condition));
    }

    public function get_breadcrumb_generator(): BreadcrumbGeneratorInterface
    {
        return new BreadcrumbGenerator($this, BreadcrumbTrail::getInstance());
    }

    private static function get_help_item_by_name($context, $identifier)
    {
        $user_id = Session::get_user_id();
        $user = \Chamilo\Core\User\Storage\DataManager::retrieve_by_id(User::class, (int) $user_id);

        $language = LocalSetting::getInstance()->get('platform_language');

        $help_item = DataManager::retrieve_help_item_by_context($context, $identifier, $language);

        $autocomplete_page = Configuration::getInstance()->get_setting(
            [self::context(), 'autocomplete_missing_pages']
        );
        $autocomplete_languages = Configuration::getInstance()->get_setting(
            [self::context(), 'autocomplete_all_languages']
        );

        if ($help_item instanceof HelpItem)
        {
            return $help_item;
        }
        elseif ($autocomplete_page)
        {
            if ($autocomplete_languages)
            {
                $installed_languages = Configuration::getInstance()->getLanguages();

                foreach ($installed_languages as $iso_code => $installed_language)
                {
                    $language_item = DataManager::retrieve_help_item_by_context(
                        $context, $identifier, $installed_language
                    );

                    if (!$language_item instanceof HelpItem)
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

    public static function get_help_url($name)
    {
        $help_item = self::get_help_item_by_name($name);
        if ($help_item)
        {
            return '<a class="help" href="' . $help_item->get_url() . '" target="about:blank">' . Translation::get(
                    'Help'
                ) . '</a>';
        }
    }

    public static function get_tool_bar_help_item($help_item)
    {
        $hide_empty_pages = Configuration::getInstance()->get_setting([self::context(), 'hide_empty_pages']);
        $help_item = self::get_help_item_by_name($help_item[0], $help_item[1]);

        if ($help_item instanceof HelpItem && ($help_item->has_url() || $hide_empty_pages == '0'))
        {
            return new ToolbarItem(
                Translation::get('Help'), new FontAwesomeGlyph('question-circle'),
                $help_item ? $help_item->get_url() : '', ToolbarItem::DISPLAY_ICON_AND_LABEL, false, 'help',
                'about:blank'
            );
        }
        else
        {
            return false;
        }
    }

    public function retrieve_help_items($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return DataManager::retrieves(
            HelpItem::class, new DataClassRetrievesParameters($condition, $count, $offset, $order_property)
        );
    }
}
