<?php

namespace Adobe\Employee\Ui\Dataprovider\Employee;

use Adobe\Employee\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class FormDataProvider extends AbstractDataProvider
{
    protected $loadedData = [];
    protected $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        foreach ($this->collection->getItems() as $employee) {
            $this->loadedData[$employee->getId()] = $employee->getData();
        }

        $data = $this->dataPersistor->get('adobe_employee');
        if (!empty($data)) {
            $employee = $this->collection->getNewEmptyItem();
            $employee->setData($data);
            $this->loadedData[$employee->getId() ?: 0] = $employee->getData();
            $this->dataPersistor->clear('adobe_employee');
        }

        return $this->loadedData;
    }
}