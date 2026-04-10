<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Block\Employee;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

class Form extends Template
{
    protected $registry;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
    }

    public function getEmployee()
    {
        return $this->registry->registry('current_employee');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('adobeemployee/employee/save');
    }

    public function getBackUrl()
    {
        return $this->getUrl('adobeemployee/employee/index');
    }
}
