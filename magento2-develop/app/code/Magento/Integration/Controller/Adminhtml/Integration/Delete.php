<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Integration\Controller\Adminhtml\Integration;

use Magento\Integration\Block\Adminhtml\Integration\Edit\Tab\Info;

class Delete extends \Magento\Integration\Controller\Adminhtml\Integration
{
    /**
     * Delete the integration.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Exception
     */
    public function execute()
    {
        $integrationId = (int)$this->getRequest()->getParam(self::PARAM_INTEGRATION_ID);

        if ($integrationId) {
            $integrationData = $this->_integrationService->get($integrationId);
            if ($this->_integrationData->isConfigType($integrationData)) {
                $this->messageManager->addError(
                    __(
                        "Uninstall the extension to remove integration '%1'.",
                        $this->escaper->escapeHtml($integrationData[Info::DATA_NAME])
                    )
                );
                return $this->getDefaultResult();
            }
            $integrationData = $this->_integrationService->delete($integrationId);
            if (!$integrationData[Info::DATA_ID]) {
                $this->messageManager->addError(__('This integration no longer exists.'));
            } else {
                //Integration deleted successfully, now safe to delete the associated consumer data
                if (isset($integrationData[Info::DATA_CONSUMER_ID])) {
                    $this->_oauthService->deleteConsumer($integrationData[Info::DATA_CONSUMER_ID]);
                }
                $this->_registry->register(self::REGISTRY_KEY_CURRENT_INTEGRATION, $integrationData);
                $this->messageManager->addSuccess(
                    __(
                        "The integration '%1' has been deleted.",
                        $this->escaper->escapeHtml($integrationData[Info::DATA_NAME])
                    )
                );
            }
        } else {
            $this->messageManager->addError(__('Integration ID is not specified or is invalid.'));
        }

        return $this->getDefaultResult();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function getDefaultResult()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}