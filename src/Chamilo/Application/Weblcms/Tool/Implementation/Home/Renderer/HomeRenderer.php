<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager;

/**
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Home\Renderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 */
abstract class HomeRenderer
{

    /**
     *
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager
     */
    private $homeTool;

    /**
     *
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[]
     */
    private $courseTools;

    /**
     *
     * @var boolean
     */
    private $introductionAllowed;

    /**
     *
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    private $introduction;

    /**
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager $homeTool
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[] $courseTools
     * @param boolean $introductionAllowed
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $introduction
     */
    public function __construct(Manager $homeTool, $courseTools, $introductionAllowed,
        ContentObjectPublication $introduction = null)
    {
        $this->homeTool = $homeTool;
        $this->courseTools = $courseTools;
        $this->introductionAllowed = $introductionAllowed;
        $this->introduction = $introduction;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager
     */
    public function getHomeTool()
    {
        return $this->homeTool;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Home\Manager $homeTool
     */
    public function setHomeTool(Manager $homeTool)
    {
        $this->homeTool = $homeTool;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[]
     */
    public function getCourseTools()
    {
        return $this->courseTools;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\CourseTool[] $courseTools
     */
    public function setCourseTools($courseTools)
    {
        $this->courseTools = $courseTools;
    }

    /**
     *
     * @return boolean
     */
    public function getIntroductionAllowed()
    {
        return $this->introductionAllowed;
    }

    /**
     *
     * @param boolean $introductionAllowed
     */
    public function setIntroductionAllowed($introductionAllowed)
    {
        $this->introductionAllowed = $introductionAllowed;
    }

    /**
     *
     * @return \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    public function getIntroduction()
    {
        return $this->introduction;
    }

    /**
     *
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $introduction
     */
    public function setIntroduction(ContentObjectPublication $introduction)
    {
        $this->introduction = $introduction;
    }

    /**
     *
     * @return string
     */
    abstract public function render();
}