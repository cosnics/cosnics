<?php
namespace Chamilo\Core\Metadata\Relation\Component;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Relation\Form\RelationForm;
use Chamilo\Core\Metadata\Relation\Manager;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Relation;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to create the schema
 *
 * @package Chamilo\Core\Metadata\Schema\Component
 * @author Sven Vanpoucke - Hogeschool Gent
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class CreatorComponent extends Manager
{

    /**
     * Executes this controller
     */
    public function run()
    {
        if (!$this->get_user()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        $relation = new Relation();

        $form = new RelationForm($relation, $this->get_url());

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $relation->set_name($values[Relation::PROPERTY_NAME]);
                $success = $relation->create();

                if ($success)
                {
                    $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($relation);
                    $success = $this->getEntityTranslationService()->createEntityTranslations(
                        $entity, $values[EntityTranslationService::PROPERTY_TRANSLATION]
                    );
                }

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

                $message = Translation::get(
                    $translation, array('OBJECT' => Translation::get('Relation')), StringUtilities::LIBRARIES
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
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}