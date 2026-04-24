<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Ajax;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Delete implements HttpPostActionInterface
{
    private JsonFactory $resultJsonFactory;
    private EmployeeRepositoryInterface $employeeRepository;
    private Session $customerSession;
    private RequestInterface $request;

    public function __construct(
        JsonFactory $resultJsonFactory,
        EmployeeRepositoryInterface $employeeRepository,
        Session $customerSession,
        RequestInterface $request
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->employeeRepository = $employeeRepository;
        $this->customerSession = $customerSession;
        $this->request = $request;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $result->setData(['success' => false, 'message' => __('Please login first.')]);
        }

        $id = (int)$this->request->getParam('id');
        $customerId = (int)$this->customerSession->getCustomerId();

        try {
            $employee = $this->employeeRepository->getById($id);

            if ((int)$employee->getData('customer_id') !== $customerId) {
                return $result->setData(['success' => false, 'message' => __('Unauthorized access.')]);
            }

            $this->employeeRepository->delete($employee);

            return $result->setData(['success' => true]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
