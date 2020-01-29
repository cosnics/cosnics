<?php

namespace Chamilo\Core\Repository\Interfaces;

/**
 * @package Chamilo\Core\Repository\Interfaces
 * @author - Sven Vanpoucke - Hogeschool Gent
 *
 * Implement this interface in your content object if you want the repoviewer be able to import existing templates
 * into the creation form of a new object.
 *
 * Usage of this interface is necessary to make sure that not all object types can be used as a template because some
 * types like a file can not act as a template to automatically fill in the form due to files being physically stored
 * on the disk. Complex content objects like learning paths and entire assessments are excluded from this as well
 * and should be copied first before being used "as a template".
 *
 * For file templates we could adapt this system so that the template is actually shown (as a downloadable object) in the
 * creation form of the repo viewer.
 */
interface TemplateSupportInterface
{

}
