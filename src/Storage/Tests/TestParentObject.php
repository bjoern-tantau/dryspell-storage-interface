<?php
namespace Dryspell\Storage\Tests;

use Dryspell\Models\BaseObject;

/**
 * TestParentObject
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
class TestParentObject extends BaseObject
{

    public string $name;
    public \DateTime $testDate;
    public ?string $nullable;

}
