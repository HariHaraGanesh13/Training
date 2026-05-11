<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Cron;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Cron job to delete employees older than 3 days.
 */
class DeleteOldEmployees
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var EmployeeRepositoryInterface
     */
    private EmployeeRepositoryInterface $employeeRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param CollectionFactory $collectionFactory
     * @param EmployeeRepositoryInterface $employeeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        EmployeeRepositoryInterface $employeeRepository,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->employeeRepository = $employeeRepository;
        $this->logger = $logger;
    }

    /**
     * Execute cron job.
     *
     * @return void
     */
    public function execute(): void
    {
        $date = date('Y-m-d H:i:s', strtotime('-30 minutes'));

        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('created_at', ['lt' => $date]);

        foreach ($collection as $employee) {
            try {
                $this->employeeRepository->delete($employee);

                $this->logger->info(
                    'Deleted old employee ID: ' . $employee->getId()
                );
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
