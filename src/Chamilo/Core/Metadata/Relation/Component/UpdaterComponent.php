<?php
namespace Chamilo\Core\Metadata\Relation\Component;

use Chamilo\Core\Metadata\Relation\Form\RelationForm;
use Chamilo\Core\Metadata\Relation\Manager;
use Chamilo\Core\Metadata\Relation\Storage\DataManager;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\Utilities;
use Exception;

/**
 * Controller to update the schema
 *
 * @package Chamilo\Core\Metadata\Relation\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class UpdaterComponent extends Manager
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\UserException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $relation_id = Request::get(self::PARAM_RELATION_ID);
        $this->set_parameter(self::PARAM_RELATION_ID, $relation_id);

        $relation = DataManager::retrieve_by_id(Relation::class, $relation_id);

        $form = new RelationForm($relation, $this->get_url());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $relation->set_name($values[Relation::PROPERTY_NAME]);
                $success = $relation->update();

                if ($success)
                {
                    $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($relation);
                    $success = $this->getEntityTranslationService()->updateEntityTranslations(
                        $entity, $values[EntityTranslationService::PROPERTY_TRANSLATION]
                    );
                }

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation::get(
                    $translation, array('OBJECT' => Translation::get('Relation')), Utilities::COMMON_LIBRARIES
                );
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirect($message, !$success, array(self::PARAM_ACTION => self::ACTION_BROWSE));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}