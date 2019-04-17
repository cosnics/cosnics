<?php

namespace Chamilo\Application\Weblcms\Bridge\ExternalTool;

use Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Bridge\Interfaces\ExternalToolServiceBridgeInterface;
use Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool;

/**
 * @package Chamilo\Application\Weblcms\Bridge\ExternalTool
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolServiceBridge implements ExternalToolServiceBridgeInterface
{
    /**
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected $course;

    /**
     * @var \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication
     */
    protected $contentObjectPublication;

    /**
     * @var bool
     */
    protected $hasEditRight;

    /**
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\ExternalToolServiceBridge
     */
    public function setCourse(\Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\ExternalToolServiceBridge
     */
    public function setContentObjectPublication(
        \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
    )
    {
        if (!$contentObjectPublication->get_content_object() instanceof ExternalTool)
        {
            throw new \RuntimeException(
                'The given publication does not reference a valid external tool and can therefor not be displayed'
            );
        }

        $this->contentObjectPublication = $contentObjectPublication;

        return $this;
    }

    /**
     * @param bool $hasEditRight
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\ExternalToolServiceBridge
     */
    public function setHasEditRight(bool $hasEditRight)
    {
        $this->hasEditRight = $hasEditRight;

        return $this;
    }

    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool()
    {
        return $this->contentObjectPublication->get_content_object();
    }

    /**
     * Returns a unique ID to identify the context where the tool is running
     *
     * @return string
     */
    public function getContextIdentifier()
    {
        return base64_encode($this->course->getId());
    }

    /**
     * Returns the title of the context where the tool is running
     *
     * @return string
     */
    public function getContextTitle()
    {
        return $this->course->get_title();
    }

    /**
     * Returns a unique label / code of the context where the tool is running
     *
     * @return string
     */
    public function getContextLabel()
    {
        return $this->course->get_visual_code();
    }

    /**
     * Returns a unique ID to identify the external link in the context (e.g. the publication ID).
     * Preferred obfuscated with Base64 encoding
     *
     * @return string
     */
    public function getResourceLinkIdentifier()
    {
        return base64_encode($this->contentObjectPublication->getId());
    }

    /**
     * Returns whether or not the current user is allowed to be a course instructor in the external tool
     *
     * @return bool
     */
    public function isCourseInstructorInTool()
    {
        return $this->hasEditRight;
    }

    /**
     * Returns the classname of the LTI Integration service. This classname is used to define the context needed
     * for the LTI webservices.
     *
     * @return string
     */
    public function getLTIIntegrationClass()
    {
        return null;
    }

    /**
     * Returns the result identifier for the current user. This identifier is used for the basic outcomes LTI webservice.
     *
     * @return int
     */
    public function getOrCreateResultIdentifierForCurrentUser()
    {
        return null;
    }

    /**
     * Returns whether or not the outcomes service is supported
     *
     * @return bool
     */
    public function supportsOutcomesService()
    {
        return false;
    }
}