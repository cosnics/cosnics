<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Form\VocabularyForm;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Core\Metadata\Vocabulary\Storage\DataManager;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to update the controlled vocabulary
 *
 * @author Sven Vanpoucke - Hogeschool Gent
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

        $vocabulary_id = Request::get(self::PARAM_VOCABULARY_ID);
        $vocabulary = DataManager::retrieve_by_id(Vocabulary::class, $vocabulary_id);

        $form = new VocabularyForm(
            $vocabulary, $this->get_url(array(self::PARAM_VOCABULARY_ID => $vocabulary_id))
        );

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $vocabulary->set_value($values[Vocabulary::PROPERTY_VALUE]);
                $vocabulary->set_default_value(isset($values[Vocabulary::PROPERTY_DEFAULT_VALUE]) ? 1 : 0);

                $success = $vocabulary->update();

                if ($success)
                {
                    $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($vocabulary);
                    $success = $this->getEntityTranslationService()->updateEntityTranslations(
                        $entity, $values[EntityTranslationService::PROPERTY_TRANSLATION]
                    );
                }

                $translation = $success ? 'ObjectUpdated' : 'ObjectNotUpdated';

                $message = Translation::get(
                    $translation, array('OBJECT' => Translation::get('Vocabulary')), StringUtilities::LIBRARIES
                );
            }
            catch (Exception $ex)
            {
                $success = false;
                $message = $ex->getMessage();
            }

            $this->redirectWithMessage(
                $message, !$success, array(
                    self::PARAM_ACTION => self::ACTION_BROWSE,
                    \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $vocabulary->get_element_id(),
                    self::PARAM_USER_ID => $vocabulary->get_user_id()
                )
            );
        }
        else
        {
            $html = [];

            $html[] = $this->render_header();
            $html[] = $form->render();
            $html[] = $this->render_footer();

            return implode(PHP_EOL, $html);
        }
    }
}