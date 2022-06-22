<?php

namespace Chamilo\Core\Repository\ContentObject\Assignment\Display\Component;

use Chamilo\Core\Repository\ContentObject\Assignment\Display\Manager;
use Chamilo\Core\Repository\ContentObject\Assignment\Display\Bridge\Storage\DataClass\Entry;
use Chamilo\Core\Repository\ContentObject\Assignment\Storage\DataClass\Assignment;
use Chamilo\Libraries\Architecture\Application\ApplicationConfiguration;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Architecture\Exceptions\UserException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Core\Repository\ContentObject\Assignment\Display\Component
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{
    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        if(empty($this->get_allowed_content_object_types()))
        {
            throw new UserException($this->getTranslator()->trans('NoSubmissionPossible', [], Manager::context()));
        }

        $this->checkAccessRights();

        $this->verifyStartEndTime();

        $this->set_parameter(self::PARAM_ENTITY_TYPE, $this->getEntityType());
        $this->set_parameter(self::PARAM_ENTITY_ID, $this->getEntityIdentifier());

        if (\Chamilo\Core\Repository\Viewer\Manager::is_ready_to_be_published())
        {
            $objects = \Chamilo\Core\Repository\Viewer\Manager::get_selected_objects();
            if(is_array($objects))
            {
                $objects = $objects[0];
            }

            $entry = $this->getDataProvider()->createEntry(
                $this->getEntityType(),
                $this->getEntityIdentifier(),
                $this->getUser()->getId(),
                $objects,
                $this->getRequest()->server->get('REMOTE_ADDR')
            );

            if ($entry instanceof Entry)
            {
                $this->redirectWithMessage(
                    Translation::get('EntryCreated'),
                    false,
                    array(
                        self::PARAM_ACTION => self::ACTION_CREATE_CONFIRMATION,
                        self::PARAM_ENTITY_TYPE => $entry->getEntityType(),
                        self::PARAM_ENTITY_ID => $entry->getEntityId()
                    )
                );
            }
            else
            {
                $this->redirectWithMessage(
                    Translation::get('EntryNotCreated'),
                    true,
                    array(self::PARAM_ACTION => self::ACTION_VIEW)
                );
            }
        }
        else
        {
            $component = $this->getApplicationFactory()->getApplication(
                \Chamilo\Core\Repository\Viewer\Manager::context(),
                new ApplicationConfiguration($this->getRequest(), $this->get_user(), $this)
            );
            $component->set_maximum_select(\Chamilo\Core\Repository\Viewer\Manager::SELECT_SINGLE);

            return $component->run();
        }
    }

    /**
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     */
    protected function checkAccessRights()
    {
        if (!$this->getRightsService()->canUserCreateEntry(
            $this->getUser(), $this->getAssignment(), $this->getEntityType(), $this->getEntityIdentifier()
        ))
        {
            throw new NotAllowedException();
        }
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render_header($pageTitle = '')
    {
        $parameters = [
            'HEADER' => parent::render_header($pageTitle),
            'CHANGE_ENTITY_URL' => $this->get_url([self::PARAM_ENTITY_ID => '__ENTITY_ID__'])
        ];

        $parameters = $this->getAvailableEntitiesParameters($parameters);

        return $this->getTwig()->render(
            Manager::context() . ':CreatorWizardHeader.html.twig', $parameters
        );
    }

    /**
     * @return bool
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     */
    protected function verifyStartEndTime()
    {
        /** @var Assignment $assignment */
        $assignment = $this->get_root_content_object();

        if ($assignment->get_start_time() > time())
        {
            $date = DatetimeUtilities::getInstance()->formatLocaleDate(
                Translation::get('DateFormatShort', null, StringUtilities::LIBRARIES) . ', ' .
                Translation::get('TimeNoSecFormat', null, StringUtilities::LIBRARIES),
                $assignment->get_start_time()
            );

            $message = Translation::get('AssignmentNotStarted') . ' - ' . Translation::get('StartTime') . ': ' . $date;

            throw new UserException($message);
        }

        if ($assignment->get_end_time() < time() && $assignment->get_allow_late_submissions() == 0)
        {
            $date = DatetimeUtilities::getInstance()->formatLocaleDate(
                Translation::get('DateFormatShort', null, StringUtilities::LIBRARIES) . ', ' .
                Translation::get('TimeNoSecFormat', null, StringUtilities::LIBRARIES),
                $assignment->get_end_time()
            );

            $message = Translation::get('AssignmentEnded') . ' - ' . Translation::get('EndTime') . ': ' . $date;

            throw new UserException($message);
        }

        return true;
    }

    public function get_allowed_content_object_types()
    {
        $types = $this->get_root_content_object()->get_allowed_types();
        if(empty($types))
        {
            return [];
        }

        return explode(',', $types);
    }
}
