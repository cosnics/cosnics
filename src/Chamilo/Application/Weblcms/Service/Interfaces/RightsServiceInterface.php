<?php

namespace Chamilo\Application\Weblcms\Service\Interfaces;

use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublicationCategory;
use Chamilo\Core\User\Storage\DataClass\User;

/**
 * Interface for the RightsService
 *
 * @package application\weblcms
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
interface RightsServiceInterface
{
    /**
     * Sets the viewAsUserMode variable
     *
     * @param bool $viewAsUserMode
     *
     * @return $this
     */
    public function setViewAsUserMode($viewAsUserMode);

    /**
     * Returns the publication identifiers where a given user has the view right for in a given category for a given
     * course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $category
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return int[]
     */
    public function getPublicationIdsWithViewRightInCategory(
        User $user, ContentObjectPublicationCategory $category, Course $course
    );

    /**
     * Returns the publication identifiers where a given user has the view right for in a given category for a given
     * course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return int[]
     */
    public function getPublicationIdsWithViewRightInTool(User $user, $tool, Course $course);

    /**
     * Checks if a user can view a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewPublication(User $user, ContentObjectPublication $publication, Course $course);

    /**
     * Checks if a user can edit a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserEditPublication(User $user, ContentObjectPublication $publication, Course $course);

    /**
     * Checks if a user can delete a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublication $publication
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserDeletePublication(User $user, ContentObjectPublication $publication, Course $course);

    /**
     * Checks if a user can view a publication category in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewPublicationCategory(
        User $user, ContentObjectPublicationCategory $publicationCategory, Course $course
    );

    /**
     * Checks if a user can edit a publication category in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserEditPublicationCategory(
        User $user, ContentObjectPublicationCategory $publicationCategory, Course $course
    );

    /**
     * Checks if a user can delete a publication in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param ContentObjectPublicationCategory $publicationCategory
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserDeletePublicationCategory(
        User $user, ContentObjectPublicationCategory $publicationCategory, Course $course
    );

    /**
     * Checks if a user can publish a publication in a tool of a course (and optionally in a category)
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     * @param ContentObjectPublicationCategory $publicationCategory
     *
     * @return bool
     */
    public function canUserCreatePublication(
        User $user, $tool, Course $course, ContentObjectPublicationCategory $publicationCategory = null
    );

    /**
     * Checks if a user can view a tool in a given course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param string $tool
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewTool(User $user, $tool, Course $course);

    /**
     * Checks if a user can view a course
     *
     * @param \Chamilo\Core\User\Storage\DataClass\User $user $user
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     *
     * @return bool
     */
    public function canUserViewCourse(User $user, Course $course);
}