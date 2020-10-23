<?php
namespace Chamilo\Core\Repository\Instance;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Configuration\Storage\DataManager;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Structure\Glyph\FontAwesomeGlyph;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\instance
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const ACTION_ACTIVATE = 'Activator';

    const ACTION_BROWSE = 'Browser';

    // Actions

    const ACTION_CREATE = 'Creator';

    const ACTION_DEACTIVATE = 'Deactivator';

    const ACTION_DELETE = 'Deleter';

    const ACTION_RIGHTS = 'RightsEditor';

    const ACTION_UPDATE = 'Updater';

    const DEFAULT_ACTION = self::ACTION_BROWSE;

    const PARAM_IMPLEMENTATION = 'implementation';

    // Default action

    const PARAM_INSTANCE_ID = 'instance';

    public static function exists($type)
    {
        return class_exists($type . '\Manager');
    }

    public static function get_instance_identifier($type)
    {
        $class_name = $type . '\Manager';

        return $class_name::get_instance_identifier();
    }

    /**
     * Get a series of links for all external repository instances of one or more types
     *
     * @param $types array An array of external repository manager types
     * @param $auto_open unknown_type if there is only one instance, should it be opened automatically
     *
     * @return string
     */
    public static function get_links($types = array(), $auto_open = false)
    {
        $instances = Storage\DataManager::retrieve_active_instances($types);

        if ($instances->count() == 0)
        {
            if (!is_array($types))
            {
                $types = array($types);
            }

            $type_names = array();
            foreach ($types as $type)
            {
                $type_names[] = Translation::get('TypeName', null, Manager::get_namespace($type));
            }
            $type_names = implode(', ', $type_names);

            if (count($types) > 1)
            {
                $translation = Translation::get(
                    'NoExternalInstanceTypeManagersAvailable', array('TYPES' => $type_names),
                    \Chamilo\Core\Repository\Manager::context()
                );
            }
            else
            {
                $translation = Translation::get(
                    'NoExternalInstanceTypeManagerAvailable', array('TYPES' => $type_names),
                    \Chamilo\Core\Repository\Manager::context()
                );
            }

            return Display::warning_message($translation, true);
        }
        else
        {
            $html = array();
            $buttons = array();

            $available_instances = 0;

            foreach($instances as $instance)
            {
                $link = Path::getInstance()->getBasePath(true) . 'index.php?' . Application::PARAM_CONTEXT . '=' .
                    urlencode($instance->get_implementation()) . '&' .
                    \Chamilo\Core\Repository\Manager::PARAM_EXTERNAL_INSTANCE . '=' . $instance->get_id() . '&' .
                    \Chamilo\Core\Repository\External\Manager::PARAM_EMBEDDED . '=1';
                $title = Translation::get(
                    'BrowseObject', array('OBJECT' => $instance->get_title()), Utilities::COMMON_LIBRARIES
                );

                $glyph = new FontAwesomeGlyph('upload', array(), null, 'fas');

                $buttons[] =
                    '<a class="btn btn-default" onclick="javascript:openPopup(\'' . htmlspecialchars($link) . '\');">';
                $buttons[] = $glyph->render();
                $buttons[] = ' ';
                $buttons[] = htmlspecialchars($title);
                $buttons[] = '</a>';

                $available_instances ++;
            }

            if ($available_instances == 0)
            {
                $translation = Translation::get(
                    'NoExternalInstanceTypeManagerAvailable', array('TYPES' => $type_names),
                    \Chamilo\Core\Repository\Manager::context()
                );

                return Display::warning_message($translation, true);
            }
            else
            {
                $html[] = '<div style="margin-bottom: 10px;">' . implode(' ', $buttons) . '</div>';

                if ($available_instances == 1 && $auto_open)
                {
                    $html[] = '<script>';
                    $html[] = '$(document).ready(function ()';
                    $html[] = '{';
                    // htmlspecialchars converts & to &amp; -> need oher solution
                    // $html[] = ' openPopup(\'' . htmlspecialchars($link) . '\');';
                    $html[] = '	openPopup(\'' . $link . '\');';
                    $html[] = '});';
                    $html[] = '</script>';
                }

                return implode(PHP_EOL, $html);
            }
        }
    }

    public static function get_manager_class($type)
    {
        $parent = ClassnameUtilities::getInstance()->getNamespaceParent(
            ClassnameUtilities::getInstance()->getNamespaceParent($type)
        );
        $instance_type = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($parent, true);
        $package = ClassnameUtilities::getInstance()->getPackageNameFromNamespace($type, true);

        return $type . '\\' . $package . $instance_type;
    }

    public static function get_manager_connector_class($type)
    {
        return self::get_manager_class($type) . 'Connector';
    }

    public static function get_namespace($instance_type = null, $type = null)
    {
        return 'Chamilo\Core\Repository\Implementation\\' . $type;
    }

    public static function get_registered_types($status = Registration :: STATUS_ACTIVE)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Registration::class, Registration::PROPERTY_TYPE),
            new StaticConditionVariable('Chamilo\Core\Repository\Implementation')
        );
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Registration::class, Registration::PROPERTY_STATUS),
            new StaticConditionVariable($status)
        );
        $condition = new AndCondition($conditions);

        return DataManager::retrieves(
            Registration::class, new DataClassRetrievesParameters($condition)
        );
    }
}
