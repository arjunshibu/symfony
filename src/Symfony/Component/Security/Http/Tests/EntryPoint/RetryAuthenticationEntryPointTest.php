<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Http\Tests\EntryPoint;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\EntryPoint\RetryAuthenticationEntryPoint;

/**
 * @group legacy
 */
class RetryAuthenticationEntryPointTest extends TestCase
{
    /**
     * @dataProvider dataForStart
     */
    public function testStart($httpPort, $httpsPort, $request, $expectedUrl)
    {
        $entryPoint = new RetryAuthenticationEntryPoint($httpPort, $httpsPort);
        $response = $entryPoint->start($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($expectedUrl, $response->headers->get('Location'));
    }

    public function dataForStart()
    {
        if (!class_exists(Request::class)) {
            return [[]];
        }

        return [
            [
                80,
                443,
                Request::create('http://localhost/foo/bar?baz=bat'),
                'https://localhost/foo/bar?baz=bat',
            ],
            [
                80,
                443,
                Request::create('https://localhost/foo/bar?baz=bat'),
                'http://localhost/foo/bar?baz=bat',
            ],
            [
                80,
                123,
                Request::create('http://localhost/foo/bar?baz=bat'),
                'https://localhost:123/foo/bar?baz=bat',
            ],
            [
                8080,
                443,
                Request::create('https://localhost/foo/bar?baz=bat'),
                'http://localhost:8080/foo/bar?baz=bat',
            ],
        ];
    }
}
