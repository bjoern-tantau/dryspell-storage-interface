<?php
namespace Dryspell\Storage;

use IteratorAggregate;

/**
 * FindInterface
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
interface FindInterface extends IteratorAggregate
{

    public function where(string $propertyName): WhereInterface;

    public function with(string $propertyName): FindInterface;

    public function sortBy(string $propertyName): SortInterface;

    public function limit(int $count, int $from = 0): FindInterface;
}
