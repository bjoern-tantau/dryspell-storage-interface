<?php
namespace Dryspell\Storage;

/**
 * SortInterface
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
interface SortInterface
{

    public function ascending(): FindInterface;

    public function descending(): FindInterface;
}
