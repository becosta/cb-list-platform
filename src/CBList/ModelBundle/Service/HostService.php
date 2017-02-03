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

namespace CBList\ModelBundle\Service;

use Doctrine\ORM\EntityManager;
use Darsyn\IP\IP as InetAddress;

use CBList\Common\Service\CBListService;
use CBList\ModelBundle\Entity\Host;
use CBList\ModelBundle\Repository\HostRepository;

/**
 * Description of HostService
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class HostService extends CBListService
{

    const SERVICE_NAME = 'app.host-service';

    const INET_ADDRESS_FIELD = HostRepository::INET_ADDRESS_FIELD;

    private $entityManager;

    private $repository;

    public function __construct(EntityManager $entityManager, HostRepository $repository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * Creates a new Host instance constructed from the given arguments.
     *
     * @param InetAddress $inetAddress the network address for the new Host
     *
     * @return Host the new Host instance
     */
    public function create(InetAddress $inetAddress)
    {
        return new Host(array(self::INET_ADDRESS_FIELD => $inetAddress));
    }

    /**
     * Adds an Host instance to the database.
     *
     * The given Host instance must not exist
     * in database prior to calling this method
     * or an exception will be thrown.
     *
     * @param Host $host the Host instance to add to the database
     *
     * @throws Exception if $host is already present in database
     */
    public function add(Host $host)
    {
        // TODO: validate $host
        if (!$this->repository->exists($host)) {
            $this->entityManager->persist($host);
            $this->entityManager->flush($host);
        }
    }

    /**
     * Returns the whole collection of Host instances.
     *
     * @return array the Host instances
     */
    public function getHosts()
    {
        return $this->repository->findAll();
    }

    /**
     * Returns the Host instance with id matching $id.
     *
     * Can return null.
     *
     * @param integer $id the id of the Host instance to return
     *
     * @return Host the Host instance or null
     *
     * @throws \InvalidArgumentException if $id isn't a valid integer
     */
    public function getHost($id)
    {
        $this->assertValidInteger($id);
        return $this->repository->find($id);
    }

    /**
     * Returns the Host instance with inetAddress matching $inetAddress.
     *
     * Can return null.
     *
     * @param InetAddress $inetAddress the network address of the Host instance to return
     *
     * @return Host the Host instance or null
     */
    public function getHostByInetAddress(InetAddress $inetAddress)
    {
        return $this->repository->findOneByInetAddress($inetAddress);
    }
}
