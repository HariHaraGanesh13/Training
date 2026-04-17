<?php

namespace Adobe\Employee\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class EmployeeActions extends Column
{
    public const EDIT_URL_PATH = 'adobe_employee/employee/edit';
    public const DELETE_URL_PATH = 'adobe_employee/employee/delete';

    protected UrlInterface $urlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')]['edit'] = [
                        'href' => $this->urlBuilder->getUrl(self::EDIT_URL_PATH, ['id' => $item['id']]),
                        'label' => __('Edit'),
                    ];

                    $item[$this->getData('name')]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::DELETE_URL_PATH, ['id' => $item['id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete employee'),
                            'message' => __('Are you sure you want to delete this employee?'),
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}