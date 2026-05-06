<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model;

use Magento\Framework\Model\AbstractModel;
use Adobe\Employee\Api\Data\EmployeeInterface;

class Employee extends AbstractModel implements EmployeeInterface
{
    protected function _construct()
    {
        $this->_init(\Adobe\Employee\Model\ResourceModel\Employee::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    public function getJoiningDate()
    {
        return $this->getData(self::JOINING_DATE);
    }

    public function setJoiningDate($joiningDate)
    {
        return $this->setData(self::JOINING_DATE, $joiningDate);
    }

    public function getDesignation()
    {
        return $this->getData(self::DESIGNATION);
    }

    public function setDesignation($designation)
    {
        return $this->setData(self::DESIGNATION, $designation);
    }

    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getHobbies()
    {
        return $this->getData(self::HOBBIES);
    }

    public function setHobbies($hobbies)
    {
        return $this->setData(self::HOBBIES, $hobbies);
    }
}
