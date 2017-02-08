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
use Darsyn\IP\IP as InetAddress;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;

use CBList\ModelBundle\Entity\Category;
use CBList\ModelBundle\Entity\Entity;

/**
 * Represent a suspicious behaviour report.
 *
 * A report instance links an Host instance to a report Category instance
 * and keeps some metadata on the report like the date the report
 * was submitted or the inet address the report was submitted from.
 *
 * @ORM\Table(name="report")
 * @ORM\Entity(repositoryClass="CBList\ModelBundle\Repository\ReportRepository")
 *
 * @author Benjamin Costa <benjamin.costa.75@gmail.com>
 * @copyright (c) 2017, Benjamin Costa
 * @license https://opensource.org/licenses/MIT MIT
 */
class Report extends \CBList\ModelBundle\Entity\Entity implements CBListEntity
{
    /**
     * The date on which this report was submitted.
     *
     * @var \DateTime
     *
     * @ORM\Column(name="date_submitted", type="datetime")
     *
     * @Groups({"list", "report-summary", "summary"})
     */
    private $dateSubmitted;

    /**
     * The InetAddress of the client which sent this report.
     *
     * @var InetAddress
     *
     * @ORM\Column(name="sender_inet_address", type="ip")
     *
     * @Groups({"details", "report-details"})
     */
    private $senderInetAddress;

    /**
     * The category this report belongs to.
     *
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="reports", cascade={"persist"})
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *
     * @Groups({"report-details"})
     * @MaxDepth(2)
     */
    private $category;

    /**
     * The network host concerned by this report.
     *
     * @var Host
     *
     * @ORM\ManyToOne(targetEntity="Host", inversedBy="reports")
     * @ORM\JoinColumn(name="host_id", referencedColumnName="id")
     *
     * @Groups({"report-details"})
     * @MaxDepth(1)
     */
    private $host;

    /**
     * {@inheritDoc}
     */
    protected function hydrate(array $data)
    {
        parent::hydrate($data);

        if (array_key_exists('dateSubmitted', $data)) {
            $this->setDateSubmitted($data['dateSubmitted']);
        }
        if (array_key_exists('senderInetAddress', $data)) {
            $this->setSenderInetAddress($data['senderInetAddress']);
        }
        if (array_key_exists('category', $data)) {
            $this->setCategory($data['category']);
        }
        if (array_key_exists('host', $data)) {
            $this->setHost($data['host']);
        }
    }

    /**
     * Returns the date on which this report was submitted.
     *
     * @return \DateTime the actual date
     */
    public function getDateSubmitted()
    {
        return $this->dateSubmitted;
    }

    /**
     * Sets the date on which this report was submitted
     *
     * @param \DateTime $dateSubmitted the actual date
     *
     * @return Report this Report instance
     */
    public function setDateSubmitted(\DateTime $dateSubmitted)
    {
        $this->dateSubmitted = $dateSubmitted;
        return $this;
    }

    /**
     * Returns the InetAddress this report was submitted from.
     *
     * @return InetAddress the InetAddress this report was submitted from
     */
    public function getSenderInetAddress()
    {
        return $this->senderInetAddress;
    }

    /**
     * Sets the InetAddress this report was submitted from.
     *
     * @param InetAddress $senderInetAddress the actual InetAddress
     *
     * @return Report this Report instance
     */
    public function setSenderInetAddress(InetAddress $senderInetAddress)
    {
        $this->senderInetAddress = $senderInetAddress;
        return $this;
    }

    /**
     * Returns the Category instance this report belongs to.
     *
     * @return Category this report's Category instance
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Sets the Category this report belongs to.
     *
     * @param Category $category the actual Category instance
     *
     * @return Report this Report instance
     */
    public function setCategory(Category $category)
    {
        $category->addReport($this);
        $this->category = $category;
        return $this;
    }

    /**
     * Returns the network host concerned by this report.
     *
     * @return Host the network host concerned by this report
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Sets the network host concerned by this report.
     *
     * @param Host $host the actual network host
     *
     * @return Report this Report instance
     */
    public function setHost(Host $host)
    {
        $host->addReport($this);
        $this->host = $host;
        return $this;
    }
}
