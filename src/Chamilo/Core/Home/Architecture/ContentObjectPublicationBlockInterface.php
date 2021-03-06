<?php
namespace Chamilo\Core\Home\Architecture;

/**
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface ContentObjectPublicationBlockInterface
{

    /**
     * Returns an array of the configuration values that return content object ids that need to be published in the
     * home application
     * 
     * @return string[]
     */
    public function getContentObjectConfigurationVariables();
}