<?php
namespace Chamilo\Libraries\Service\Bootstrap;

use Chamilo\Libraries\Architecture\Interface\ApplicationInterface;
use Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException;
use Chamilo\Libraries\Service\Utilities\StringUtilities;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Libraries\Service\Bootstrap
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 */
class ApplicationFactory
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<string,\Doctrine\Common\Collections\ArrayCollection<string,\Chamilo\Libraries\Architecture\Interface\ApplicationInterface>>
     */
    protected ArrayCollection $contextComponents;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection<string>
     */
    protected ArrayCollection $contextDefaults;

    public function __construct(protected Translator $translator)
    {
        $this->contextComponents = new ArrayCollection();
        $this->contextDefaults = new ArrayCollection();
    }

    public function addApplicationComponent(ApplicationInterface $application): void
    {
        $applicationComponents = $this->getContextComponents();
        $defaultContextActions = $this->getContextDefaults();
        $context = $application->getApplicationContext();

        if (!$applicationComponents->containsKey($context)) {
            $applicationComponents->set($context, new ArrayCollection());
        }

        $applicationComponents->get($context)->set($application->getApplicationAction(), $application);

        if (!$defaultContextActions->containsKey($context)) {
            $defaultContextActions->set($context, $application->getDefaultApplicationAction());
        }
    }

    /**
     * @throws \Chamilo\Libraries\Protocol\ExceptionHandling\Architecture\Exception\UserException
     */
    public function getApplicationComponent(string $context, ?string $action = null): ApplicationInterface
    {
        if (!isset($action)) {
            $action = $this->getContextDefaults()->get($context);
        }

        $applicationComponents = $this->getContextComponents();

        if (!$applicationComponents->containsKey($context)) {
            throw new UserException(
                $this->translator->trans(
                    'InvalidApplicationContext', ['%Context%' => $context], StringUtilities::LIBRARIES
                )
            );
        }

        $contextComponents = $applicationComponents->get($context);

        if (!$contextComponents->containsKey($action)) {
            throw new UserException(
                $this->translator->trans(
                    'InvalidApplicationAction', ['%Context%' => $context, '%Action%' => $action],
                    StringUtilities::LIBRARIES
                )
            );
        }

        return $contextComponents->get($action);
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<string,\Doctrine\Common\Collections\ArrayCollection<string,\Chamilo\Libraries\Architecture\Interface\ApplicationInterface>>
     */
    public function getContextComponents(): ArrayCollection
    {
        return $this->contextComponents;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection<string>
     */
    public function getContextDefaults(): ArrayCollection
    {
        return $this->contextDefaults;
    }
}
