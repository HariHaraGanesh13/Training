<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Api\Data\EmployeeInterface;
use Adobe\Employee\Model\ResourceModel\Employee as EmployeeResource;
use Adobe\Employee\Model\EmployeeFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    protected $resource;
    protected $employeeFactory;

    public function __construct(
        EmployeeResource $resource,
        EmployeeFactory $employeeFactory
    ) {
        $this->resource = $resource;
        $this->employeeFactory = $employeeFactory;
    }

    public function save(EmployeeInterface $employee)
    {
        $this->resource->save($employee);
        return $employee;
    }

    public function getById($id)
    {
        $employee = $this->employeeFactory->create();
        $this->resource->load($employee, $id);
        if (!$employee->getId()) {
            throw new NoSuchEntityException(__('Employee with id "%1" does not exist.', $id));
        }
        return $employee;
    }

    public function delete(EmployeeInterface $employee)
    {
        $this->resource->delete($employee);
        return true;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
