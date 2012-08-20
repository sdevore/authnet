<?php
    /**
     * Plugin model for "Authorize.Tet Credit Card Transaction Processing".
     *
     * @author Alan Blount <alan@zeroasterisk.com>
     * @link http://zeroasterisk.com
     * @copyright (c) 2010 Alan Blount
     * @license MIT License - http://www.opensource.org/licenses/mit-license.php
     *
     * @example
     $saved = $this->AuthnetTransaction->save(array(
     	'amount' => 100,
     	'cc_account' => '000000000000000',
     	'cc_name' => 'john doe',
     	'cc_expires' => '11/11',
     ));
     $transaction_id = $this->AuthnetTransaction->id
     debug($saved);
     debug($transaction_id);
     debug($this->AuthnetTransaction->log);
     */
    class AuthnetTransaction extends AuthnetAppModel {
        public $useDbConfig = 'authnet';
        public $primaryKey = 'transaction_id';
        public $displayField = 'transaction_id';
        /**
         * standard validate array
         * @var array
         */
        public $validate = array(
            'amount' => array('numeric' => array(
                    'rule' => 'numeric',
                    'message' => 'Invalid amount.',
                    'required' => false,
                    'allowEmpty' => true
                )),
            'card_number' => array(
                /*
                 'cc' => array(
                 'rule' => array('cc', 'fast'),
                 'message' => 'Invalid credit card number.',
                 'required' => false,
                 'allowEmpty' => true
                 )
                 */
            ),
            'expiration' => array('mmyyyy' => array(
                    'rule' => array(
                        'mmyyyy',
                        'expiration'
                    ),
                    'message' => 'Invalid expiration date.',
                    'required' => false,
                    'allowEmpty' => true
                ),
                /*
                 'notExpired' => array(
                 'rule' => array('notExpired', 'expiration'),
                 'message' => 'Credit card is expired according to date
                 provided.',
                 'required' => false,
                 'allowEmpty' => true
                 )
                 */
            )
        );
        /**
         * standard beforeSave()
         * @return bool
         */
        public function beforeSave() {
            if (!isset($this->data[$this->alias])) {
                $this->data = array($this->alias => $this->data);
            }
            if (isset($this->data[$this->alias]['expiration'])) {
                $this->data[$this->alias]['expiration'] = preg_replace('/[^0-9]/', '', $this->data[$this->alias]['expiration']);
            }
            return true;
        }

        /**
         * Helper function to determine if CC expire format is correct
         * @return bool
         */
        public function mmyyyy($data) {
            $value = preg_replace('/[^0-9]/', '', current($data));
            if (strlen($value) == 4 || strlen($value) == 6) {
                return true;
            } elseif ((strlen($value) == 3 || strlen($value) == 5) && substr($value, 0, 1) !== 0) {
                return true;
            }
            return false;
        }

        /**
         * Helper function to determine if CC has not expired
         * @return bool
         */
        public function notExpired($data) {
            $value = preg_replace('/[^0-9]/', '', current($data));
            if (strlen($value) > 6) {
                return false;
            } elseif (strlen($value) > 4) {
                $year = str_pad(substr($value, -4), 4, "0", STR_PAD_LEFT);
                $month = str_pad(substr($value, 0, -4), 2, "0", STR_PAD_LEFT);
            } else {
                $year = str_pad(substr(date('Y'), 0, 2) . substr($value, -2), 2, "0", STR_PAD_LEFT);
                $month = str_pad(substr($value, 0, -2), 2, "0", STR_PAD_LEFT);
            }
            $epoch = strtotime("{$year}-{$month}-01");
            return ($epoch > time());
        }

        /**
         * Overwrite of the exists function, to be used for delete()
         * @return bool
         */
        public function exists() {
            if (!empty($this->data)) {
                if (!empty($this->data[$this->alias]['transaction_id'])) {
                    $this->id = $this->data[$this->alias]['transaction_id'];
                }
            }
            if (!empty($this->id)) {
                $this->__exists = true;
                return $this->__exists;
            }
            return false;
        }

        /**
         * Overwrite of the save() function
         * we prepare for the repsonse array, and parse the status to see if it's
         * an error or not
         * @param mixed $data
         * @param mixed $validate true
         */
        public function save($data = array(), $validate = true) {
            $this->response = array();
            $response = parent::save($data, $validate);
            if (!empty($this->response) && isset($this->response['status']) && $this->response['status'] == "good") {
                return $this->response;
            }
            if (isset($this->response['error']) && !empty($this->response['error'])) {
                $this->validationErrors[] = $this->response['error'];
                return false;
            }
            $this->validationErrors[] = "unknown error";
            return false;
        }

        /**
         * Overwrite of the delete() function
         * we prepare for the repsonse array, and parse the status to see if it's
         * an error or not
         * @param mixed $data
         * @param mixed $validate true
         */
        public function delete($id = null) {
            $this->response = array();
            $this->data[$this->alias]['transaction_id'] = $id;
            $response = parent::delete($id);
            if (!empty($this->response) && isset($this->response['status']) && $this->response['status'] == "good") {
                return true;
            }
            if (isset($this->response['error']) && !empty($this->response['error'])) {
                $this->validationErrors[] = $this->response['error'];
                return false;
            }
            $this->validationErrors[] = "unknown error";
            return false;
        }

    }
?>