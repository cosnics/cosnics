<?php
namespace Chamilo\Core\Repository\Common\Import;

use Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

abstract class ImportParameters
{

    /**
     *
     * @var \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    private $workspace;

    private $category;

    private $type;

    private $user;

    public function __construct($type, $user, WorkspaceInterface $workspace, $category = 0)
    {
        $this->workspace = $workspace;
        $this->category = $category;
        $this->type = $type;
        $this->user = $user;
    }

    /**
     *
     * @return \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     *
     * @param \Chamilo\Core\Repository\Workspace\Architecture\WorkspaceInterface $workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
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
     * @return int
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

    public static function factory($type, $user, WorkspaceInterface $workspace, $category = 0, $file = null, 
        $form_values = array())
    {
        $class = __NAMESPACE__ . '\\' . StringUtilities::getInstance()->createString($type)->upperCamelize() . '\\' .
             (string) StringUtilities::getInstance()->createString($type)->upperCamelize() . 'ImportParameters';
        
        if (! class_exists($class))
        {
            throw new \Exception(Translation::get('UnknownImportParametersType', array('TYPE' => $type)));
        }
        
        return new $class($type, $user, $workspace, $category, $file, $form_values);
    }
}
