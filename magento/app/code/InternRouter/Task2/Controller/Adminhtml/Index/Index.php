<?php
namespace InternRouter\Task2\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'InternRouter_Task2::task2_page';

    protected PageFactory $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('InternRouter_Task2::task2_page');
        $resultPage->getConfig()->getTitle()->prepend(__('Task 2 Admin Page'));
        return $resultPage;
    }
}
