<?php

namespace Adminmenu\FeatureMenu\Plugin;

use Magento\Theme\Block\Html\Footer;
use Adminmenu\FeatureMenu\Helper\Data;

class FooterPlugin
{
    private Data $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetCopyright(Footer $subject, $result)
    {
        if (!$this->helper->isEnabled()) {
            return $result;
        }

        return $result . ' | Adminmenu Feature Menu Enabled';
    }
}