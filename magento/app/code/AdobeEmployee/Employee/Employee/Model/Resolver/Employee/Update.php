<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model\Resolver\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Framework\Exception\GraphQlAuthorizationException;
use Magento\Framework\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Update implements ResolverInterface
{
    private EmployeeRepositoryInterface $employeeRepository;

    public function __construct(
        EmployeeRepositoryInterface $employeeRepository
    ) {
        $this->employeeRepository = $employeeRepository;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $customerId = (int)$context->getUserId();

        if (!$customerId) {
            throw new GraphQlAuthorizationException(__('Customer authentication is required.'));
        }

        if (empty($args['input']['id'])) {
            throw new GraphQlInputException(__('Employee ID is required.'));
        }

        $input = $args['input'];
        $employee = $this->employeeRepository->getById((int)$input['id']);

        if ((int)$employee->getData('customer_id') !== $customerId) {
            throw new GraphQlAuthorizationException(__('You are not allowed to update this employee.'));
        }

        if (isset($input['name']) && trim((string)$input['name']) === '') {
            throw new GraphQlInputException(__('Name is required.'));
        }

        if (isset($input['designation']) && trim((string)$input['designation']) === '') {
            throw new GraphQlInputException(__('Designation is required.'));
        }

        if (isset($input['status']) && !in_array((int)$input['status'], [0, 1], true)) {
            throw new GraphQlInputException(__('Status must be 0 or 1.'));
        }

        if (isset($input['name'])) {
            $employee->setData('name', trim((string)$input['name']));
        }

        if (isset($input['joining_date'])) {
            $employee->setData('joining_date', $input['joining_date']);
        }

        if (isset($input['designation'])) {
            $employee->setData('designation', trim((string)$input['designation']));
        }

        if (isset($input['address'])) {
            $employee->setData('address', $input['address']);
        }

        if (isset($input['status'])) {
            $employee->setData('status', (int)$input['status']);
        }

        if (isset($input['hobbies'])) {
            $employee->setData('hobbies', implode(',', $input['hobbies']));
        }

        $this->employeeRepository->save($employee);

        return [
            'success' => true,
            'message' => __('Employee updated successfully.'),
            'employee' => [
                'id' => (int)$employee->getId(),
                'customer_id' => (int)$employee->getData('customer_id'),
                'name' => (string)$employee->getData('name'),
                'joining_date' => (string)$employee->getData('joining_date'),
                'designation' => (string)$employee->getData('designation'),
                'address' => (string)$employee->getData('address'),
                'status' => (int)$employee->getData('status'),
                'hobbies' => $employee->getData('hobbies')
                    ? explode(',', (string)$employee->getData('hobbies'))
                    : []
            ]
        ];
    }
}
