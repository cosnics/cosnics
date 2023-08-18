<?php

namespace Chamilo\Application\Weblcms\Bridge;

use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;

/**
 * @author Stefan Gabriëls - Hogeschool Gent
 */
class PublicationServiceBridge implements PublicationServiceBridgeInterface
{
    /**
     * @var ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var Course
     */
    protected $course;

    /**
     * @return ContentObjectPublication
     */
    public function getContentObjectPublication(): ContentObjectPublication
    {
        return $this->contentObjectPublication;
    }

    /**
     * @param ContentObjectPublication $contentObjectPublication
     */
    public function setContentObjectPublication(ContentObjectPublication $contentObjectPublication)
    {
        $this->contentObjectPublication = $contentObjectPublication;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     * @return void
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;
    }
}
