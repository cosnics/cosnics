<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Chamilo\Libraries\Utilities\StringUtilities;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Configuration\Package\Properties\Dependencies
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyVerifierRenderer
{
    public const TYPE_CONFIRM = 1;
    public const TYPE_ERROR = 2;
    public const TYPE_WARNING = 3;

    protected DependencyRenderer $dependencyRenderer;

    protected DependencyVerifier $dependencyVerifier;

    protected Translator $translator;

    public function __construct(
        DependencyRenderer $dependencyRenderer, DependencyVerifier $dependencyVerifier, Translator $translator
    )
    {
        $this->dependencyRenderer = $dependencyRenderer;
        $this->dependencyVerifier = $dependencyVerifier;
        $this->translator = $translator;
    }

    public function getDependencyRenderer(): DependencyRenderer
    {
        return $this->dependencyRenderer;
    }

    public function getDependencyVerifier(): DependencyVerifier
    {
        return $this->dependencyVerifier;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function renderMessage(array $parameters, int $type): string
    {
        $translator = $this->getTranslator();

        return match ($type)
        {
            self::TYPE_CONFIRM => '<span class="text-success"><strong>' .
                $translator->trans('CurrentDependency', $parameters, 'Chamilo\Configuration') . '</strong></span>',
            self::TYPE_WARNING => '<span class="text-warning"><strong>' .
                $translator->trans('CurrentDependency', $parameters, 'Chamilo\Configuration') . '</strong></span>',
            self::TYPE_ERROR => '<span class="text-danger"><strong>' .
                $translator->trans('CurrentDependency', $parameters, 'Chamilo\Configuration') . '</strong></span>',
        };
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderVerifiedDependencies(Package $package): string
    {
        $dependencies = $package->get_dependencies();
        $messages = [];

        if ($dependencies instanceof Dependencies)
        {
            foreach ($dependencies as $dependency)
            {
                $messages[] = $this->renderVerifiedDependency($dependency);
            }
        }

        return implode(
            ' ' . $this->getTranslator()->trans('And', [], 'Chamilo\Configuration') . ' ', $messages
        );
    }

    /**
     * @throws \Symfony\Component\Cache\Exception\CacheException
     */
    public function renderVerifiedDependency($dependency): string
    {
        $translator = $this->getTranslator();
        $parameters = [];

        $parameters['{REQUIREMENT}'] = $this->getDependencyRenderer()->renderDependency($dependency);

        if (!$this->getDependencyVerifier()->dependencyHasRegistration($dependency))
        {
            $parameters['{CURRENT}'] = '--' . $translator->trans('Nothing', [], StringUtilities::LIBRARIES) . '--';

            return $this->renderMessage($parameters, self::TYPE_ERROR);
        }
        elseif (!$this->getDependencyVerifier()->dependencyHasValidRegisteredVersion($dependency))
        {
            $parameters['{CURRENT}'] = '--' . $translator->trans('WrongVersion', [], StringUtilities::LIBRARIES) . '--';

            return $this->renderMessage($parameters, self::TYPE_ERROR);
        }
        elseif (!$this->getDependencyVerifier()->dependencyHasActiveRegistration($dependency))
        {
            $parameters['{CURRENT}'] =
                '--' . $translator->trans('InactiveObject', [], StringUtilities::LIBRARIES) . '--';

            return $this->renderMessage($parameters, self::TYPE_ERROR);
        }
        else
        {
            return $parameters['{REQUIREMENT}'];
        }
    }
}
