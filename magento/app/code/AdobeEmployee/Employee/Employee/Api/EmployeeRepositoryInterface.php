<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Api;

use Adobe\Employee\Api\Data\EmployeeInterface;

interface EmployeeRepositoryInterface
{
    public function save(EmployeeInterface $employee);

    public function getById($id);

    public function delete(EmployeeInterface $employee);

    public function deleteById($id);
}
