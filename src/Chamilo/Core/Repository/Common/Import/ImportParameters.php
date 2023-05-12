<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Workspace\Storage\DataClass\Workspace;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

abstract class ImportParameters
{

    private $category;

    private $type;

    private $user;

    private Workspace $workspace;

    public function __construct($type, $user, Workspace $workspace, $category = 0)
    {
        $this->workspace = $workspace;
        $this->category = $category;
        $this->type = $type;
        $this->user = $user;
    }

    public static function factory(
        $type, $user, Workspace $workspace, $category = 0, $file = null, $form_values = []
    )
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() . '\\' .
            (string) StringUtilities::getInstance()->createString($type)->upperCamelize() . 'ImportParameters';

        if (!class_exists($class))
        {
            throw new Exception(Translation::get('UnknownImportParametersType', ['TYPE' => $type]));
        }

        return new $class($type, $user, $workspace, $category, $file, $form_values);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getWorkspace(): Workspace
    {
        return $this->workspace;
    }

    /**
     * @return the $category
     */
    public function get_category()
    {
        return $this->category;
    }

    /**
     * @deprecated Use ImportParameters::getType() now
     */
    public function get_type()
    {
        return $this->getType();
    }

    /**
     * @return the $user
     */
    public function get_user()
    {
        return $this->user;
    }

    public function setWorkspace(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     * @param $category field_type
     */
    public function set_category($category)
    {
        $this->category = $category;
    }

    /**
     * @param $type field_type
     */
    public function set_type($type)
    {
        $this->type = $type;
    }

    /**
     * @param $user field_type
     */
    public function set_user($user)
    {
        $this->user = $user;
    }
}
