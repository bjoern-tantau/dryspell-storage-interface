<?php
namespace Dryspell\Storage\Tests;

use Dryspell\Storage\StorageInterface;
use Dryspell\Storage\StorageSetupInterface;
use PHPUnit\Framework\TestCase;

/**
 * AbstractTest
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
abstract class AbstractTest extends TestCase
{

    abstract protected function getStorage(): StorageInterface;

    abstract protected function getSetupStorage(): StorageSetupInterface;

    protected function getTestObjects(): array
    {
        $objects = [];

        $objects['parents'][0]           = new TestParentObject();
        $objects['parents'][0]->name     = 'First Parent';
        $objects['parents'][0]->nullable = 'Not Null';

        $objects['parents'][1]       = new TestParentObject();
        $objects['parents'][1]->name = 'Second Parent';

        $objects['children'][0]         = new TestObject();
        $objects['children'][0]->name   = 'First Child';
        $objects['children'][0]->parent = $objects['parents'][0];

        $objects['children'][1]         = new TestObject();
        $objects['children'][1]->name   = 'Second Child';
        $objects['children'][1]->parent = $objects['parents'][1];

        $objects['children'][2]         = new TestObject();
        $objects['children'][2]->name   = 'Third Child';
        $objects['children'][2]->parent = $objects['parents'][0];

        return $objects;
    }

    public function testSetup()
    {
        $this->getSetupStorage()->setup(TestParentObject::class);
        $this->getSetupStorage()->setup(TestObject::class);
    }

    /**
     * @depends testSetup
     */
    public function testCreateObjects()
    {
        $objects = $this->getTestObjects();
        $this->assertArrayHasKey('parents', $objects);
        $this->assertArrayHasKey('children', $objects);

        $this->assertCount(2, $objects['parents']);
        $expectedId = 0;
        foreach ($objects['parents'] as $parent) {
            $expectedId++;
            $now = new \DateTime();
            $this->getStorage()->save($parent);
            $this->assertEquals($expectedId, $parent->id);
            $this->assertEquals($now->getTimestamp(), $parent->created_at->getTimestamp());
            $this->assertEquals($now->getTimestamp(), $parent->updated_at->getTimestamp());
        }

        $this->assertCount(3, $objects['children']);
        $expectedId = 0;
        foreach ($objects['children'] as $child) {
            $expectedId++;
            $now = new \DateTime();
            $this->getStorage()->save($child);
            $this->assertEquals($expectedId, $child->id);
            $this->assertEquals($now->getTimestamp(), $child->created_at->getTimestamp());
            $this->assertEquals($now->getTimestamp(), $child->updated_at->getTimestamp());
        }

        return $objects;
    }

    /**
     *
     * @param array $objects
     *
     * @depends testCreateObjects
     */
    public function testUpdateObjects(array $objects)
    {
        /* @var $object TestParentObject */
        $object       = $objects['parents'][0];
        $object->name = 'First Parent Updated';

        sleep(1); // Sleep one second so that updated_at has to change
        $now = new \DateTime();
        $this->getStorage()->save($object);
        $this->assertEquals($now->getTimestamp(), $object->updated_at->getTimestamp());
        $this->assertNotEquals($object->created_at->getTimestamp(), $object->updated_at->getTimestamp());

        foreach ($objects['children'] as $child) {
            unset($child->parent);
        }
        return $objects;
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindWithNulls(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('nullable')
                ->isNull()
        );

        $expected = [
            $objects['parents'][1],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindWithoutNulls(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('nullable')
                ->isNotNull()
        );

        $expected = [
            $objects['parents'][0],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindEquals(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('id')
                ->equals(1)
        );

        $expected = [
            $objects['parents'][0],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindContains(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('name')
                ->contains('Parent')
        );

        $expected = [
            $objects['parents'][0],
            $objects['parents'][1],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindStartsWith(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('name')
                ->startsWith('First')
        );

        $expected = [
            $objects['parents'][0],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindEndsWith(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('name')
                ->endsWith('Parent')
        );

        $expected = [
            $objects['parents'][1],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindGreaterThan(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('id')
                ->isGreaterThan(1)
        );

        $expected = [
            $objects['parents'][1],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindGreaterThanOrEquals(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('id')
                ->isGreaterThanOrEquals(1)
        );

        $expected = [
            $objects['parents'][0],
            $objects['parents'][1],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindLowerThan(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('id')
                ->isLowerThan(2)
        );

        $expected = [
            $objects['parents'][0],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindLowerThanOrEquals(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestParentObject::class)
                ->where('updated_at')
                ->isLowerThanOrEquals(new \DateTime())
        );

        $expected = [
            $objects['parents'][0],
            $objects['parents'][1],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindOneOf(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestObject::class)
                ->where('parent')
                ->equalsOneOf($objects['parents'])
        );

        $expected = [
            $objects['children'][0],
            $objects['children'][1],
            $objects['children'][2],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testSortAscending(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestObject::class)
                ->sortBy('id')
                ->ascending()
        );

        $expected = [
            $objects['children'][0],
            $objects['children'][1],
            $objects['children'][2],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testSortDescending(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestObject::class)
                ->sortBy('id')
                ->descending()
        );

        $expected = [
            $objects['children'][2],
            $objects['children'][1],
            $objects['children'][0],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testLimt(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestObject::class)
                ->limit(2, 1)
        );

        $expected = [
            $objects['children'][1],
            $objects['children'][2],
        ];

        $this->assertEquals($expected, $actual);
    }

    /**
     *
     * @param array $objects
     *
     * @depends testUpdateObjects
     */
    public function testFindWithParent(array $objects)
    {
        $actual = iterator_to_array(
            $this->getStorage()
                ->find(TestObject::class)
                ->with('parent')
                ->where('id')
                ->equals(1)
        );

        $objects['children'][0]->parent = $objects['parents'][0];
        $objects['children'][2]->parent = $objects['parents'][0];

        $expected = [
            $objects['children'][0],
            $objects['children'][2],
        ];

        $this->assertEquals($expected, $actual);

        unset($objects['children'][0]->parent);
        unset($objects['children'][2]->parent);
    }
}
