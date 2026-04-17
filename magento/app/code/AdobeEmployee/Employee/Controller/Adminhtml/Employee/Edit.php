<?php

namespace Adobe\Employee\Controller\Adminhtml\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends Index
{
    protected EmployeeRepositoryInterface $employeeRepository;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->employeeRepository = $employeeRepository;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if ($id) {
            try {
                $this->employeeRepository->getById($id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This employee no longer exists.'));
                return $this->_redirect('*/*/index');
            }
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Adobe_Employee::employee');
        $resultPage->getConfig()->getTitle()->prepend($id ? __('Edit Employee') : __('New Employee'));

        return $resultPage;
    }
}