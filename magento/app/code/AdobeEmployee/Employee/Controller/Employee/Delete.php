<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Employee;

use Magento\Customer\Controller\AbstractAccount;
use Adobe\Employee\Api\EmployeeRepositoryInterface;

class Delete extends AbstractAccount
{
    protected $employeeRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        parent::__construct($context);
        $this->employeeRepository = $employeeRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$id) {
            $this->messageManager->addErrorMessage(__('Employee ID is missing.'));
            return $resultRedirect->setPath('adobeemployee/employee/index');
        }

        try {
            $this->employeeRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('Employee deleted successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('adobeemployee/employee/index');
    }
}
