<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Ajax;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Get one employee for edit.
 */
class Get implements HttpGetActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var EmployeeRepositoryInterface
     */
    private EmployeeRepositoryInterface $employeeRepository;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param EmployeeRepositoryInterface $employeeRepository
     * @param Session $customerSession
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        EmployeeRepositoryInterface $employeeRepository,
        Session $customerSession
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->employeeRepository = $employeeRepository;
        $this->customerSession = $customerSession;
    }

    /**
     * Return one employee belonging to current customer.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $result->setData(['success' => false, 'message' => __('Please login first.')]);
        }

        $id = (int)($_GET['id'] ?? 0);
        $customerId = (int)$this->customerSession->getCustomerId();

        try {
            $employee = $this->employeeRepository->getById($id);

            if ((int)$employee->getData('customer_id') !== $customerId) {
                return $result->setData(['success' => false, 'message' => __('Unauthorized access.')]);
            }

            return $result->setData([
                'success' => true,
                'item' => [
                    'id' => $employee->getId(),
                    'name' => $employee->getData('name'),
                    'joining_date' => $employee->getData('joining_date'),
                    'designation' => $employee->getData('designation'),
                    'address' => $employee->getData('address'),
                    'status' => (int)$employee->getData('status'),
                    'hobbies' => $employee->getData('hobbies')
                        ? explode(',', $employee->getData('hobbies'))
                        : []
                ]
            ]);
        } catch (NoSuchEntityException $e) {
            return $result->setData(['success' => false, 'message' => __('Employee not found.')]);
        }
    }
}
