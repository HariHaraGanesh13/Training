<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Controller\Ajax;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\EmployeeFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Save employee via AJAX.
 */
class Save implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    private JsonFactory $resultJsonFactory;

    /**
     * @var EmployeeRepositoryInterface
     */
    private EmployeeRepositoryInterface $employeeRepository;

    /**
     * @var EmployeeFactory
     */
    private EmployeeFactory $employeeFactory;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * @param JsonFactory $resultJsonFactory
     * @param EmployeeRepositoryInterface $employeeRepository
     * @param EmployeeFactory $employeeFactory
     * @param Session $customerSession
     */
    public function __construct(
        JsonFactory $resultJsonFactory,
        EmployeeRepositoryInterface $employeeRepository,
        EmployeeFactory $employeeFactory,
        Session $customerSession
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->employeeRepository = $employeeRepository;
        $this->employeeFactory = $employeeFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * Save current customer's employee.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        if (!$this->customerSession->isLoggedIn()) {
            return $result->setData(['success' => false, 'message' => __('Please login first.')]);
        }

        $data = $_POST;
        $customerId = (int)$this->customerSession->getCustomerId();
        $id = isset($data['id']) ? (int)$data['id'] : 0;

        try {
            if (empty(trim((string)($data['name'] ?? '')))) {
                throw new LocalizedException(__('Name is required.'));
            }

            $employee = $id
                ? $this->employeeRepository->getById($id)
                : $this->employeeFactory->create();

            if ($id && (int)$employee->getData('customer_id') !== $customerId) {
                throw new LocalizedException(__('Unauthorized access.'));
            }

            $hobbies = $data['hobbies'] ?? [];
            if (is_array($hobbies)) {
                $hobbies = implode(',', $hobbies);
            }

            $employee->setData('customer_id', $customerId);
            $employee->setData('name', trim((string)($data['name'] ?? '')));
            $employee->setData('joining_date', $data['joining_date'] ?? null);
            $employee->setData('designation', $data['designation'] ?? '');
            $employee->setData('address', $data['address'] ?? '');
            $employee->setData('status', (int)($data['status'] ?? 0));
            $employee->setData('hobbies', $hobbies);

            $this->employeeRepository->save($employee);

            return $result->setData([
                'success' => true,
                'message' => __('Employee saved successfully.')
            ]);
        } catch (NoSuchEntityException $e) {
            return $result->setData(['success' => false, 'message' => __('Employee not found.')]);
        } catch (LocalizedException $e) {
            return $result->setData(['success' => false, 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return $result->setData(['success' => false, 'message' => __('Something went wrong while saving.')]);
        }
    }
}
