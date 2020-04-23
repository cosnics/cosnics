<?php
namespace Chamilo\Core\Repository\Filter;

use Chamilo\Core\Repository\Configuration;
use Chamilo\Core\Repository\Manager;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Session\Session;

/**
 * The data set via Session, $_POST and $_GET variables related to filtering a set of content objects
 *
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class FilterData
{
    // Storage
    const FILTER_CATEGORY = ContentObject::PROPERTY_PARENT_ID;

    // Available general filters

    const FILTER_CATEGORY_RECURSIVE = 'category_recursive';

    const FILTER_CREATION_DATE = ContentObject::PROPERTY_CREATION_DATE;

    const FILTER_FROM_DATE = 'from';

    const FILTER_MODIFICATION_DATE = ContentObject::PROPERTY_MODIFICATION_DATE;

    const FILTER_TEXT = 'text';

    const FILTER_TO_DATE = 'to';

    const FILTER_TYPE = 'filter_type';

    const FILTER_USER_VIEW = 'view';

    const STORAGE = 'filter';

    /**
     *
     * @var \core\repository\filter\FilterData
     */
    private static $instance;

    /**
     *
     * @var string[]
     */
    protected $storage;

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspaceImplementation;

    /**
     *
     * @var string
     */
    private $context;

    /**
     * Constructs a new Filter
     */
    public function __construct(WorkspaceInterface $workspaceImplementation)
    {
        $this->workspaceImplementation = $workspaceImplementation;
        $this->initialize();
    }

    public static function build_url($parsed_url)
    {
        $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
        $host = isset($parsed_url['host']) ? $parsed_url['host'] : '';
        $port = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
        $user = isset($parsed_url['user']) ? $parsed_url['user'] : '';
        $pass = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
        $pass = ($user || $pass) ? $pass . '@' : '';
        $path = isset($parsed_url['path']) ? $parsed_url['path'] : '';
        $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

        if (isset($parsed_url['query']) && is_array($parsed_url['query']))
        {
            $query = '?' . http_build_query($parsed_url['query']);
        }
        elseif (isset($parsed_url['query']) && is_string($parsed_url['query']))
        {
            $query = '?' . $parsed_url['query'];
        }
        else
        {
            $query = '';
        }

        return $scheme . $user . $pass . $host . $port . $path . $query . $fragment;
    }

    public static function clean_url(WorkspaceInterface $workspaceImplementation, $url)
    {
        $filter_data = self::getInstance($workspaceImplementation);
        $url_parts = parse_url(urldecode($url));

        parse_str($url_parts['query'], $query);

        foreach ($filter_data->get_filter_properties() as $property)
        {
            if (!$filter_data->has_filter_property($property))
            {
                unset($query[$property]);
            }
        }

        $url_parts['query'] = http_build_query($query);

        return self::build_url($url_parts);
    }

    /**
     * Clear all filter parameters from the session
     */
    public function clear($updateSession = true)
    {
        $this->set_storage(array());

        if ($updateSession)
        {
            $this->update_session();
        }
    }

    /**
     * Returns the value of a filter property from a request
     *
     * @param string $filterProperty
     *
     * @return string
     */
    protected function getFromRequest($filterProperty)
    {
        $postValue = Request::post($filterProperty);

        if (isset($postValue))
        {
            return $postValue;
        }

        $getValue = Request::get($filterProperty);

        if (isset($getValue))
        {
            return $getValue;
        }

        return null;
    }

    /**
     *
     * @return FilterData
     */
    public static function getInstance(WorkspaceInterface $workspaceImplementation)
    {
        if (!isset(self::$instance))
        {
            $filter_data = new FilterData($workspaceImplementation);
            $type = $filter_data->get_filter_property(FilterData::FILTER_TYPE);

            if (is_numeric($type) && !empty($type))
            {
                $template_id = $filter_data->get_filter_property(FilterData::FILTER_TYPE);
                $template_registration = Configuration::registration_by_id((int) $template_id);

                $class_name = $template_registration->get_content_object_type() . '\Filter\FilterData';
                $filter_data = new $class_name($workspaceImplementation);
            }

            self::$instance = $filter_data;
        }

        return self::$instance;
    }

    protected function getStorageKey()
    {
        return static::STORAGE . '_' . $this->workspaceImplementation->getHash();
    }

    /**
     * Returns the dataclass for the given type (if there is a filter on the type)
     */
    public function getTypeDataClass()
    {
        $type = $this->get_type();

        if (!is_null($type))
        {
            $context = $this->get_context();

            return $context . '\Storage\DataClass\\' .
                ClassnameUtilities::getInstance()->getPackageNameFromNamespace($context);
        }

        return ContentObject::class_name();
    }

    public function get_category()
    {
        return $this->get_filter_property(self::FILTER_CATEGORY);
    }

    public function get_context()
    {
        if (!isset($this->context))
        {
            $type = $this->get_filter_property(FilterData::FILTER_TYPE);

            if (is_numeric($type) && !empty($type))
            {
                $template_id = $this->get_filter_property(FilterData::FILTER_TYPE);
                $template_registration = Configuration::registration_by_id((int) $template_id);
                $this->context = $template_registration->get_content_object_type();
            }
            else
            {
                $this->context = Manager::package();
            }
        }

        return $this->context;
    }

    /**
     *
     * @param string $type
     *
     * @return int NULL
     */
    public function get_creation_date($type = self :: FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_CREATION_DATE, $type);
    }

    /**
     *
     * @param string $date_type
     * @param string $part_type
     *
     * @return int NULL
     */
    public function get_date($date_type = self :: FILTER_CREATION_DATE, $part_type = self :: FILTER_FROM_DATE)
    {
        $filter_property = $this->get_filter_property($date_type);

        if (isset($filter_property))
        {
            return $filter_property[$part_type];
        }
        else
        {
            return null;
        }
    }

    /**
     *
     * @param string[] $filter_properties
     *
     * @return string[]
     */
    public function get_filter_properties($filter_properties = array())
    {
        $filter_properties[] = self::FILTER_TEXT;
        $filter_properties[] = self::FILTER_CATEGORY;
        $filter_properties[] = self::FILTER_CATEGORY_RECURSIVE;
        $filter_properties[] = self::FILTER_CREATION_DATE;
        $filter_properties[] = self::FILTER_MODIFICATION_DATE;
        $filter_properties[] = self::FILTER_TYPE;
        $filter_properties[] = self::FILTER_USER_VIEW;

        return $filter_properties;
    }

    /**
     *
     * @param string $property
     *
     * @return string
     */
    public function get_filter_property($property)
    {
        return $this->storage[$property];
    }

    /**
     *
     * @param string $type
     *
     * @return int NULL
     */
    public function get_modification_date($type = self :: FILTER_FROM_DATE)
    {
        return $this->get_date(self::FILTER_MODIFICATION_DATE, $type);
    }

    /**
     *
     * @return string[]
     */
    public function get_storage()
    {
        return $this->storage;
    }

    public function get_type()
    {
        $type = $this->get_filter_property(self::FILTER_TYPE);

        return is_numeric($type) && !empty($type) ? $type : null;
    }

    public function get_type_category()
    {
        $type = $this->get_filter_property(self::FILTER_TYPE);

        return !is_numeric($type) && !empty($type) ? $type : null;
    }

    public function get_user_view()
    {
        return $this->get_filter_property(self::FILTER_USER_VIEW);
    }

    /**
     *
     * @return boolean
     */
    public function has_creation_date()
    {
        return $this->has_date(self::FILTER_CREATION_DATE);
    }

    /**
     *
     * @return boolean
     */
    public function has_date($date_type = null)
    {
        if ($date_type)
        {
            $date = $this->get_filter_property($date_type);

            return $date[self::FILTER_FROM_DATE] || $date[self::FILTER_TO_DATE];
        }
        else
        {
            return $this->has_date(self::FILTER_CREATION_DATE) || $this->has_date(self::FILTER_MODIFICATION_DATE);
        }
    }

    /**
     *
     * @param string $property
     *
     * @return boolean
     */
    public function has_filter_property($property)
    {
        $value = $this->get_filter_property($property);

        return isset($value) && !empty($value) && $value;
    }

    /**
     *
     * @return boolean
     */
    public function has_modification_date()
    {
        return $this->has_date(self::FILTER_MODIFICATION_DATE);
    }

    /**
     * Get the available filter property values from the session storage and update them if and when they were set /
     * changed in the request
     */
    public function initialize()
    {
        $this->storage = unserialize(Session::retrieve($this->getStorageKey()));

        foreach ($this->get_filter_properties() as $filter_property)
        {
            $valueFromRequest = $this->getFromRequest($filter_property);
            if (!is_null($valueFromRequest))
            {
                $this->set_filter_property($filter_property, $valueFromRequest);
            }
        }
    }

    /**
     * Determine whether one or more of the basic parameters were set
     *
     * @return boolean
     */
    public function is_set()
    {
        $text = $this->get_filter_property(self::FILTER_TEXT);
        $type = $this->get_type();
        $type_category = $this->get_type_category();
        $user_view_id = (int) $this->get_filter_property(self::FILTER_USER_VIEW);

        $category_id = $this->get_category();
        $category_id = isset($category_id) ? (int) $category_id : - 1;

        return (isset($text) || $type > 0 || isset($type_category) || $category_id >= 0 || $user_view_id > 0 ||
            $this->has_date());
    }

    /**
     *
     * @param string $property
     * @param string $value
     */
    public function set_filter_property($property, $value)
    {
        $this->storage[$property] = $value;
        $this->update_session();
    }

    /**
     *
     * @param string[] $storage
     */
    private function set_storage($storage)
    {
        $this->storage = $storage;
    }

    /**
     * Update the combined filters in the session storage
     */
    public function update_session()
    {
        $data = serialize($this->get_storage());
        Session::register($this->getStorageKey(), $data);
    }
}