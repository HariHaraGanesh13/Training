<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model\Resolver\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\EmployeeFactory;
use Magento\Framework\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Create implements ResolverInterface
{
    private EmployeeFactory $employeeFactory;
    private EmployeeRepositoryInterface $employeeRepository;

    public function __construct(
        EmployeeFactory $employeeFactory,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        $this->employeeFactory = $employeeFactory;
        $this->employeeRepository = $employeeRepository;
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        $input = $args['input'] ?? [];

        if (empty(trim((string)($input['name'] ?? '')))) {
            throw new GraphQlInputException(__('Name is required.'));
        }

        if (empty(trim((string)($input['designation'] ?? '')))) {
            throw new GraphQlInputException(__('Designation is required.'));
        }

        if (empty($input['joining_date'])) {
            throw new GraphQlInputException(__('Joining date is required.'));
        }

        $employee = $this->employeeFactory->create();

        $employee->setData('customer_id', (int)$context->getUserId());
        $employee->setData('name', trim((string)$input['name']));
        $employee->setData('joining_date', $input['joining_date']);
        $employee->setData('designation', trim((string)$input['designation']));
        $employee->setData('address', $input['address'] ?? '');
        $employee->setData('status', (int)($input['status'] ?? 1));
        $employee->setData(
            'hobbies',
            isset($input['hobbies']) && is_array($input['hobbies'])
                ? implode(',', $input['hobbies'])
                : ''
        );

        $this->employeeRepository->save($employee);

        return [
            'success' => true,
            'message' => __('Employee created successfully.'),
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
