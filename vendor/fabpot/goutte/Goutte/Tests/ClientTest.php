<?php

/*
 * This file is part of the Goutte package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Goutte\Tests;

use Goutte\Clientg;
use PHPUnit\Framework\TestCase;
use Symfony\Component\BrowserKit\HttpBrowser;

class ClientTest extends TestCase
{
    public function testNew()
    {
        $clientg = new Clientg();
        $this->assertInstanceOf(HttpBrowser::class, $clientg);
    }
}
