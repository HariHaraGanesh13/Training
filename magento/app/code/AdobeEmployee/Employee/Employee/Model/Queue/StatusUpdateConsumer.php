<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Model\Queue;

use Adobe\Employee\Model\EmployeeFactory;

/**
 * Queue consumer for employee status updates.
 */
class StatusUpdateConsumer
{
    /**
     * @var EmployeeFactory
     */
    protected EmployeeFactory $employeeFactory;

    /**
     * Constructor
     *
     * @param EmployeeFactory $employeeFactory
     */
    public function __construct(
        EmployeeFactory $employeeFactory
    ) {
        $this->employeeFactory = $employeeFactory;
    }

    /**
     * Process queue message.
     *
     * @param string $message
     * @return void
     */
    public function process(string $message): void
    {
        $data = json_decode($message, true);

        if (
            !isset($data['ids']) ||
            !isset($data['status'])
        ) {
            return;
        }

        foreach ($data['ids'] as $employeeId) {

            $employee = $this->employeeFactory
                ->create()
                ->load($employeeId);

            if ($employee->getId()) {

                $employee->setStatus(
                    (int)$data['status']
                );

                $employee->save();
            }
        }
    }
}