<?php
namespace Dryspell\Storage;

/**
 * StorageSetupInterface
 *
 * @author Björn Tantau <bjoern@bjoern-tantau.de>
 */
interface StorageSetupInterface
{

    public function setup(string $entityName): self;
}
