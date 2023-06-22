<?php

namespace Chamilo\Application\Weblcms\Bridge\LearningPath\LearningPath;

use Chamilo\Libraries\Architecture\ContextIdentifier;
use Chamilo\Application\Weblcms\Course\Storage\DataClass\Course;


interface LearningPathServiceBridgeInterface
{
    /**
    * @return ContextIdentifier
    */
    public function getContextIdentifier(): ContextIdentifier;

    /**
     * @return Course|null
     */
    public function getCourse(): ?Course;

    /**
     * @param string $toolName
     *
     * @return bool
     */
    public function isCourseToolActive(string $toolName): bool;

    /**
     * @return string
     */
    public function getCourseUrl(): string;

    /**
     * @param string $toolName
     *
     * @return string
     */
    public function getCourseToolUrl(string $toolName): string;
}