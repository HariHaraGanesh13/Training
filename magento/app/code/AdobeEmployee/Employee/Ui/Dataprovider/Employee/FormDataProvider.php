<?php

namespace Adobe\Employee\Ui\DataProvider\Employee;

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
            $this->loadedData[$employee->getId()] = [
                'id' => $employee->getId(),
                'name' => $employee->getName(),
                'joining_date' => $employee->getJoiningDate(),
                'designation' => $employee->getDesignation(),
                'address' => $employee->getAddress(),
                'status' => $employee->getStatus(),
                'hobbies' => $employee->getHobbies() ? explode(',', $employee->getHobbies()) : [],
            ];
        }

        $data = $this->dataPersistor->get('adobe_employee');
        if (!empty($data)) {
            $this->loadedData[0] = $data;
            $this->dataPersistor->clear('adobe_employee');
        }

        return $this->loadedData;
    }
}