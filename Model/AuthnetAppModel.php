<?php

    class AuthnetAppModel extends AppModel {
		public $useDbConfig = 'authnet';
		
        public $log = array();
        // false to disable

        public $logModel = 'AuthnetTransactionLog';
        // false to disable

        public $config = array(
            'AuthnetPluginVersion' => '1.0',
            'logModel' => 'Authnet.AuthnetTransactionLog',
            'logModel.useTable' => null,
        );

        /**
         * Updates config from: app/Config/authorize_net.php
         * Sets up $this->logModel
         * @param mixed $id
         * @param string $table
         * @param mixed $ds
         */
        public function __construct($id = false, $table = null, $ds = null) {
        	Configure::load('authorize_net');
            $this->config = Configure::read('Authnet');
			
			// double-check we have required keys
            if (empty($this->config['login'])) {
                trigger_error(__d('authnet', "Invalid AUTHNET Configuration, missing 'login' field."), E_USER_WARNING);
                die();
            } elseif (empty($this->config['key'])) {
                trigger_error(__d('authnet', "Invalid AUTHNET Configuration, missing 'key' field."), E_USER_WARNING);
                die();
            }

            // initialize extras: transaction log model
            if (!empty($this->config['logModel']) && $this->config['logModel']['model']) {
                if (App::import('model', $this->config['logModel']['model'])) {
                    $this->logModel = ClassRegistry::init(array_pop(explode('.', $this->config['logModel']['model'])));
                    if (isset($this->config['logModel']['useTable']) && $this->config['logModel']['useTable'] !== null) {
                        $this->logModel->useTable = $this->config['logModel']['useTable'];
                    }
                }
            }

            ConnectionManager::create($this->useDbConfig, $this->config);
            $ds = &ConnectionManager::getDataSource($this->useDbConfig);
            parent::__construct($id, $table, $ds);

        }

        /**
         * Simple function to return the $config array
         * @param array $config if set, merge with existing array
         * @return array $config
         */
        public function config($config = array()) {
            $db = &ConnectionManager::getDataSource($this->useDbConfig);
            if (!empty($config) && is_array($config)) {
                $db->config = set::merge($db->config, $config);
            }
            return $db->config;
        }

    }
?>
