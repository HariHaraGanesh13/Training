<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Adobe\Employee\Controller\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\EmployeeFactory;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save frontend employee form.
 */
class Save extends AbstractAccount
{
    /**
     * @var EmployeeRepositoryInterface
     */
    protected EmployeeRepositoryInterface $employeeRepository;

    /**
     * @var EmployeeFactory
     */
    protected EmployeeFactory $employeeFactory;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param EmployeeRepositoryInterface $employeeRepository
     * @param EmployeeFactory $employeeFactory
     * @param Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        EmployeeRepositoryInterface $employeeRepository,
        EmployeeFactory $employeeFactory,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->employeeRepository = $employeeRepository;
        $this->employeeFactory = $employeeFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Save employee for current logged-in customer.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('adobeemployee/employee/index');
        }

        try {
            $customerId = (int)$this->customerSession->getCustomerId();
            $id = isset($data['id']) ? (int)$data['id'] : null;

            $employee = $id
                ? $this->employeeRepository->getById($id)
                : $this->employeeFactory->create();

            if ($id && (int)$employee->getData('customer_id') !== $customerId) {
                throw new LocalizedException(__('You are not allowed to edit this employee.'));
            }

            if (empty(trim((string)($data['name'] ?? '')))) {
                throw new LocalizedException(__('Name is required.'));
            }

            if (empty(trim((string)($data['designation'] ?? '')))) {
                throw new LocalizedException(__('Designation is required.'));
            }

            if (empty($data['joining_date'])) {
                throw new LocalizedException(__('Joining date is required.'));
            }

            $hobbies = isset($data['hobbies']) && is_array($data['hobbies'])
                ? implode(',', $data['hobbies'])
                : '';

            $employee->setData('customer_id', $customerId);
            $employee->setData('name', trim((string)$data['name']));
            $employee->setData('joining_date', $data['joining_date']);
            $employee->setData('designation', trim((string)$data['designation']));
            $employee->setData('address', $data['address'] ?? '');
            $employee->setData('status', (int)($data['status'] ?? 0));
            $employee->setData('hobbies', $hobbies);

            $this->employeeRepository->save($employee);

            $this->messageManager->addSuccessMessage(__('Employee saved successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('adobeemployee/employee/index');
    }
}
