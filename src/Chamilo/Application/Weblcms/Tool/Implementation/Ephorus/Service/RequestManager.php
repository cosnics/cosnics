<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Result;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\EphorusWebserviceRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\RequestRepository;
use Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File;
use Chamilo\Core\Repository\Storage\DataClass\ContentObject;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
use Chamilo\Core\User\Storage\DataClass\User;
use Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters;
use Chamilo\Libraries\Storage\Query\Condition\Condition;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class RequestManager
{
    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\EphorusWebserviceRepository
     */
    protected $ephorusWebserviceRepository;

    /**
     * @var \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\RequestRepository
     */
    protected $requestRepository;

    /**
     * @var \Chamilo\Core\User\Service\UserService
     */
    protected $userService;

    /**
     * @var \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository
     */
    protected $contentObjectRepository;

    /**
     * RequestManager constructor.
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\EphorusWebserviceRepository $ephorusWebserviceRepository
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\RequestRepository $requestRepository
     * @param \Chamilo\Core\User\Service\UserService $userService
     * @param \Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository $contentObjectRepository
     */
    public function __construct(
        EphorusWebserviceRepository $ephorusWebserviceRepository, RequestRepository $requestRepository,
        UserService $userService, ContentObjectRepository $contentObjectRepository
    )
    {
        $this->ephorusWebserviceRepository = $ephorusWebserviceRepository;
        $this->requestRepository = $requestRepository;
        $this->userService = $userService;
        $this->contentObjectRepository = $contentObjectRepository;
    }

    /**
     * @param int[] $contentObjectIds
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $courseId
     *
     * @return int
     */
    public function handInDocumentsByIds(array $contentObjectIds = [], User $user, $courseId = 0)
    {
        $failures = 0;

        foreach ($contentObjectIds as $contentObjectId)
        {
            $contentObject = $this->contentObjectRepository->findById($contentObjectId);
            if (!$contentObject instanceof File)
            {
                $failures ++;
                continue;
            }

            try
            {
                $this->handInDocumentObject($contentObject, $user, $courseId);
            }
            catch (\Exception $ex)
            {
                $failures ++;
            }
        }

        return $failures;
    }

    /**
     * @param \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File $document
     * @param \Chamilo\Core\User\Storage\DataClass\User $user
     * @param int $courseId
     *
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request
     */
    public function handInDocumentObject(File $document, User $user, $courseId = 0)
    {
        $request = new Request();
        $request->set_process_type(Request::PROCESS_TYPE_CHECK_AND_INVISIBLE);
        $request->set_course_id($courseId);
        $request->set_content_object_id($document->getId());
        $request->set_author_id($document->get_owner_id());
        $request->set_request_user_id($user->getId());

        if (!$request->is_content_object_valid())
        {
            throw new \InvalidArgumentException(
                'The given base request is not valid: ' . implode(PHP_EOL, $request->get_errors())
            );
        }

        $author = $this->retrieveAuthorFromRequest($request);

        $documentGUID = $this->ephorusWebserviceRepository->handInDocument($document, $author);
        if (!$documentGUID)
        {
            throw new \RuntimeException('Could not create the document in the ephorus webservice');
        }

        $request->set_guid($documentGUID);
        $request->set_process_type(Request::PROCESS_TYPE_CHECK_AND_INVISIBLE);

        if (!$this->requestRepository->create($request))
        {
            throw new \RuntimeException(
                'Could not create the request in the database for guid: ' . $request->get_guid()
            );
        }

        return $request;
    }

    /**
     * @param Request[] $requests
     *
     * @return int
     */
    public function changeDocumentsVisibility(array $requests)
    {
        $failures = 0;

        foreach ($requests as $request)
        {
            try
            {
                $this->changeDocumentVisibility($request);
            }
            catch (\Exception $ex)
            {
                $failures ++;
            }
        }

        return $failures;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $request
     */
    public function changeDocumentVisibility(Request $request)
    {
        if (!$this->ephorusWebserviceRepository->changeDocumentVisiblity(
            $request->get_guid(), !$request->is_visible_in_index()
        ))
        {
            throw new \RuntimeException('The given document visibility could not be changed');
        }

        $request->set_visible_on_index(!$request->is_visible_in_index());
        if (!$this->requestRepository->update($request))
        {
            throw new \RuntimeException(
                sprintf(
                    'The given request with guid %s could not be updated in the database',
                    $request->get_guid()
                )
            );
        }
    }

    /**
     * Retrieves a request by a given guid
     *
     * @param string $documentGuid
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass | Request
     */
    public function findRequestByGuid(string $documentGuid)
    {
        if (!$documentGuid)
        {
            throw new \InvalidArgumentException('A valid guid is required to retrieve a request by guid');
        }

        return $this->requestRepository->findRequestByGuid($documentGuid);
    }

    /**
     * Retrieves a request by a given id
     *
     * @param int $id
     *
     * @return \Chamilo\Libraries\Storage\DataClass\CompositeDataClass|\Chamilo\Libraries\Storage\DataClass\DataClass | Request
     */
    public function findRequestById(int $id)
    {
        if (!$id)
        {
            throw new \InvalidArgumentException('A valid id is required to retrieve a request by id');
        }

        return $this->requestRepository->findRequestById($id);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Query\Condition\Condition $condition
     *
     * @return int
     */
    public function countRequestsWithContentObjects(Condition $condition)
    {
        return $this->requestRepository->countRequestsWithContentObjects($condition);
    }

    /**
     * @param \Chamilo\Libraries\Storage\Parameters\RecordRetrievesParameters $recordRetrievesParameters
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findRequestsWithContentObjects(RecordRetrievesParameters $recordRetrievesParameters)
    {
        return $this->requestRepository->findRequestsWithContentObjects($recordRetrievesParameters);
    }

    /**
     * @param int|int[] $guids
     *
     * @return \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findRequestsWithContentObjectsByGuids($guids)
    {
        if (!is_array($guids))
        {
            $guids = [$guids];
        }

        return $this->requestRepository->findRequestsWithContentObjectsByGuids($guids);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $request
     *
     * @return Result[] | \Chamilo\Libraries\Storage\Iterator\DataClassIterator
     */
    public function findResultsForRequest(Request $request)
    {
        return $this->requestRepository->findResultsForRequest($request);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $baseRequest
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | \Chamilo\Core\User\Storage\DataClass\User
     */
    protected function retrieveAuthorFromRequest(Request $baseRequest)
    {
        $author = $this->userService->findUserByIdentifier($baseRequest->get_author_id());

        if (!$author)
        {
            throw new \RuntimeException(
                sprintf('The given author with id %s can not be retrieved', $baseRequest->get_author_id())
            );
        }

        return $author;
    }

}