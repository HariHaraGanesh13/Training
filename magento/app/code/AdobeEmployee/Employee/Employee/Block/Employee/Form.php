<?php

namespace Adobe\Employee\Block\Employee;

use Adobe\Employee\Api\EmployeeRepositoryInterface;
use Magento\Framework\View\Element\Template;

/**
 * Frontend employee form block.
 */
class Form extends Template
{
    protected EmployeeRepositoryInterface $employeeRepository;

    public function __construct(
        Template\Context $context,
        EmployeeRepositoryInterface $employeeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->employeeRepository = $employeeRepository;
    }

    public function getEmployee()
    {
        $id = (int)$this->getRequest()->getParam('id');

        if (!$id) {
            return null;
        }

        try {
            return $this->employeeRepository->getById($id);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getSaveUrl(): string
    {
        return $this->getUrl('adobeemployee/employee/save');
    }

    public function getBackUrl(): string
    {
        return $this->getUrl('adobeemployee/employee/index');
    }
}
