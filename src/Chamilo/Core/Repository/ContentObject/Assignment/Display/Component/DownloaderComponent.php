<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DownloaderComponent extends Manager
{

    public function run()
    {
        var_dump($this->getRequest()->query);
        var_dump($this->getRequest()->request);

        $entryDownloader = new EntryDownloader($this->getDataProvider(),
                $this->getUser());
    }
}