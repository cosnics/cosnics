<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Entity\DataClassEntityFactory;
use Chamilo\Core\Metadata\Service\EntityTranslationFormService;
use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Form\VocabularyForm;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\Utilities;

/**
 * Controller to update the controlled vocabulary
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
class UpdaterComponent extends Manager
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

        $vocabulary_id = Request :: get(self :: PARAM_VOCABULARY_ID);
        $vocabulary = DataManager :: retrieve_by_id(Vocabulary :: class_name(), $vocabulary_id);

        $form = new VocabularyForm(
            $vocabulary,
            new EntityTranslationFormService($vocabulary),
            $this->get_url(array(self :: PARAM_VOCABULARY_ID => $vocabulary_id)));

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $vocabulary->set_value($values[Vocabulary :: PROPERTY_VALUE]);
                $vocabulary->set_default_value(isset($values[Vocabulary :: PROPERTY_DEFAULT_VALUE]) ? 1 : 0);

                $success = $vocabulary->update();

                if ($success)
                {
                    $entity = DataClassEntityFactory :: getInstance()->getEntityFromDataClass($vocabulary);
                    $entityTranslationService = new EntityTranslationService($entity);
                    $success = $entityTranslationService->updateEntityTranslations(
                        $values[EntityTranslationService :: PROPERTY_TRANSLATION]);
                }

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation :: get(
                    $translation,
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
                    self :: PARAM_USER_ID => $vocabulary->get_user_id()));
        }
        else
        {
            $html = array();

            $html[] = $this->render_header();
            $html[] = $form->toHtml();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}