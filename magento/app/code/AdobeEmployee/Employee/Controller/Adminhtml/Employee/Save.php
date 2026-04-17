<?php

namespace Adobe\Employee\Controller\Adminhtml\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Adobe\Employee\Model\EmployeeFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Adobe_Employee::save';

    protected EmployeeRepositoryInterface $employeeRepository;
    protected EmployeeFactory $employeeFactory;
    protected DataPersistorInterface $dataPersistor;

    public function __construct(
        Action\Context $context,
        EmployeeRepositoryInterface $employeeRepository,
        EmployeeFactory $employeeFactory,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->employeeRepository = $employeeRepository;
        $this->employeeFactory = $employeeFactory;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $this->_redirect('*/*/index');
        }

        $id = isset($data['id']) ? (int)$data['id'] : null;

        try {
            if (empty(trim((string)($data['name'] ?? '')))) {
                throw new LocalizedException(__('Name is required.'));
            }
            if (empty(trim((string)($data['designation'] ?? '')))) {
                throw new LocalizedException(__('Designation is required.'));
            }
            if (empty($data['joining_date'])) {
                throw new LocalizedException(__('Joining date is required.'));
            }

            $employee = $id
                ? $this->employeeRepository->getById($id)
                : $this->employeeFactory->create();

            $hobbies = $data['hobbies'] ?? [];
            if (is_array($hobbies)) {
                $hobbies = implode(',', $hobbies);
            }

            $employee->setData('name', trim((string)$data['name']));
            $employee->setData('joining_date', $data['joining_date']);
            $employee->setData('designation', trim((string)$data['designation']));
            $employee->setData('address', $data['address'] ?? '');
            $employee->setData('status', (int)($data['status'] ?? 0));
            $employee->setData('hobbies', $hobbies);

            $this->employeeRepository->save($employee);

            $this->messageManager->addSuccessMessage(__('Employee saved successfully.'));
            $this->dataPersistor->clear('adobe_employee');

            if ($this->getRequest()->getParam('back')) {
                return $this->_redirect('*/*/edit', ['id' => $employee->getId()]);
            }

            return $this->_redirect('*/*/index');
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('This employee no longer exists.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the employee.'));
        }

        $this->dataPersistor->set('adobe_employee', $data);
        return $id
            ? $this->_redirect('*/*/edit', ['id' => $id])
            : $this->_redirect('*/*/new');
    }
}