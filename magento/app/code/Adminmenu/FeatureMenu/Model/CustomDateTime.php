<?php
namespace Adminmenu\FeatureMenu\Model;

class CustomDateTime extends \Magento\Framework\Stdlib\DateTime\DateTime
{
    public function gmtDate($format = null, $input = null)
    {
        // LOG (proof)
        file_put_contents(
            BP . '/var/log/preference-test.log',
            "Preference HIT\n",
            FILE_APPEND
        );

        // RETURN MODIFIED VALUE
        return parent::gmtDate($format, $input) . ' [PREFERENCE WORKING]';
    }
}