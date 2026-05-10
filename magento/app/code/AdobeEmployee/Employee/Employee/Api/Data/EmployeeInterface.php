<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Api\Data;

interface EmployeeInterface
{
    public const ID = 'id';
    public const NAME = 'name';
    public const JOINING_DATE = 'joining_date';
    public const DESIGNATION = 'designation';
    public const ADDRESS = 'address';
    public const STATUS = 'status';
    public const HOBBIES = 'hobbies';

    public function getId();
    public function getName();
    public function setName($name);

    public function getJoiningDate();
    public function setJoiningDate($joiningDate);

    public function getDesignation();
    public function setDesignation($designation);

    public function getAddress();
    public function setAddress($address);

    public function getStatus();
    public function setStatus($status);

    public function getHobbies();
    public function setHobbies($hobbies);
}
