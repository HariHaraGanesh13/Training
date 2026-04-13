<?php

namespace Adobe\Employee\Block\Employee;

use Magento\Framework\View\Element\Template;
use Adobe\Employee\Api\EmployeeRepositoryInterface;

class Form extends Template
{
    /**
     * @var EmployeeRepositoryInterface
     */
    protected $employeeRepository;

    /**
     * @param Template\Context $context
     * @param EmployeeRepositoryInterface $employeeRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        EmployeeRepositoryInterface $employeeRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->employeeRepository = $employeeRepository;
    }

    /**
     * Get employee for edit form
     *
     * @return \Adobe\Employee\Api\Data\EmployeeInterface|\Adobe\Employee\Model\Employee|null
     */
    public function getEmployee()
    {
        $id = (int) $this->getData('employee_id');

        if ($id) {
            return $this->employeeRepository->getById($id);
        }

        return null;
    }

    /**
     * Form action URL
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('adobeemployee/employee/save');
    }

    /**
     * Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('adobeemployee/employee/index');
    }
}