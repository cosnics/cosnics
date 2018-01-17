<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Service\EntryDownloader;

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
     */
    public function run()
    {
        $entryCompressor = new EntryDownloader($this->getDataProvider(), $this->get_root_content_object());
        $entryCompressor->downloadByRequest($this->getRequest());
    }
}