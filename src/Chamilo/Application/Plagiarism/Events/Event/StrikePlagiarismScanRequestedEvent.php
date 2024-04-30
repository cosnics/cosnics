<?php

namespace Chamilo\Application\Plagiarism\Events\Event;

use Chamilo\Application\Plagiarism\API\StrikePlagiarism\Model\Request\UploadDocumentRequestParameters;
use Symfony\Contracts\EventDispatcher\Event;

class StrikePlagiarismScanRequestedEvent extends Event
{
    protected UploadDocumentRequestParameters $requestParameters;

    public function __construct(UploadDocumentRequestParameters $requestParameters)
    {
        $this->requestParameters = $requestParameters;
    }

    public function getRequestParameters(): UploadDocumentRequestParameters
    {
        return $this->requestParameters;
    }
}