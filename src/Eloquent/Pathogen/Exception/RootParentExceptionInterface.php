<?php

/*
 * This file is part of the Pathogen package.
 *
 * Copyright © 2013 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eloquent\Pathogen\Exception;

/**
 * The root path has no parent.
 *
 * This type of exception is thrown when PathInterface::parent() is called on
 * the root path (i.e. '/').
 */
interface RootParentExceptionInterface
{
}