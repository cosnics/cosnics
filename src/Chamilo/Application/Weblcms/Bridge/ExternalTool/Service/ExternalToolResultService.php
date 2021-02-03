<?php

namespace Chamilo\Application\Weblcms\Bridge\ExternalTool\Service;

use Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult;
use Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\FilterParameters;

/**
 * @package Chamilo\Application\Weblcms\Bridge\ExternalTool\Service
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class ExternalToolResultService
{
    /**
     * @var \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\Repository\ExternalToolResultRepository
     */
    protected $externalToolResultRepository;

    /**
     * ExternalToolResultService constructor.
     *
     * @param \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\Repository\ExternalToolResultRepository $externalToolResultRepository
     */
    public function __construct(
        \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\Repository\ExternalToolResultRepository $externalToolResultRepository
    )
    {
        $this->externalToolResultRepository = $externalToolResultRepository;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult
     */
    public function getOrCreateResultForUser(ContentObjectPublication $contentObjectPublication, User $user)
    {
        $externalToolResult = $this->getResultForUser($contentObjectPublication, $user);
        if (!$externalToolResult instanceof ExternalToolResult)
        {
            $externalToolResult = $this->createResultForUser($contentObjectPublication, $user);
        }

        return $externalToolResult;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return \Chamilo\Application\Weblcms\Bridge\ExternalTool\Storage\DataClass\ExternalToolResult
     */
    public function createResultForUser(ContentObjectPublication $contentObjectPublication, User $user)
    {
        $externalToolResult = new ExternalToolResult();

        $externalToolResult->setUserId($user->getId());
        $externalToolResult->setContentObjectPublicationId($contentObjectPublication->getId());

        if (!$this->externalToolResultRepository->create($externalToolResult))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not create the external tool result in the database for content object publication %s and user %s',
                    $contentObjectPublication->getId(), $user->getId()
                )
            );
        }

        return $externalToolResult;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     *
     * @return ExternalToolResult
     */
    public function getResultForUser(ContentObjectPublication $contentObjectPublication, User $user)
    {
        return $this->externalToolResultRepository->findByContentObjectPublicationAndUser(
            $contentObjectPublication, $user
        );
    }

    /**
     * @param int $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass|ExternalToolResult
     */
    public function getResultById(int $id)
    {
        $externalToolResult = $this->externalToolResultRepository->findById($id);
        if (!$externalToolResult instanceof ExternalToolResult)
        {
            throw new \RuntimeException(
                sprintf('The given external tool result id %s does not reference a valid object', $id)
            );
        }

        return $externalToolResult;
    }

    /**
     * @param int $resultId
     * @param float $score
     */
    public function updateResultByIdAndLTIScore(int $resultId, float $score)
    {
        $externalToolResult = $this->getResultById($resultId);
        $externalToolResult->fromLTIResult($score);

        if (!$this->externalToolResultRepository->update($externalToolResult))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not update the external tool result with id %s', $resultId
                )
            );
        }
    }

    /**
     * @param int $resultId
     */
    public function deleteResultById(int $resultId)
    {
        $externalToolResult = $this->getResultById($resultId);

        if (!$this->externalToolResultRepository->delete($externalToolResult))
        {
            throw new \RuntimeException(
                sprintf(
                    'Could not delete the external tool result with id %s', $resultId
                )
            );
        }
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\RecordIterator
     * @throws \Exception
     */
    public function getResultsWithUsers(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        return $this->externalToolResultRepository->getResultsWithUsers($contentObjectPublication, $filterParameters);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Storage\DataClass\ContentObjectPublication $contentObjectPublication
     * @param \Chamilo\Libraries\Storage\Parameters\FilterParameters $filterParameters
     *
     * @return int
     */
    public function countResultsWithUsers(
        ContentObjectPublication $contentObjectPublication, FilterParameters $filterParameters
    )
    {
        return $this->externalToolResultRepository->countResultsWithUsers($contentObjectPublication, $filterParameters);
    }
}