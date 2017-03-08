<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Storage\DataClass\Registration;
use Chamilo\Libraries\Architecture\ClassnameUtilities;
use Chamilo\Libraries\Platform\Translation;

/**
 *
 * @package Chamilo\Configuration\Package\Action
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Deactivator extends Action
{

    public function get_data_manager()
    {
        return $this->data_manager;
    }

    public function run()
    {
        $classNameUtilities = ClassnameUtilities::getInstance();
        
        $registration = \Chamilo\Configuration\Storage\DataManager::retrieveRegistrationByContext(
            $classNameUtilities->getNamespaceParent(
                self::context()
            )
        );

        if ($registration->is_active())
        {
            $registration->set_status(Registration::STATUS_INACTIVE);

            if (!$registration->update())
            {
                return $this->failed(Translation::get('DeactivationFailed'));
            }
            else
            {
                $this->add_message(self::TYPE_NORMAL, Translation::get('DeactivationSuccessful'));
            }

            return $this->successful();
        }
        else
        {
            return $this->failed(Translation::get('PackageAlreadyInactive'));
        }
    }

    /**
     * Creates an application-specific installer.
     *
     * @param $context string The namespace of the package for which we want to start the installer.
     * @param $values string The form values passed on by the wizard.
     */
    public static function factory($context)
    {
        $class = $context . '\Package\Deactivator';

        return new $class();
    }
}
