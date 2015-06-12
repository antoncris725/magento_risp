<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Quote\Model\Observer\Backend;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Config\Share as ShareConfig;
use Magento\Quote\Model\QuoteRepository;
use Magento\Framework\Event\Observer;

/**
 * Class CustomerQuote
 */
class CustomerQuote
{
    /**
     * @var ShareConfig
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ShareConfig $config
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ShareConfig $config,
        QuoteRepository $quoteRepository
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Set new customer group to all his quotes
     *
     * @param Observer $observer
     * @return void
     */
    public function dispatch(Observer $observer)
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomerDataObject();
        /** @var \Magento\Customer\Api\Data\CustomerInterface $origCustomer */
        $origCustomer = $observer->getEvent()->getOrigCustomerDataObject();
        if ($customer->getGroupId() !== $origCustomer->getGroupId()) {
            /**
             * It is needed to process customer's quotes for all websites
             * if customer accounts are shared between all of them
             */
            /** @var $websites \Magento\Store\Model\Website[] */
            $websites = $this->config->isWebsiteScope()
                ? [$this->storeManager->getWebsite($customer->getWebsiteId())]
                : $this->storeManager->getWebsites();

            foreach ($websites as $website) {
                try {
                    $quote = $this->quoteRepository->getForCustomer($customer->getId());
                    $quote->setWebsite($website);
                    $quote->setCustomerGroupId($customer->getGroupId());
                    $quote->collectTotals();
                    $this->quoteRepository->save($quote);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                }
            }
        }
    }
}
