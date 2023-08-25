<?php
namespace Chamilo\Configuration\Package\Action;

use Chamilo\Configuration\Package\Action;
use Chamilo\Configuration\Service\RegistrationService;

/**
 * @package Chamilo\Configuration\Package\Action
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Activator extends Action
{
    public function run(): bool
    {
        $translator = $this->getTranslator();

        if (!$this->getRegistrationService()->activateRegistrationForContext(static::CONTEXT))
        {
            return $this->failed($translator->trans('ActivationFailed', [], 'Chamilo\Configuration\Package'));
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans('ActivationSuccessful', [], 'Chamilo\Configuration\Package')
            );
        }

        return $this->successful();
    }

    public static function factory(string $context): Activator
    {
        $class = $context . '\Package\Activator';

        return new $class();
    }

    public function getRegistrationService(): RegistrationService
    {
        return $this->getService(RegistrationService::class);
    }
}
