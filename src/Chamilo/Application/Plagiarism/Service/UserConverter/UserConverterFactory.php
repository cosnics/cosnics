<?php

namespace Chamilo\Application\Plagiarism\Service\UserConverter;

/**
 * @package Chamilo\Application\Plagiarism\Service\Turnitin\UserConverter
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UserConverterFactory
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\UserConverter\DefaultUserConverter
     */
    protected $defaultUserConverter;

    /**
     * @var \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface[]
     */
    protected $userConverters;

    /**
     * UserConverterFactory constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Service\UserConverter\DefaultUserConverter $defaultUserConverter
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\UserConverter\DefaultUserConverter $defaultUserConverter
    )
    {
        $this->defaultUserConverter = $defaultUserConverter;
        $this->userConverters = [];
    }

    /**
     * @param \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface $userConverter
     */
    public function addUserConverter(UserConverterInterface $userConverter)
    {
        $this->userConverters[] = $userConverter;
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\UserConverter\UserConverterInterface
     */
    public function createUserConverter()
    {
        if(!empty($this->userConverters))
        {
            return $this->userConverters[0];
        }

        return $this->defaultUserConverter;
    }
}