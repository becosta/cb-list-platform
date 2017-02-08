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

namespace CBList\ModelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Darsyn\IP\IP as InetAddress;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;

use CBList\ModelBundle\Entity\Entity;

/**
 * Represents a network host.
 *
 * @ORM\Table(name="host")
 * @ORM\Entity(repositoryClass="CBList\ModelBundle\Repository\HostRepository")
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class Host extends \CBList\ModelBundle\Entity\Entity implements CBListEntity
{
    /**
     * A collection of Report instances linked to this Host instance.
     *
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Report", mappedBy="host")
     *
     * @Groups({"host-details"})
     * @MaxDepth(3)
     */
    private $reports;

    /**
     * This Network Host IP Address.
     *
     * @var InetAddress
     *
     * @ORM\Column(name="inet_address", type="ip", unique=true)
     *
     * @Groups({"host-summary", "list", "summary"})
     * @Accessor(getter="getInetAddressAsString", setter="setInetAddress")
     */
    private $inetAddress;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $data = null)
    {
        $this->reports = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function hydrate(array $data)
    {
        parent::hydrate($data);

        if (array_key_exists('reports', $data)) {
            $this->setReports($data['reports']);
        }
        if (array_key_exists('inetAddress', $data)) {
            $this->setInetAddress($data['inetAddress']);
        }
    }

    /**
     * Link a Report instance to this Host.
     *
     * If the given report object is already linked
     * to this host the operation is skipped silently.
     *
     * @param Report $report the report to add to this host
     */
    public function addReport(Report $report)
    {
        if (null === $this->reports) {
            $this->setReports(new ArrayCollection());
        }
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
        }
    }

    /**
     * Returns the Report instances related to this Category instance.
     *
     * @return Collection the collection of Report instances
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * Sets the Report collection related to this Category instance.
     *
     * @param Collection $reports the collection of Report instances
     *
     * @return Category this Category instance
     */
    public function setReports(Collection $reports)
    {
        $this->reports = $reports;
        return $this;
    }

    /**
     * Returns this instance's network address.
     *
     * @return InetAddress this instance's network address
     */
    public function getInetAddress()
    {
        return $this->inetAddress;
    }

    /**
     * Returns this instance's network address.
     *
     * @return string this instance's network address
     */
    public function getInetAddressAsString()
    {
        return (string)$this->inetAddress->getShortAddress();
    }

    /**
     * Sets this instance's network address
     *
     * @param InetAddress $inetAddress the new network address for this Host instance
     *
     * @return Host this Host instance
     */
    public function setInetAddress(InetAddress $inetAddress)
    {
        $this->inetAddress = $inetAddress;
        return $this;
    }
}

