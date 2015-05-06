<?php
namespace Chamilo\Application\Calendar;

use Chamilo\Libraries\Architecture\Application\Application;

abstract class Actions
{

    /**
     *
     * @var \libraries\architecture\application\Application
     */
    private $application;

    /**
     *
     * @param \libraries\architecture\application\Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     *
     * @return \libraries\architecture\application\Application
     */
    public function get_application()
    {
        return $this->application;
    }

    /**
     *
     * @return \libraries\format\ToolbarItem[]
     */
    public function get()
    {
        return array();
    }
}