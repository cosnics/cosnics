<?php
namespace Chamilo\Libraries\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Translation\Translator;

/**
 * @author Sven Vanpoucke - Hogeschool Gent
 * @package Chamilo\Libraries\Console\Command
 */
abstract class ChamiloCommand extends Command
{

    protected Translator $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
        parent::__construct();
    }
}