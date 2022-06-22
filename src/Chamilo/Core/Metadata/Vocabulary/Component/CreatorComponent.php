<?php
namespace Chamilo\Core\Metadata\Vocabulary\Component;

use Chamilo\Core\Metadata\Service\EntityTranslationService;
use Chamilo\Core\Metadata\Storage\DataClass\Vocabulary;
use Chamilo\Core\Metadata\Vocabulary\Form\VocabularyForm;
use Chamilo\Core\Metadata\Vocabulary\Manager;
use Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException;
use Chamilo\Libraries\Architecture\Exceptions\NotAllowedException;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;
use Exception;

/**
 * Controller to create the schema
 */
class CreatorComponent extends Manager
{

    /**
     * @return string
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NoObjectSelectedException
     * @throws \Chamilo\Libraries\Architecture\Exceptions\NotAllowedException
     * @throws \Exception
     */
    public function run()
    {
        if (!$this->getUser()->is_platform_admin())
        {
            throw new NotAllowedException();
        }

        if (is_null($this->getSelectedElementId()))
        {
            throw new NoObjectSelectedException(Translation::get('Element', null, 'Chamilo\Core\Metadata\Element'));
        }

        if (is_null($this->getSelectedUserId()))
        {
            throw new NoObjectSelectedException(Translation::get('User', null, 'Chamilo\Core\Metadata\Vocabulary'));
        }

        $vocabulary = new Vocabulary();
        $vocabulary->set_element_id($this->getSelectedElementId());
        $vocabulary->set_user_id($this->getSelectedUserId());

        $form = new VocabularyForm(
            $vocabulary, $this->get_url(
            array(
                \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $this->getSelectedElementId(),
                self::PARAM_USER_ID => $this->getSelectedUserId()
            )
        )
        );

        if ($form->validate())
        {
            try
            {
                $values = $form->exportValues();

                $vocabulary->set_value($values[Vocabulary::PROPERTY_VALUE]);
                $vocabulary->set_default_value(isset($values[Vocabulary::PROPERTY_DEFAULT_VALUE]) ? 1 : 0);

                $success = $vocabulary->create();

                if ($success)
                {
                    $entity = $this->getDataClassEntityFactory()->getEntityFromDataClass($vocabulary);
                    $success = $this->getEntityTranslationService()->createEntityTranslations(
                        $entity, $values[EntityTranslationService::PROPERTY_TRANSLATION]
                    );
                }

                $translation = $success ? 'ObjectCreated' : 'ObjectNotCreated';

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
                    \Chamilo\Core\Metadata\Element\Manager::PARAM_ELEMENT_ID => $this->getSelectedElementId(),
                    self::PARAM_USER_ID => $this->getSelectedUserId()
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