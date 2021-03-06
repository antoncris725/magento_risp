<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Customer\Model\Observer;

use Magento\Customer\Model\Logger;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Event\Observer;

/**
 * Customer log observer.
 */
class Log
{
    /**
     * Logger of customer's log data.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handler for 'customer_login' event.
     *
     * @param Observer $observer
     * @return void
     */
    public function logLastLoginAt(Observer $observer)
    {
        $this->logger->log(
            $observer->getEvent()->getCustomer()->getId(),
            ['last_login_at' => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)]
        );
    }

    /**
     * Handler for 'customer_logout' event.
     *
     * @param Observer $observer
     * @return void
     */
    public function logLastLogoutAt(Observer $observer)
    {
        $this->logger->log(
            $observer->getEvent()->getCustomer()->getId(),
            ['last_logout_at' => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)]
        );
    }
}
