<?php

namespace Chamilo\Application\Lti\Domain;

/**
 * Class ContextType
 *
 * @package Chamilo\Application\Lti\Domain
 * @author - Sven Vanpoucke - Hogeschool Gent
 */
class ContextType
{
    const CONTEXT_COURSE_TEMPLATE = 'urn:lti:context-type:ims/lis/CourseTemplate';
    const CONTEXT_COURSE_OFFERING = 'urn:lti:context-type:ims/lis/CourseOffering';
    const CONTEXT_COURSE_SECTION = 'urn:lti:context-type:ims/lis/CourseSection';
    const CONTEXT_GROUP = 'urn:lti:context-type:ims/lis/Group';

    /**
     * @var string
     */
    protected $contextType;

    /**
     * ContextType constructor.
     *
     * @param string $contextType
     */
    public function __construct(string $contextType)
    {
        if(!in_array($contextType, $this->getAvailableContextTypes()))
        {
            throw new \InvalidArgumentException(
                'The given context type %s is not valid. Context type should be one of (%s)',
                $contextType, implode(', ' , $this->getAvailableContextTypes())
            );
        }

        $this->contextType = $contextType;
    }

    /**
     * @return array
     */
    public function getAvailableContextTypes()
    {
        return [self::CONTEXT_COURSE_TEMPLATE, self::CONTEXT_COURSE_OFFERING, self::CONTEXT_COURSE_SECTION, self::CONTEXT_GROUP];
    }
}

