<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\RestAjax;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\EmployeeFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;

class Save extends AbstractAccount
{
    private EmployeeRepositoryInterface $employeeRepository;
    private EmployeeFactory $employeeFactory;
    private JsonFactory $resultJsonFactory;
    private Session $customerSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        EmployeeRepositoryInterface $employeeRepository,
        EmployeeFactory $employeeFactory,
        JsonFactory $resultJsonFactory,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->employeeRepository = $employeeRepository;
        $this->employeeFactory = $employeeFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();
        $customerId = (int)$this->customerSession->getCustomerId();

        try {
            if (empty(trim((string)($data['name'] ?? '')))) {
                throw new \Exception('Name is required.');
            }

            if (empty(trim((string)($data['designation'] ?? '')))) {
                throw new \Exception('Designation is required.');
            }

            if (empty($data['joining_date'])) {
                throw new \Exception('Joining date is required.');
            }

            $id = isset($data['id']) && $data['id'] !== '' ? (int)$data['id'] : null;

            $employee = $id
                ? $this->employeeRepository->getById($id)
                : $this->employeeFactory->create();

            if ($id && (int)$employee->getData('customer_id') !== $customerId) {
                throw new \Exception('Unauthorized access.');
            }

            $hobbies = $data['hobbies'] ?? [];

            if (is_string($hobbies)) {
                $hobbies = [$hobbies];
            }

            $employee->setData('customer_id', $customerId);
            $employee->setData('name', trim((string)$data['name']));
            $employee->setData('joining_date', $data['joining_date']);
            $employee->setData('designation', trim((string)$data['designation']));
            $employee->setData('address', $data['address'] ?? '');
            $employee->setData('status', (int)($data['status'] ?? 1));
            $employee->setData('hobbies', is_array($hobbies) ? implode(',', $hobbies) : '');

            $this->employeeRepository->save($employee);

            return $result->setData([
                'success' => true,
                'message' => 'Employee saved successfully.'
            ]);
        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}