<?php
namespace Chamilo\Core\Repository\ContentObject\Glossary\Display;

/**
 * @package Chamilo\Core\Repository\ContentObject\Glossary\Display
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
interface GlossaryDisplayContextProviderInterface
{
    public function countItems(): int;

    public function getItems($offset, $count, $orderBy);

    public function isAllowedToDeleteItem(): bool;

    public function isAllowedtoEdit(): bool;
}