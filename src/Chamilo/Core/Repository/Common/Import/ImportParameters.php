<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class ImportParameters
{
    const CLASS_NAME = __CLASS__;

    private $category;

    private $type;

    private $user;

    public function __construct($type, $user, $category = 0)
    {
        $this->category = $category;
        $this->type = $type;
        $this->user = $user;
    }

    /**
     *
     * @return the $category
     */
    public function get_category()
    {
        return $this->category;
    }

    /**
     *
     * @param $category field_type
     */
    public function set_category($category)
    {
        $this->category = $category;
    }

    /**
     *
     * @return the $type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     *
     * @param $type field_type
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return the $user
     */
    public function get_user()
    {
        return $this->user;
    }

    /**
     *
     * @param $user field_type
     */
    public function set_user($user)
    {
        $this->user = $user;
    }

    public static function factory($type, $user, $category = 0, $file = null, $form_values = array())
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities :: getInstance()->createString($type)->upperCamelize() . '\\' .
             (string) StringUtilities :: getInstance()->createString($type)->upperCamelize() . 'ImportParameters';
        
        if (! class_exists($class))
        {
            throw new \Exception(Translation :: get('UnknownImportParametersType', array('TYPE' => $type)));
        }
        
        return new $class($type, $user, $category, $file, $form_values);
    }
}
