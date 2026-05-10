<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Api;

interface EmployeeApiInterface
{
    /**
     * Get employees.
     *
     * @return array
     */
    public function getEmployees(): array;

    /**
     * Get employee by ID.
     *
     * @param int $id
     * @return array
     */
    public function getEmployee(int $id): array;

    /**
     * Create employee.
     *
     * @param string $name
     * @param string $joiningDate
     * @param string $designation
     * @param string $address
     * @param int $status
     * @param string[] $hobbies
     * @return array
     */
    public function createEmployee(
        string $name,
        string $joiningDate,
        string $designation,
        string $address = '',
        int $status = 1,
        array $hobbies = []
    ): array;

    /**
     * Update employee.
     *
     * @param int $id
     * @param string $name
     * @param string $joiningDate
     * @param string $designation
     * @param string $address
     * @param int $status
     * @param string[] $hobbies
     * @return array
     */
    public function updateEmployee(
        int $id,
        string $name,
        string $joiningDate,
        string $designation,
        string $address = '',
        int $status = 1,
        array $hobbies = []
    ): array;

    /**
     * Delete employee.
     *
     * @param int $id
     * @return array
     */
    public function deleteEmployee(int $id): array;
}
