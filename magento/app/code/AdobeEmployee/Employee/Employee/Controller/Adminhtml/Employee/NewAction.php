<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Adminhtml\Employee;

class NewAction extends Index
{
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Adobe_Employee::employee');
        $resultPage->getConfig()->getTitle()->prepend(__('New Employee'));
        return $resultPage;
    }
}
