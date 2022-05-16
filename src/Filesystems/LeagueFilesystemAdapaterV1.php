<?php

declare(strict_types=1);

namespace Fezfez\BackupManager\Filesystems;

use League\Flysystem\Adapter\AbstractAdapter as LeagueV1;
use League\Flysystem\Filesystem;

use function class_exists;
use function sprintf;

if (! class_exists(LeagueV1::class)) {
    return;
}

class LeagueFilesystemAdapaterV1 implements FilesystemAdapter
{
    private Filesystem $fileSysteme;

    public function __construct(LeagueV1 $adapter)
    {
        $this->fileSysteme = new Filesystem($adapter);
    }

    public function readStream(string $path): BackupManagerRessource
    {
        return new BackupManagerRessource($this->fileSysteme->readStream($path));
    }

    public function writeStream(string $path, BackupManagerRessource $resource): void
    {
        if ($this->fileSysteme->writeStream($path, $resource->getResource()) !== true) {
            throw new CantWriteFile(sprintf('cant delete file %s', $path));
        }
    }

    public function delete(string $path): void
    {
        if ($this->fileSysteme->delete($path) !== true) {
            throw new CantDeleteFile(sprintf('cant delete file %s', $path));
        }
    }
}
