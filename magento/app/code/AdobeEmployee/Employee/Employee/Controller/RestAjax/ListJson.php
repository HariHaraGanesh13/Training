<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\RestAjax;

use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;

class ListJson extends AbstractAccount
{
    private CollectionFactory $collectionFactory;
    private JsonFactory $resultJsonFactory;
    private Session $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CollectionFactory $collectionFactory,
        JsonFactory $resultJsonFactory,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $customerId = (int)$this->customerSession->getCustomerId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);

        $items = [];

        foreach ($collection as $employee) {
            $hobbies = (string)$employee->getData('hobbies');

            $items[] = [
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

        return $result->setData([
            'success' => true,
            'items' => $items
        ]);
    }
}
