<?php

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
        $collection = $this->collectionFactory->create();

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
        $this->validateInput($name, $joiningDate, $designation, $status);

        $employee = $this->employeeFactory->create();

        $employee->setData('name', trim($name));
        $employee->setData('joining_date', $joiningDate);
        $employee->setData('designation', trim($designation));
        $employee->setData('address', $address);
        $employee->setData('status', $status);
        $employee->setData('hobbies', implode(',', $hobbies));

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
        $this->validateInput($name, $joiningDate, $designation, $status);

        $employee = $this->employeeRepository->getById($id);

        $employee->setData('name', trim($name));
        $employee->setData('joining_date', $joiningDate);
        $employee->setData('designation', trim($designation));
        $employee->setData('address', $address);
        $employee->setData('status', $status);
        $employee->setData('hobbies', implode(',', $hobbies));

        $this->employeeRepository->save($employee);

        return [
            'success' => true,
            'message' => 'Employee updated successfully.',
            'employee' => $this->formatEmployee($employee)
        ];
    }

    public function deleteEmployee(int $id): array
    {
        $employee = $this->employeeRepository->getById($id);
        $this->employeeRepository->delete($employee);

        return [
            'success' => true,
            'message' => 'Employee deleted successfully.'
        ];
    }

    private function validateInput(
        string $name,
        string $joiningDate,
        string $designation,
        int $status
    ): void {
        if (trim($name) === '') {
            throw new LocalizedException(__('Name is required.'));
        }

        if ($joiningDate === '') {
            throw new LocalizedException(__('Joining date is required.'));
        }

        if (trim($designation) === '') {
            throw new LocalizedException(__('Designation is required.'));
        }

        if (!in_array($status, [0, 1], true)) {
            throw new LocalizedException(__('Status must be 0 or 1.'));
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