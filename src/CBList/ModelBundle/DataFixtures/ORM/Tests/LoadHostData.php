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

namespace CBList\ModelBundle\DataFixtures\ORM\Tests;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Darsyn\IP\IP as InetAddress;

use CBList\ModelBundle\Entity\Host;

/**
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class LoadHostData extends AbstractFixture implements OrderedFixtureInterface
{
    const HOST_1_INET_ADDRESS = '127.0.0.1';

    const HOST_1_NAME = 'host-instance-1';

    const HOST_2_INET_ADDRESS = '192.168.0.1';

    const HOST_2_NAME = 'host-instance-2';

    const HOST_3_INET_ADDRESS = '192.168.1.100';

    const HOST_3_NAME = 'host-instance-3';

    const HOST_4_INET_ADDRESS = '10.1.1.1';

    const HOST_4_NAME = 'host-instance-4';

    const HOST_5_INET_ADDRESS = '172.16.0.1';

    const HOST_5_NAME = 'host-instance-5';

    public function load(ObjectManager $manager)
    {
        $this->addHostInstance($manager, self::HOST_1_NAME, self::HOST_1_INET_ADDRESS);
        $this->addHostInstance($manager, self::HOST_2_NAME, self::HOST_2_INET_ADDRESS);
        $this->addHostInstance($manager, self::HOST_3_NAME, self::HOST_3_INET_ADDRESS);
        $this->addHostInstance($manager, self::HOST_4_NAME, self::HOST_4_INET_ADDRESS);
        $this->addHostInstance($manager, self::HOST_5_NAME, self::HOST_5_INET_ADDRESS);
        $manager->flush();
    }

    public function getOrder()
    {
        return 50;
    }

    private function addHostInstance(ObjectManager $manager, $name, $inetAddress)
    {
        $host = new Host(array('inetAddress' => new InetAddress($inetAddress)));
        $manager->persist($host);
        $this->addReference($name, $host);
    }
}
