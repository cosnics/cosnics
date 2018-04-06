<?php

namespace Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component;

use Chamilo\Application\Weblcms\Rights\WeblcmsRights;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Manager;
use Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntriesDownloader\EntriesDownloaderFactory;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;

/**
 * @package Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class EntriesDownloaderComponent extends Manager
{
    const PARAM_ENTRIES_DOWNLOAD_STRATEGY = 'EntriesDownloadStrategy';

    /**
     * Runs this component and returns its output
     *
     * @return string|void
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\ObjectNotExistException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    public function run()
    {
        if (!$this->is_allowed(WeblcmsRights::EDIT_RIGHT))
        {
            throw new NotAllowedException();
        }

        $publicationIdentifiers =
            $this->getRequest()->getFromPostOrUrl(\Chamilo\Application\Weblcms\Manager::PARAM_PUBLICATION);

        $factory = $this->getEntriesDownloaderFactory();

        $strategy = $this->getRequest()->getFromUrl(
            self::PARAM_ENTRIES_DOWNLOAD_STRATEGY, EntriesDownloaderFactory::ENTRIES_DOWNLOADER_DEFAULT
        );

        $entriesDownloader = $factory->getEntriesDownloader($strategy);
        $entriesDownloader->downloadEntriesForAssignmentsByPublicationIdentifiers(
            $publicationIdentifiers, $this->getRequest(), $this->get_course()
        );
    }

    /**
     * @return \Chamilo\Application\Weblcms\Tool\Implementation\Assignment\Service\EntriesDownloader\EntriesDownloaderFactory
     */
    protected function getEntriesDownloaderFactory()
    {
        return $this->getService(
            'chamilo.application.weblcms.tool.implementation.assignment.service.entries_downloader.entries_downloader_factory'
        );
    }
}