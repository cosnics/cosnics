<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\CourseGroupServiceDecoratorInterface;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\PublicationCategoryCourseGroupServiceDecorator;
use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Storage\DataClass\CourseGroup;
use Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Form\CourseGroupFormDecorator;

/**
 * Decorates the service for course groups. Adding additional functionality for the common course group functionality
 *
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Document\Integration\Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class CourseGroupServiceDecorator extends PublicationCategoryCourseGroupServiceDecorator implements CourseGroupServiceDecoratorInterface
{
    /**
     * Returns the name of the tool to be used in the category
     *
     * @return string
     */
    function getToolName()
    {
        return 'Document';
    }

    /**
     * Returns the name of the property for the
     * @return mixed
     */
    function getFormProperty()
    {
        return CourseGroupFormDecorator::PROPERTY_DOCUMENT_CATEGORY_ID;
    }
}