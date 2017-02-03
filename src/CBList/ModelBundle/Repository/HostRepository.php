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

namespace CBList\ModelBundle\Repository;

use Darsyn\IP\IP as InetAddress;

use CBList\ModelBundle\Entity\Host;

/**
 * HostRepository
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class HostRepository extends CBListRepository
{
    const SERVICE_NAME = 'app.host-repository';

    const INET_ADDRESS_FIELD = 'inetAddress';

    /**
     * Returns the Host instance with matching inet address, if any.
     *
     * @param InetAddress $inetAddress the address to search for in database
     *
     * @return Host the matching Host instance or null
     */
    public function findOneByInetAddress(InetAddress $inetAddress)
    {
        return $this->findOneBy(array(self::INET_ADDRESS_FIELD => $inetAddress));
    }

    /**
     * Check that the given Host instance exists in this repository.
     *
     * @param Host $host the host instance to search for
     *
     * @return boolean true if the Host instance was found in database, false otherwise
     */
    public function exists(Host $host)
    {
        return
                $this->existsId($host->getId()) ||
                (
                        null !== $host->getInetAddress() &&
                        $this->existsInetAddress($host->getInetAddress())
                )
        ;
    }

    /**
     * Check that an Host instance with matching inet address exists in this repository.
     *
     * @param InetAddress $inetAddress the inet address to search for
     *
     * @return boolean true if a match was found, false otherwise
     */
    public function existsInetAddress(InetAddress $inetAddress)
    {
        return null !== $this->findOneByInetAddress($inetAddress);
    }
}
