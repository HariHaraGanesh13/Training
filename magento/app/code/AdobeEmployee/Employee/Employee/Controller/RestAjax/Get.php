<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\RestAjax;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;

class Get extends AbstractAccount
{
    private EmployeeRepositoryInterface $employeeRepository;
    private JsonFactory $resultJsonFactory;
    private Session $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        EmployeeRepositoryInterface $employeeRepository,
        JsonFactory $resultJsonFactory,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->employeeRepository = $employeeRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $id = (int)$this->getRequest()->getParam('id');
        $customerId = (int)$this->customerSession->getCustomerId();

        try {
            $employee = $this->employeeRepository->getById($id);

            if ((int)$employee->getData('customer_id') !== $customerId) {
                return $result->setData([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ]);
            }

            $hobbies = (string)$employee->getData('hobbies');

            return $result->setData([
                'success' => true,
                'item' => [
                    'id' => (int)$employee->getId(),
                    'customer_id' => (int)$employee->getData('customer_id'),
                    'name' => (string)$employee->getData('name'),
                    'joining_date' => (string)$employee->getData('joining_date'),
                    'designation' => (string)$employee->getData('designation'),
                    'address' => (string)$employee->getData('address'),
                    'status' => (int)$employee->getData('status'),
                    'hobbies' => $hobbies ? explode(',', $hobbies) : [],
                    'hobbies_text' => $hobbies
                ]
            ]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
