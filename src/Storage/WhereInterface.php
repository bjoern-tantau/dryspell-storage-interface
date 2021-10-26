<?php
namespace Dryspell\Storage;

/**
 * WhereInterface
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
interface WhereInterface
{

    public function isNull(): FindInterface;

    public function isNotNull(): FindInterface;

    public function equals($value): FindInterface;

    public function contains($value): FindInterface;

    public function startsWith($value): FindInterface;

    public function endsWith($value): FindInterface;

    public function isGreaterThan($value): FindInterface;

    public function isGreaterThanOrEquals($value): FindInterface;

    public function isLowerThan($value): FindInterface;

    public function isLowerThanOrEquals($value): FindInterface;

    public function equalsOneOf(array $values): FindInterface;
}
