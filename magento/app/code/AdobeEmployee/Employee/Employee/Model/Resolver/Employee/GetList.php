<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model\Resolver\Employee;

use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Framework\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * GraphQL resolver to fetch employee list.
 */
class GetList implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Resolve employee list.
     *
     * @param Field $field
     * @param mixed $context
     * @param ResolveInfo $info
     * @param mixed[]|null $value
     * @param mixed[]|null $args
     * @return mixed[]
     * @throws GraphQlAuthorizationException
     */
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

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);

        $items = [];

        foreach ($collection as $employee) {
            $items[] = [
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
            ];
        }

        return $items;
    }
}
