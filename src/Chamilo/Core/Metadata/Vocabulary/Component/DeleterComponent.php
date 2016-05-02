<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to delete the schema
 */
class DeleterComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $vocabulary_ids = $this->getRequest()->get(self :: PARAM_VOCABULARY_ID);

        try
        {
            if (empty($vocabulary_ids))
            {
                throw new NoObjectSelectedException(Translation :: get('Vocabulary'));
            }

            if (! is_array($vocabulary_ids))
            {
                $vocabulary_ids = array($vocabulary_ids);
            }

            foreach ($vocabulary_ids as $vocabulary_id)
            {
                $vocabulary = DataManager :: retrieve_by_id(Vocabulary :: class_name(), $vocabulary_id);

                if (! $vocabulary->delete())
                {
                    throw new \Exception(
                        Translation :: get(
                            'ObjectNotDeleted',
                            array('OBJECT' => Translation :: get('Vocabulary')),
                            Utilities :: COMMON_LIBRARIES));
                }
            }

            $success = true;
            $message = Translation :: get(
                'ObjectDeleted',
                array('OBJECT' => Translation :: get('Vocabulary')),
                Utilities :: COMMON_LIBRARIES);
        }
        catch (\Exception $ex)
        {
            $success = false;
            $message = $ex->getMessage();
        }

        $this->redirect(
            $message,
            ! $success,
            array(
                self :: PARAM_ACTION => self :: ACTION_BROWSE,
                \Chamilo\Core\Metadata\Element\Manager :: PARAM_ELEMENT_ID => $vocabulary->get_element_id(),
                \Chamilo\Core\Metadata\Vocabulary\Manager :: PARAM_USER_ID => $vocabulary->get_user_id()));
    }
}