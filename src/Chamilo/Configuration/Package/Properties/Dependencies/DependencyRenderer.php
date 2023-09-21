<?php
namespace Chamilo\Configuration\Package\Properties\Dependencies;

use Chamilo\Configuration\Package\Properties\Dependencies\Dependency\Dependency;
use Chamilo\Configuration\Package\Storage\DataClass\Package;
use Symfony\Component\Translation\Translator;

/**
 * @package Chamilo\Configuration\Package\Properties\Dependencies
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DependencyRenderer
{

    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    public function renderDependencies(Package $package): string
    {
        $dependencies = $package->get_dependencies();
        $messages = [];

        if ($dependencies instanceof Dependencies)
        {
            foreach ($dependencies as $dependency)
            {
                $messages[] = $this->renderDependency($dependency);
            }
        }

        return implode(
            ' ' . $this->getTranslator()->trans('And', [], 'Chamilo\Configuration') . ' ', $messages
        );
    }

    public function renderDependency(Dependency $dependency): string
    {
        return $this->getTranslator()->trans(
            'Dependency', ['{ID}' => $dependency->get_id(), '{VERSION}' => $dependency->get_version()],
            'Chamilo\Configuration'
        );
    }
}
