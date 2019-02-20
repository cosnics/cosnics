<?php

namespace Chamilo\Application\Plagiarism\Table;

/**
 * @package Chamilo\Application\Plagiarism\Table
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class PlagiarismResultTableParameters
{
    /**
     * @var \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService
     */
    protected $contentObjectPlagiarismResultService;

    /**
     * @var \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    protected $course;

    /**
     * PlagiarismResultTableParameters constructor.
     *
     * @param \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService
     * @param \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
     */
    public function __construct(
        \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService $contentObjectPlagiarismResultService,
        \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course $course
    )
    {
        $this->contentObjectPlagiarismResultService = $contentObjectPlagiarismResultService;
        $this->course = $course;
    }

    /**
     * @return \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService
     */
    public function getContentObjectPlagiarismResultService(
    ): \Chamilo\Application\Plagiarism\Service\ContentObjectPlagiarismResultService
    {
        return $this->contentObjectPlagiarismResultService;
    }

    /**
     * @return \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
     */
    public function getCourse(): \Chamilo\Application\Weblcms\Course\Storage\DataClass\Course
    {
        return $this->course;
    }
}