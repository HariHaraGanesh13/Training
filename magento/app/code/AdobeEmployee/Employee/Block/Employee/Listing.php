<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adobe\Employee\Block\Employee;

use Magento\Framework\View\Element\Template;
use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;

class Listing extends Template
{
    protected $collectionFactory;

    public function __construct(
        Template\Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
    }

    public function getEmployees()
    {
        return $this->collectionFactory->create();
    }

    public function getAddUrl()
    {
        return $this->getUrl('adobeemployee/employee/new');
    }

    public function getEditUrl($id)
    {
        return $this->getUrl('adobeemployee/employee/edit', ['id' => $id]);
    }

    public function getDeleteUrl($id)
    {
        return $this->getUrl('adobeemployee/employee/delete', ['id' => $id]);
    }
}
