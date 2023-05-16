<?php
namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Exception;

/**
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class DeleterComponent extends Manager
{

    /**
     * @return string|void
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    public function run()
    {
        $this->checkAccessRights();
        $entryIdentifiers = $this->initializeEntryIdentifiers();

        try
        {
            $this->deleteEntriesByIdentifiers($entryIdentifiers);

            $success = true;
            $message = 'EntryDeleted';
        }
        catch (Exception $ex)
        {
            $success = false;
            $message = 'EntryNotDeleted';
        }

        $this->redirectWithMessage(
            Translation::get($message),
            !$success,
            array(
                self::PARAM_ACTION => self::ACTION_ENTRY, self::PARAM_ENTITY_ID => $this->getEntityIdentifier(),
                self::PARAM_ENTITY_TYPE => $this->getEntityType()
            )
        );
    }

    /**
     * @return int[]
     *
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     */
    protected function initializeEntryIdentifiers()
    {
        $entryIdentifiers = $this->getRequest()->getFromRequestOrQuery(self::PARAM_ENTRY_ID);

        if (empty($entryIdentifiers))
        {
            throw new NoObjectSelectedException(Translation::get('Entry'));
        }
        else
        {
            $this->set_parameter(self::PARAM_ENTRY_ID, $entryIdentifiers);
        }

        if (!is_array($entryIdentifiers))
        {
            $entryIdentifiers = array($entryIdentifiers);
        }

        return $entryIdentifiers;
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        throw new NotAllowedException();

//        if (!$this->getRightsService()->canUserDeleteEntries($this->getUser(), $this->getAssignment()))
//        {
//            throw new NotAllowedException();
//        }
    }

    /**
     * @param $entryIdentifiers
     */
    protected function deleteEntriesByIdentifiers($entryIdentifiers)
    {
        foreach ($entryIdentifiers as $entryIdentifier)
        {
            $entry = $this->getDataProvider()->findEntryByIdentifier($entryIdentifier);
            if (!$entry instanceof Entry)
            {
                continue;
            }

            $this->getDataProvider()->deleteEntry($entry);
        }
    }
}
