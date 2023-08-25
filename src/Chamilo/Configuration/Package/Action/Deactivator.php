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
abstract class Deactivator extends Action
{
    public function run(): bool
    {
        $translator = $this->getTranslator();

        if (!$this->getRegistrationService()->deactivateRegistrationForContext(static::CONTEXT))
        {
            return $this->failed($translator->trans('DeactivationFailed', [], 'Chamilo\Configuration\Package'));
        }
        else
        {
            $this->add_message(
                self::TYPE_NORMAL, $translator->trans('DeactivationSuccessful', [], 'Chamilo\Configuration\Package')
            );
        }
    }

    public static function factory(string $context): Deactivator
    {
        $class = $context . '\Package\Deactivator';

        return new $class();
    }

    public function getRegistrationService(): RegistrationService
    {
        return $this->getService(RegistrationService::class);
    }
}
