<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Repository\Publication;

use Chamilo\Core\Repository\Publication\LocationSupport;

/**
 *
 * @package personal_calendar\integration\core\repository\publication
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class Location implements LocationSupport

{
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_TOOL_ID = 'tool_id';

    /**
     *
     * @var int
     */
    private $course_id;

    /**
     *
     * @var string
     */
    private $tool_id;

    /**
     *
     * @var string
     */
    private $course_title;

    /**
     *
     * @var string
     */
    private $visual_code;

    /**
     *
     * @var string
     */
    private $tool_name;

    /**
     *
     * @param int $course_id
     * @param string $tool_id
     * @param string $course_title
     * @param string $visual_code
     * @param string $tool_name
     */
    function __construct($course_id, $tool_id, $course_title, $visual_code, $tool_name)
    {
        $this->course_id = $course_id;
        $this->tool_id = $tool_id;
        $this->course_title = $course_title;
        $this->visual_code = $visual_code;
        $this->tool_name = $tool_name;
    }

    /**
     *
     * @see \core\repository\publication\Location::encode()
     */
    public function encode()
    {
        return base64_encode(serialize($this));
    }

    /**
     *
     * @return int
     */
    public function get_course_id()
    {
        return $this->course_id;
    }

    /**
     *
     * @param int $course_id
     */
    public function set_course_id($course_id)
    {
        $this->course_id = $course_id;
    }

    /**
     *
     * @return string
     */
    public function get_tool_id()
    {
        return $this->tool_id;
    }

    /**
     *
     * @param string $tool_id
     */
    public function set_tool_id($tool_id)
    {
        $this->tool_id = $tool_id;
    }

    /**
     *
     * @return string
     */
    public function get_course_title()
    {
        return $this->course_title;
    }

    /**
     *
     * @param string $course_title
     */
    public function set_course_title($course_title)
    {
        $this->course_title = $course_title;
    }

    /**
     *
     * @return string
     */
    public function get_visual_code()
    {
        return $this->visual_code;
    }

    /**
     *
     * @param string $visual_code
     */
    public function set_visual_code($visual_code)
    {
        $this->visual_code = $visual_code;
    }

    /**
     *
     * @return string
     */
    public function get_tool_name()
    {
        return $this->tool_name;
    }

    /**
     *
     * @param string $tool_name
     */
    public function set_tool_name($tool_name)
    {
        $this->tool_name = $tool_name;
    }
}