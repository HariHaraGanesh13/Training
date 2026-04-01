<?php

namespace Adminmenu\FeatureMenu\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Adminmenu_FeatureMenu::menu';

    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath(
            'adminhtml/system_config/edit',
            ['section' => 'adminmenu_featuremenu']
        );
    }
}