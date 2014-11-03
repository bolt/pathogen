<?php

/*
 * This file is part of the Pathogen package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Pathogen\FileSystem\Factory;

use Eloquent\Pathogen\Factory\PathFactoryInterface;
use Eloquent\Pathogen\FileSystem\AbsoluteFileSystemPathInterface;
use Eloquent\Pathogen\Unix\Factory\UnixPathFactory;
use Eloquent\Pathogen\Windows\Factory\WindowsPathFactory;

/**
 * Abstract base class for classes implementing FileSystemPathFactoryInterface.
 */
abstract class AbstractFileSystemPathFactory implements
    FileSystemPathFactoryInterface
{
    /**
     * Construct a new file system path factory.
     *
     * @param PathFactoryInterface|null $unixFactory    The path factory to use for Unix paths.
     * @param PathFactoryInterface|null $windowsFactory The path factory to use for Windows paths.
     * @param Isolator|null             $isolator       The isolator to use.
     */
    public function __construct(
        PathFactoryInterface $unixFactory = null,
        PathFactoryInterface $windowsFactory = null,
        $isolator = null
    ) {
        if (null === $unixFactory) {
            $unixFactory = UnixPathFactory::instance();
        }
        if (null === $windowsFactory) {
            $windowsFactory = WindowsPathFactory::instance();
        }

        $this->unixFactory = $unixFactory;
        $this->windowsFactory = $windowsFactory;
        
        // Bolt doesn't use isolator, so warn users it is not available
        if ($isolator != null) {
            throw new Exception("Bolt pathogen does not use isolator", 1);
        }
    }

    /**
     * Get the path factory used for Unix paths.
     *
     * @return PathFactoryInterface The path factory used for Unix paths.
     */
    public function unixFactory()
    {
        return $this->unixFactory;
    }

    /**
     * Get the path factory used for Windows paths.
     *
     * @return PathFactoryInterface The path factory used for Windows paths.
     */
    public function windowsFactory()
    {
        return $this->windowsFactory;
    }

    /**
     * Create a path representing the current working directory.
     *
     * @return AbsoluteFileSystemPathInterface A new path instance representing the current working directory path.
     */
    public function createWorkingDirectoryPath()
    {
        return $this->factoryByPlatform()
            ->create(getcwd());
    }

    /**
     * Create a path representing the system temporary directory.
     *
     * @return AbsoluteFileSystemPathInterface A new path instance representing the system default temporary directory path.
     */
    public function createTemporaryDirectoryPath()
    {
        return $this->factoryByPlatform()
            ->create(sys_get_temp_dir());
    }

    /**
     * Create a path representing a suitable for use as the location for a new
     * temporary file or directory.
     *
     * This path is not guaranteed to be unused, but collisions are fairly
     * unlikely.
     *
     * @param string|null $prefix A string to use as a prefix for the path name.
     *
     * @return AbsoluteFileSystemPathInterface A new path instance representing the new temporary path.
     */
    public function createTemporaryPath($prefix = null)
    {
        if (null === $prefix) {
            $prefix = '';
        }

        return $this->createTemporaryDirectoryPath()
            ->joinAtoms(uniqid($prefix, true));
    }

    /**
     * Return the most appropriate path factory depending on the current
     * platform.
     *
     * @return PathFactoryInterface The most appropriate path factory.
     */
    protected function factoryByPlatform()
    {
        if (defined('PHP_WINDOWS_VERSION_BUILD')) {
            return $this->windowsFactory();
        }

        return $this->unixFactory();
    }

    private $unixFactory;
    private $windowsFactory;
}
