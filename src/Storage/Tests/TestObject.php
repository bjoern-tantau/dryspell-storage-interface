<?php
namespace Dryspell\Storage\Tests;

use Dryspell\Models\BaseObject;

/**
 * TestObject
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
class TestObject extends BaseObject
{

    public string $name;
    public TestParentObject $parent;

}
