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

class Delete extends AbstractAccount
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
                throw new \Exception('Unauthorized access.');
            }

            $this->employeeRepository->delete($employee);

            return $result->setData([
                'success' => true,
                'message' => 'Employee deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}