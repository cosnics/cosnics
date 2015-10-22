<?php
namespace Chamilo\Core\Repository\Instance;

use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Format\Display;
use Chamilo\Libraries\Format\Theme;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Storage\Parameters\DataClassRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\AndCondition;
use Chamilo\Libraries\Storage\Query\Condition\EqualityCondition;
use Chamilo\Libraries\Storage\Query\Variable\PropertyConditionVariable;
use Chamilo\Libraries\Storage\Query\Variable\StaticConditionVariable;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package core\repository\instance
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    // Parameters
    const PARAM_INSTANCE_ID = 'instance';
    const PARAM_IMPLEMENTATION = 'implementation';

    // Actions
    const ACTION_BROWSE = 'Browser';
    const ACTION_ACTIVATE = 'Activator';
    const ACTION_DEACTIVATE = 'Deactivator';
    const ACTION_UPDATE = 'Updater';
    const ACTION_DELETE = 'Deleter';
    const ACTION_CREATE = 'Creator';
    const ACTION_RIGHTS = 'RightsEditor';

    // Default action
    const DEFAULT_ACTION = self :: ACTION_BROWSE;

    public static function get_registered_types($status = Registration :: STATUS_ACTIVE)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_TYPE),
            new StaticConditionVariable('Chamilo\Core\Repository\Implementation'));
        $conditions[] = new EqualityCondition(
            new PropertyConditionVariable(Registration :: class_name(), Registration :: PROPERTY_STATUS),
            new StaticConditionVariable($status));
        $condition = new AndCondition($conditions);

        return \Chamilo\Configuration\Storage\DataManager :: retrieves(
            Registration :: class_name(),
            new DataClassRetrievesParameters($condition));
    }

    public static function get_namespace($instance_type = null, $type = null)
    {
        return 'Chamilo\Core\Repository\Implementation\\' . $type;
    }

    public static function get_instance_identifier($type)
    {
        $class_name = $type . '\Manager';
        return $class_name :: get_instance_identifier();
    }

    public static function get_manager_class($type)
    {
        $parent = ClassnameUtilities :: getInstance()->getNamespaceParent(
            ClassnameUtilities :: getInstance()->getNamespaceParent($type));
        $instance_type = ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($parent, true);
        $package = ClassnameUtilities :: getInstance()->getPackageNameFromNamespace($type, true);

        return $type . '\\' . $package . $instance_type;
    }

    public static function get_manager_connector_class($type)
    {
        return self :: get_manager_class($type) . 'Connector';
    }

    public static function exists($type)
    {
        return class_exists($type . '\Manager');
    }

    /**
     * Get a series of links for all external repository instances of one or more types
     *
     * @param $types array An array of external repository manager types
     * @param $auto_open unknown_type if there is only one instance, should it be opened automatically
     * @return string
     */
    public static function get_links($types = array(), $auto_open = false)
    {
        $instances = \Chamilo\Core\Repository\Instance\Storage\DataManager :: retrieve_active_instances($types);
        if ($instances->size() == 0)
        {
            if (! is_array($types))
            {
                $types = array($types);
            }

            $type_names = array();
            foreach ($types as $type)
            {
                $type_names[] = Translation :: get('TypeName', null, Manager :: get_namespace($type));
            }
            $type_names = implode(', ', $type_names);

            if (count($types) > 1)
            {
                $translation = Translation :: get(
                    'NoExternalInstanceTypeManagersAvailable',
                    array('TYPES' => $type_names),
                    \Chamilo\Core\Repository\Manager :: context());
            }
            else
            {
                $translation = Translation :: get(
                    'NoExternalInstanceTypeManagerAvailable',
                    array('TYPES' => $type_names),
                    \Chamilo\Core\Repository\Manager :: context());
            }

            return Display :: warning_message($translation, true);
        }
        else
        {
            $html = array();
            $buttons = array();

            $available_instances = 0;

            while ($instance = $instances->next_result())
            {
                $link = Path :: getInstance()->getBasePath(true) . 'index.php?' . Application :: PARAM_CONTEXT . '=' .
                     urlencode($instance->get_implementation()) . '&' .
                     \Chamilo\Core\Repository\Manager :: PARAM_EXTERNAL_INSTANCE . '=' . $instance->get_id() . '&' .
                     \Chamilo\Core\Repository\External\Manager :: PARAM_EMBEDDED . '=1';
                $image = Theme :: getInstance()->getImagePath($instance->get_implementation(), 'Logo/16');
                $title = Translation :: get(
                    'BrowseObject',
                    array('OBJECT' => $instance->get_title()),
                    Utilities :: COMMON_LIBRARIES);
                $buttons[] = '<a class="button normal_button upload_button" style="background-image: url(' .
                     htmlspecialchars($image) . ');" onclick="javascript:openPopup(\'' . htmlspecialchars($link) .
                     '\');"> ' . htmlspecialchars($title) . '</a>';
                $available_instances ++;
            }

            if ($available_instances == 0)
            {
                $translation = Translation :: get(
                    'NoExternalInstanceTypeManagerAvailable',
                    array('TYPES' => $type_names),
                    \Chamilo\Core\Repository\Manager :: context());
                return Display :: warning_message($translation, true);
            }
            else
            {
                $html[] = '<div style="margin-bottom: 10px;">' . implode(' ', $buttons) . '</div>';

                if ($available_instances == 1 && $auto_open)
                {
                    $html[] = '<script type="text/javascript">';
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
}
