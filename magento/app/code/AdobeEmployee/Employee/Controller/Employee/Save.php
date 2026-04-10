<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Employee;

use Magento\Customer\Controller\AbstractAccount;
use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\EmployeeFactory;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractAccount
{
    protected $employeeRepository;
    protected $employeeFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        EmployeeRepositoryInterface $employeeRepository,
        EmployeeFactory $employeeFactory
    ) {
        parent::__construct($context);
        $this->employeeRepository = $employeeRepository;
        $this->employeeFactory = $employeeFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$data) {
            return $resultRedirect->setPath('adobeemployee/employee/index');
        }

        try {
            $id = isset($data['id']) ? (int)$data['id'] : null;
            $employee = $id ? $this->employeeRepository->getById($id) : $this->employeeFactory->create();

            if (empty(trim($data['name'] ?? ''))) {
                throw new LocalizedException(__('Name is required.'));
            }

            if (empty(trim($data['designation'] ?? ''))) {
                throw new LocalizedException(__('Designation is required.'));
            }

            if (empty($data['joining_date'])) {
                throw new LocalizedException(__('Joining date is required.'));
            }

            $hobbies = isset($data['hobbies']) && is_array($data['hobbies']) ? implode(',', $data['hobbies']) : '';

            $employee->setName(trim($data['name']));
            $employee->setJoiningDate($data['joining_date']);
            $employee->setDesignation(trim($data['designation']));
            $employee->setAddress($data['address'] ?? '');
            $employee->setStatus((int)($data['status'] ?? 0));
            $employee->setHobbies($hobbies);

            $this->employeeRepository->save($employee);

            $this->messageManager->addSuccessMessage(__('Employee saved successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('adobeemployee/employee/index');
    }
}
