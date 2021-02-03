<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Bridge\Interfaces;

use Chamilo\Core\User\Storage\DataClass\User;

/**
 * @package Chamilo\Core\Repository\ContentObject\ExternalTool\Display\Bridge
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ExternalToolServiceBridgeInterface
{
    /**
     * @return \Chamilo\Core\Repository\ContentObject\ExternalTool\Storage\DataClass\ExternalTool
     */
    public function getExternalTool();

    /**
     * Returns a unique ID to identify the context where the tool is running
     *
     * @return string
     */
    public function getContextIdentifier();

    /**
     * Returns the title of the context where the tool is running
     *
     * @return string
     */
    public function getContextTitle();

    /**
     * Returns a unique label / code of the context where the tool is running
     *
     * @return string
     */
    public function getContextLabel();

    /**
     * Returns a unique ID to identify the external link in the context (e.g. the publication ID).
     * Preferred obfuscated with Base64 encoding
     *
     * @return string
     */
    public function getResourceLinkIdentifier();

    /**
     * Returns whether or not the current user is allowed to be a course instructor in the external tool
     *
     * @return bool
     */
    public function isCourseInstructorInTool();

    /**
     * Returns the classname of the LTI Integration service. This classname is used to define the context needed
     * for the LTI webservices.
     *
     * @return string
     */
    public function getLTIIntegrationClass();

    /**
     * Returns the result identifier for the current user. This identifier is used for the basic outcomes LTI webservice.
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return int
     */
    public function getOrCreateResultIdentifierForUser(User $user);

    /**
     * Returns whether or not the outcomes service is supported
     *
     * @return bool
     */
    public function supportsOutcomesService();
}