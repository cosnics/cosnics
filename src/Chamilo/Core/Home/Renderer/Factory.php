<?php
namespace Chamilo\Core\Home\Renderer;

use Chamilo\Libraries\Architecture\Application\Application;
use Chamilo\Libraries\Translation\Translation;

/**
 *
 * @package Chamilo\Core\Home\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Factory
{

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var \Chamilo\Libraries\Architecture\Application\Application
     */
    private $application;

    /**
     *
     * @param string $type
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function __construct($type, Application $application)
    {
        $this->type = $type;
        $this->application = $application;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     *
     * @return \Chamilo\Libraries\Architecture\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     *
     * @param \Chamilo\Libraries\Architecture\Application\Application $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \Chamilo\Core\Home\Renderer\Renderer
     */
    public function getRenderer()
    {
        $class = __NAMESPACE__ . '\Type\\' . $this->getType();
        
        if (! class_exists($class))
        {
            throw new \Exception(Translation::get('HomeRendererTypeDoesNotExist', array('type' => $this->getType())));
        }
        
        return new $class($this->getApplication());
    }
}
