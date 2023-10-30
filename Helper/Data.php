<?php

namespace AOL\LogCoreConfig\Helper;

use Magento\Backend\Model\Auth\Session;

class Data
{
    protected $authSession;
    public function __construct(Session $authSession)
    {
        $this->authSession = $authSession;
    }
    public function getCurrentUserName():string
    {
        return (string)$this->authSession->getUser()->getUserName();
    }

}
