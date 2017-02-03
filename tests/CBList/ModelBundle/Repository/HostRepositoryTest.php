<?php

/*
 * The MIT License
 *
 * Copyright (c) 2017 Benjamin Costa <benjamin.costa.75@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace tests\CBList\ModelBundle\Repository;

use Doctrine\ORM\EntityManager;
use Darsyn\IP\IP as InetAddress;

use CBList\Common\CBListKernelTestCase;
use CBList\ModelBundle\DataFixtures\ORM\Tests\LoadHostData;
use CBList\ModelBundle\Entity\Host;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class HostRepositoryTest extends CBListKernelTestCase
{
    const HOST_REPOSITORY = 'CBListModelBundle:Host';

    const VALID_ADDRESS     = '192.168.1.100';

    const INVALID_ADDRESS   = '192.168.10.10';

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addFixture(new LoadHostData());
        $this->executeFixtures();

        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager()
        ;
    }

    public function testFindOneByInetAddress()
    {
        $validAddress = new InetAddress(self::VALID_ADDRESS);
        $invalidAddress = new InetAddress(self::INVALID_ADDRESS);

        $repository = $this->em->getRepository(self::HOST_REPOSITORY);

        $host = $repository->findOneByInetAddress($validAddress);
        $this->assertNotNull($host);
        $this->assertEquals($validAddress, $host->getInetAddress());

        $host = $repository->findOneByInetAddress($invalidAddress);
        $this->assertNull($host);
    }

    public function testExists()
    {
        $repository = $this->em->getRepository(self::HOST_REPOSITORY);

        $validAddress = new InetAddress(self::VALID_ADDRESS);

        $validHost = new Host(array('inetAddress' => $validAddress));
        $invalidHost = new Host(
                array('inetAddress' => new InetAddress(self::INVALID_ADDRESS))
        );

        $host = $repository->findOneByInetAddress($validAddress);
        if (null === $host) {
            $this->outOfScopeFailure(
                    "Either method HostRepository::findOneByInetAddress is not working" .
                    "properly or the testing database isn't initialized correctly"
            );
        }
        $id = $host->getId();

        $this->assertTrue($repository->exists($validHost));
        $this->assertFalse($repository->exists($invalidHost));
        $this->assertTrue($repository->exists(new Host(array('id' => $id))));
        $this->assertFalse($repository->exists(new Host(array('id' => -$id))));
        $this->assertFalse($repository->exists(new Host(array('id' => -1))));
        $this->assertFalse($repository->exists(new Host()));
    }

    public function testExistsInetAddress()
    {
        $repository = $this->em->getRepository(self::HOST_REPOSITORY);

        $validAddress = new InetAddress(self::VALID_ADDRESS);
        $invalidAddress = new InetAddress(self::INVALID_ADDRESS);

        $this->assertTrue($repository->existsInetAddress($validAddress));
        $this->assertFalse($repository->existsInetAddress($invalidAddress));
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }
}
