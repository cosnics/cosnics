<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Test\Stub;

use Chamilo\Application\Weblcms\Tool\Implementation\CourseGroup\Infrastructure\Service\CourseGroupDecorator\PublicationCategoryCourseGroupServiceDecorator;

class DefaultPublicationCategoryCourseGroupServiceDecorator extends PublicationCategoryCourseGroupServiceDecorator
{

    /**
     * Returns the name of the tool to be used in the category
     *
     * @return string
     */
    function getToolName()
    {
        return 'Test';
    }

    /**
     * Returns the name of the property for the
     * @return mixed
     */
    function getFormProperty()
    {
        return 'use_test';
    }
}