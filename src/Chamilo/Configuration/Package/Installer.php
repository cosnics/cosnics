<?php
namespace Chamilo\Configuration\Package;

use Chamilo\Configuration\Service\Consulter\LanguageConsulter;
use Chamilo\Configuration\Storage\DataClass\Language;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 * @package admin.install
 */

/**
 * This installer can be used to create the storage structure for the users application.
 */
class Installer extends Action\Installer
{
    public const CONTEXT = 'Chamilo\Configuration';

    public function createLanguages(): bool
    {
        $languages = $this->getLanguageConsulter()->getLanguagesFromFilesystem();

        foreach ($languages as $language)
        {
            $language = new Language($language);
            $language->set_available('1');

            if ($language->create())
            {
                $this->add_message(
                    self::TYPE_NORMAL, Translation::get(
                        'ObjectAdded', ['OBJECT' => Translation::get('Language')], StringUtilities::LIBRARIES
                    ) . ' ' . $language->get_english_name()
                );
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Runs the install-script.
     */
    public function extra(): bool
    {

        // Add the default language entries in the database
        if (!$this->createLanguages())
        {
            return false;
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, Translation::get(
                'ObjectsAdded', ['OBJECTS' => Translation::get('Languages')], StringUtilities::LIBRARIES
            )
            );
        }

        return true;
    }

    public function getLanguageConsulter(): LanguageConsulter
    {
        return $this->getService(LanguageConsulter::class);
    }
}
