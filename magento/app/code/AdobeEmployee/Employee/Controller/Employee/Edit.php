<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Employee;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Adobe\Employee\Api\EmployeeRepositoryInterface;
/**
 * Summary of Edit
 */
class Edit extends AbstractAccount
{
    protected $resultPageFactory;
    protected $registry;
    protected $employeeRepository;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        EmployeeRepositoryInterface $employeeRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->employeeRepository = $employeeRepository;
    }
    /**
     * Summary of execute
     */
    public function execute(){
    $resultPage = $this->resultPageFactory->create();

    $id = (int)$this->getRequest()->getParam('id');

    if ($id) {
        $block = $resultPage->getLayout()->getBlock('adobeemployee.employee.form');

        if ($block) {
            $block->setData('employee_id', $id);
        }
    }

    return $resultPage;
}
}
