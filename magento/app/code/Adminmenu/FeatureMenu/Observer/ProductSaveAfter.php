<?php

namespace Adminmenu\FeatureMenu\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use Adminmenu\FeatureMenu\Helper\Data;

class ProductSaveAfter implements ObserverInterface
{
    private Data $helper;
    private LoggerInterface $logger;

    public function __construct(
        Data $helper,
        LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->logger = $logger;
    }

    public function execute(Observer $observer): void
    {
        if (!$this->helper->isEnabled()) {
            return;
        }

        $product = $observer->getEvent()->getProduct();

        if ($product && $product->getId()) {
            $this->logger->info(
                sprintf(
                    'Adminmenu_FeatureMenu: Product saved - %s (ID: %s)',
                    $product->getName(),
                    $product->getId()
                )
            );
        }
    }
}