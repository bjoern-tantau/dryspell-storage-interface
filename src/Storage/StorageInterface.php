<?php
namespace Dryspell\Storage;

use Dryspell\Models\ObjectInterface;

/**
 * StorageInterface
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
interface StorageInterface
{

    public function find(string $className): FindInterface;

    public function save(ObjectInterface $entity): self;

    public function delete(ObjectInterface $entity): self;
}
