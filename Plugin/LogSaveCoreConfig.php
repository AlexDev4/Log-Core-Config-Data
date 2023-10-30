<?php

namespace AOL\LogCoreConfig\Plugin;

use AOL\LogCoreConfig\Logger\Logger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use AOL\LogCoreConfig\Helper\Data;

class LogSaveCoreConfig
{
    protected Logger $logger;
    protected ScopeConfigInterface $_scopeConfig;

    protected Data $adminUser;

    public function __construct(
        Logger               $logger,
        ScopeConfigInterface $scopeConfig,
        Data $adminUser
    )
    {
        $this->logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->adminUser = $adminUser;
    }

    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        callable                     $proceed
    )
    {
        $oldConfigData = $this->_scopeConfig->getValue($subject->getSection());

        $result = $proceed();
        $newConfigData = $this->_scopeConfig->getValue($subject->getSection());

        $serializedNewValues = array_diff(array_map('serialize',$oldConfigData), array_map('serialize',$newConfigData));
        $newValues = array_map('unserialize', $serializedNewValues);
        $keys = [];
        foreach(array_keys($newValues) as $newValuesKey){
            $keys[] = $newValuesKey;
        }
        if(count($keys) === 1)
        {
            $this->logger->info('-- BEGIN -- One change in core_config_data by ' . $this->adminUser->getCurrentUserName());
            $this->logger->info("Old config of " . $keys[0] . " = " . json_encode($oldConfigData[$keys[0]]));
            $this->logger->info("New config of " . $keys[0] . " = " . json_encode($newConfigData[$keys[0]]));
            $this->logger->info('-- END --');
        }else{
            $this->logger->info('-- BEGIN -- Some changes in core_config_data by ' . $this->adminUser->getCurrentUserName());
            $i = 1;
            foreach($keys as $key){
                $this->logger->info("n°" . $i . " | old config of " . $key . " = " . json_encode($oldConfigData[$key]));
                $this->logger->info("n°" . $i . " | new config of " . $key . " = " . json_encode($newConfigData[$key]));
                $i++;
            }
            $this->logger->info('-- END --');
        }

        return $result;
    }
}
