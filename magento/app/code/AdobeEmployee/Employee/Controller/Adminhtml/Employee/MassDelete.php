<?php

namespace Adobe\Employee\Controller\Adminhtml\Employee;

use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Action
{
    public const ADMIN_RESOURCE = 'Adobe_Employee::delete';

    protected Filter $filter;
    protected CollectionFactory $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deleted = 0;

            foreach ($collection as $employee) {
                $employee->delete();
                $deleted++;
            }

            $this->messageManager->addSuccessMessage(
                __('A total of %1 employee record(s) have been deleted.', $deleted)
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting the selected employees.'));
        }

        return $this->_redirect('*/*/index');
    }
}