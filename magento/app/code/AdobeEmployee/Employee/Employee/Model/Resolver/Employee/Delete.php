<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model\Resolver\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Framework\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Delete implements ResolverInterface
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
        if (empty($args['id'])) {
            throw new GraphQlInputException(__('Employee ID is required.'));
        }

        $employee = $this->employeeRepository->getById((int)$args['id']);
        $this->employeeRepository->delete($employee);

        return [
            'success' => true,
            'message' => __('Employee deleted successfully.')
        ];
    }
}
