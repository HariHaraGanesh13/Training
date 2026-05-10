<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Adminhtml\Employee;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Mass status update controller.
 */
class MassStatus extends Action
{
    /**
     * Authorization level.
     */
    public const ADMIN_RESOURCE = 'Adobe_Employee::employee';

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $collectionFactory;

    /**
     * @var PublisherInterface
     */
    protected PublisherInterface $publisher;

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param PublisherInterface $publisher
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PublisherInterface $publisher
    ) {
        parent::__construct($context);

        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->publisher = $publisher;
    }

    /**
     * Execute mass status update.
     *
     * @return Redirect
     */
    public function execute(): Redirect
    {
        $status = (int)$this->getRequest()->getParam('status');

        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );

        $employeeIds = [];

        foreach ($collection as $employee) {
            $employeeIds[] = (int)$employee->getId();
        }

        $message = json_encode([
            'ids' => $employeeIds,
            'status' => $status
        ]);

        $this->publisher->publish(
            'adobe.employee.status.update',
            $message
        );

        $this->messageManager->addSuccessMessage(
            __('Employees added to queue successfully.')
        );

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}