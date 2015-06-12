<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Setup\Model\InstallerFactory;
use Magento\Setup\Model\WebLogger;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Magento\Setup\Validator\DbValidator;

class DatabaseCheck extends AbstractActionController
{
    /**
     * WebLogger to access log
     *
     * @var WebLogger
     */
    private $webLogger;

    /**
     * @var DbValidator
     */
    private $dbValidator;


    /**
     * Constructor
     *
     * @param WebLogger $webLogger
     * @param DbValidator $dbValidator
     */
    public function __construct(WebLogger $webLogger, DbValidator $dbValidator)
    {
        $this->webLogger = $webLogger;
        $this->dbValidator = $dbValidator;
    }

    /**
     * Result of checking DB credentials
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);

        try {
            $password = isset($params['password']) ? $params['password'] : '';
            $this->dbValidator->checkDatabaseConnection($params['name'], $params['host'], $params['user'], $password);
            $tablePrefix = isset($params['tablePrefix']) ? $params['tablePrefix'] : '';
            $this->dbValidator->checkDatabaseTablePrefix($tablePrefix);
            return new JsonModel(['success' => true]);
        } catch (\Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
