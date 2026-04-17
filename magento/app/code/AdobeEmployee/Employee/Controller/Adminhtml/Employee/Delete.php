<?php

namespace Adobe\Employee\Controller\Adminhtml\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Adobe_Employee::delete';

    protected EmployeeRepositoryInterface $employeeRepository;

    public function __construct(
        Action\Context $context,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        parent::__construct($context);
        $this->employeeRepository = $employeeRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id) {
            $this->messageManager->addErrorMessage(__('We can\'t find an employee to delete.'));
            return $this->_redirect('*/*/index');
        }

        try {
            $this->employeeRepository->deleteById($id);
            $this->messageManager->addSuccessMessage(__('Employee deleted successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting the employee.'));
            return $this->_redirect('*/*/edit', ['id' => $id]);
        }

        return $this->_redirect('*/*/index');
    }
}