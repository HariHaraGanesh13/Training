<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Ajax;

use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Employee list JSON for current customer.
 */
class ListJson implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $collectionFactory
     * @param Session $customerSession
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        CollectionFactory $collectionFactory,
        Session $customerSession
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->collectionFactory = $collectionFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Return employees for logged-in customer.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $result->setData([
                'success' => false,
                'message' => __('Please login first.')
            ]);
        }

        $customerId = (int)$this->customerSession->getCustomerId();

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);

        $items = [];
        foreach ($collection as $employee) {
            $items[] = [
                'id' => $employee->getId(),
                'name' => $employee->getData('name'),
                'joining_date' => $employee->getData('joining_date'),
                'designation' => $employee->getData('designation'),
                'address' => $employee->getData('address'),
                'status' => (int)$employee->getData('status'),
                'hobbies' => $employee->getData('hobbies')
                    ? explode(',', $employee->getData('hobbies'))
                    : [],
                'hobbies_text' => $employee->getData('hobbies') ?: ''
            ];
        }

        return $result->setData([
            'success' => true,
            'items' => $items
        ]);
    }
}
