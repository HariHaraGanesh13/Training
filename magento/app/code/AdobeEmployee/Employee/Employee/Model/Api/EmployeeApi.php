<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model\Api;

use Adobe\Employee\Api\EmployeeApiInterface;
use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\EmployeeFactory;
use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\LocalizedException;

class EmployeeApi implements EmployeeApiInterface
{
    private EmployeeRepositoryInterface $employeeRepository;
    private EmployeeFactory $employeeFactory;
    private CollectionFactory $collectionFactory;
    private UserContextInterface $userContext;

    public function __construct(
        EmployeeRepositoryInterface $employeeRepository,
        EmployeeFactory $employeeFactory,
        CollectionFactory $collectionFactory,
        UserContextInterface $userContext
    ) {
        $this->employeeRepository = $employeeRepository;
        $this->employeeFactory = $employeeFactory;
        $this->collectionFactory = $collectionFactory;
        $this->userContext = $userContext;
    }

    public function getEmployees(): array
    {
        $customerId = $this->getCustomerId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);

        $items = [];

        foreach ($collection as $employee) {
            $items[] = $this->formatEmployee($employee);
        }

        return [
            'success' => true,
            'items' => $items
        ];
    }

    public function getEmployee(int $id): array
    {
        $employee = $this->employeeRepository->getById($id);
        $this->validateAccess($employee);

        return [
            'success' => true,
            'item' => $this->formatEmployee($employee)
        ];
    }

    public function createEmployee(
        string $name,
        string $joiningDate,
        string $designation,
        string $address = '',
        int $status = 1,
        array $hobbies = []
    ): array {
            $customerId = $this->getCustomerId();

            $data = [
                'name' => $name,
                'joining_date' => $joiningDate,
                'designation' => $designation,
                'address' => $address,
                'status' => $status,
                'hobbies' => $hobbies
            ];

            $this->validateInput($data);

            $employee = $this->employeeFactory->create();
            $employee->setData('customer_id', $customerId);
            $this->setEmployeeData($employee, $data);

            $this->employeeRepository->save($employee);

            return [
                'success' => true,
                'message' => 'Employee created successfully.',
                'employee' => $this->formatEmployee($employee)
            ];
    }
    public function updateEmployee(
        int $id,
        string $name,
        string $joiningDate,
        string $designation,
        string $address = '',
        int $status = 1,
        array $hobbies = []
    ): array {
        $employee = $this->employeeRepository->getById($id);
        $this->validateAccess($employee);

        $data = [
            'name' => $name,
            'joining_date' => $joiningDate,
            'designation' => $designation,
            'address' => $address,
            'status' => $status,
            'hobbies' => $hobbies
        ];

        $this->validateInput($data);
        $this->setEmployeeData($employee, $data);
        $this->employeeRepository->save($employee);

        return [
            'success' => true,
            'message' =>'Employee updated successfully.',
            'employee' => $this->formatEmployee($employee)
        ];
    }

    public function deleteEmployee(int $id): array
    {
        $employee = $this->employeeRepository->getById($id);
        $this->validateAccess($employee);

        $this->employeeRepository->delete($employee);

        return [
            'success' => true,
            'message' => 'Employee deleted successfully.'
        ];
    }

    private function getCustomerId(): int
    {
        $customerId = (int)$this->userContext->getUserId();

        if (!$customerId) {
            throw new LocalizedException(__('Customer authentication is required.'));
        }

        return $customerId;
    }

    private function validateAccess($employee): void
    {
        if ((int)$employee->getData('customer_id') !== $this->getCustomerId()) {
            throw new LocalizedException(__('You are not allowed to access this employee.'));
        }
    }

    private function validateInput(array $data): void
    {
        if (isset($data['name']) && trim((string)$data['name']) === '') {
            throw new LocalizedException(__('Name is required.'));
        }

        if (isset($data['designation']) && trim((string)$data['designation']) === '') {
            throw new LocalizedException(__('Designation is required.'));
        }

        if (isset($data['status']) && !in_array((int)$data['status'], [0, 1], true)) {
            throw new LocalizedException(__('Status must be 0 or 1.'));
        }
    }

    private function setEmployeeData($employee, array $data): void
    {
        if (isset($data['name'])) {
            $employee->setData('name', trim((string)$data['name']));
        }

        if (isset($data['joining_date'])) {
            $employee->setData('joining_date', $data['joining_date']);
        }

        if (isset($data['designation'])) {
            $employee->setData('designation', trim((string)$data['designation']));
        }

        if (isset($data['address'])) {
            $employee->setData('address', $data['address']);
        }

        if (isset($data['status'])) {
            $employee->setData('status', (int)$data['status']);
        }

        if (isset($data['hobbies'])) {
            $hobbies = $data['hobbies'];
            $employee->setData('hobbies', is_array($hobbies) ? implode(',', $hobbies) : $hobbies);
        }
    }

    private function formatEmployee($employee): array
    {
        $hobbies = (string)$employee->getData('hobbies');

        return [
            'id' => (int)$employee->getId(),
            'customer_id' => (int)$employee->getData('customer_id'),
            'name' => (string)$employee->getData('name'),
            'joining_date' => (string)$employee->getData('joining_date'),
            'designation' => (string)$employee->getData('designation'),
            'address' => (string)$employee->getData('address'),
            'status' => (int)$employee->getData('status'),
            'hobbies' => $hobbies ? explode(',', $hobbies) : [],
            'hobbies_text' => $hobbies
        ];
    }
}
