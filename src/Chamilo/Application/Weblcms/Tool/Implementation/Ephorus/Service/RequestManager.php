<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Service;

use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\EphorusWebserviceRepository;
use Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\Repository\RequestRepository;
use Chamilo\Core\Repository\Workspace\Repository\ContentObjectRepository;
use Chamilo\Core\User\Service\UserService;
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
     * Hand in documents based on a base_request
     *
     * @param Request[] $base_requests
     *
     * @return int
     */
    public function handInDocuments($base_requests)
    {
        if (!is_array($base_requests))
        {
            $base_requests = array($base_requests);
        }

        $failures = 0;

        foreach ($base_requests as $base_request)
        {
            try
            {
                $this->handInDocument($base_request);
            }
            catch (\Exception $ex)
            {
                $failures ++;
            }
        }

        return $failures;
    }

    /**
     * Sends a request to ephorus and saves the reference in the chamilo database
     *
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $baseRequest
     */
    public function handInDocument(Request $baseRequest)
    {
        if (!$baseRequest->is_content_object_valid())
        {
            throw new \InvalidArgumentException(
                'The given base request is not valid: ' . implode(PHP_EOL, $baseRequest->get_errors())
            );
        }

        $file = $this->retrieveFileFromRequest($baseRequest);
        $author = $this->retrieveAuthorFromRequest($baseRequest);

        $documentGUID = $this->ephorusWebserviceRepository->handInDocument($file, $author);
        if (!$documentGUID)
        {
            throw new \RuntimeException('Could not create the document in the ephorus webservice');
        }

        $baseRequest->set_guid($documentGUID);
        $baseRequest->set_process_type(Request::PROCESS_TYPE_CHECK_AND_INVISIBLE);

        if (!$this->requestRepository->create($baseRequest))
        {
            throw new \RuntimeException(
                'Could not create the base request in the database for guid: ' . $baseRequest->get_guid()
            );
        }
    }

    /**
     * Changes the visibility based on index_type index_type = 1 => show index_type = 2 => hide
     *
     * @param string[] $documentGuids
     *
     * @return boolean
     */
    public function changeVisibilityOfDocumentsOnIndex(array $documentGuids)
    {
        $failures = 0;

        foreach ($documentGuids as $documentGuid => $showOnIndex)
        {
            try
            {
                $this->changeDocumentVisibilityOnIndex($documentGuid, $showOnIndex);
            }
            catch(\Exception $ex)
            {
                $failures++;
            }
        }

        return $failures;
    }

    /**
     * @param string $documentGuid
     * @param bool $showOnIndex
     *
     * @throws \Exception
     */
    public function changeDocumentVisibilityOnIndex(string $documentGuid, bool $showOnIndex)
    {
        if (!$this->ephorusWebserviceRepository->changeDocumentVisiblity($documentGuid, $showOnIndex))
        {
            throw new \RuntimeException('The given document visibility could not be changed');
        }

        $request = $this->requestRepository->findRequestByGuid($documentGuid);

        $request->set_visible_on_index($showOnIndex);
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
        if (! $documentGuid)
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
        if (! $id)
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
        if(!is_array($guids))
        {
            $guids = [$guids];
        }

        return $this->requestRepository->findRequestsWithContentObjectsByGuids($guids);
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $baseRequest
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | \Chamilo\Core\Repository\ContentObject\File\Storage\DataClass\File
     */
    private function retrieveFileFromRequest(Request $baseRequest)
    {
        $file = $this->contentObjectRepository->findById($baseRequest->get_content_object_id());

        if (!$file)
        {
            throw new \RuntimeException(
                sprintf('The given document with id %s can not be retrieved', $baseRequest->get_content_object_id())
            );
        }

        return $file;
    }

    /**
     * @param \Chamilo\Application\Weblcms\Tool\Implementation\Ephorus\Storage\DataClass\Request $baseRequest
     *
     * @return \Chamilo\Libraries\Storage\DataClass\DataClass | \Chamilo\Core\User\Storage\DataClass\User
     */
    private function retrieveAuthorFromRequest(Request $baseRequest)
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