<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\EntryDownloader;
use Chamilo\Libraries\Architecture\Exceptions\UserException;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DownloaderComponent extends Manager
{

    /**
     * @return string|void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    public function run()
    {
        $entryCompressor = new EntryDownloader(
            $this->getAssignmentServiceBridge(), $this->getRightsService(), $this->getArchiveCreator(),
            $this->getUser(), $this->getAssignment()
        );

        try
        {
            $entryCompressor->downloadByRequest($this->getRequest());
        }
        catch(\Exception $exception)
        {
            throw new UserException($this->getTranslator()->trans('EntriesNotDownloadable', [], Manager::context()));
        }
    }

    /**
     * @return \Chamilo\Libraries\File\Compression\ArchiveCreator\ArchiveCreator
     */
    protected function getArchiveCreator()
    {
        return $this->getService('chamilo.libraries.file.compression.archive_creator.archive_creator');
    }
}