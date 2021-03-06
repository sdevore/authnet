<?php

class AuthnetTransactionsController extends AuthnetAppController {
	
	public function admin_add() {
		if (!empty($this->data)) {
			if ($result = $this->AuthnetTransaction->save($this->request->data)) {
				$this->request->data = Set::merge($this->request->data, $result);
				$this->Session->setFlash(__('Transaction approved. Transaction id: '.$this->AuthnetTransaction->id));
				$this->request->data[$this->AuthnetTransaction->alias] = null;
			} else {
				$this->__flashDecline();
			}
		}
	}
	
	public function admin_update() {
		$this->add();
	}
	
	public function delete() {
		if (!empty($this->data)) {
			debug($this->data);
			if ($result = $this->AuthnetTransaction->delete($this->data[$this->AuthnetTransaction->alias][$this->AuthnetTransaction->primaryKey])) {
				$this->Session->setFlash(__('Transaction voided.'));
				debug($result);
			} else {
				debug($this->AuthnetTransaction->invalidFields());
			}
		}
	}
	
	private function __flashDecline(&$Model = null) {
		if (empty($Model)) {
			$Model = $this->AuthnetTransaction;
		}
		$invalid = $Model->invalidFields();
		if (!empty($invalid)) {
			debug($invalid);
			if (!empty($invalid['declined'])) {
				$this->Session->setFlash('<span title="Subcode: ' . $invalid['declined'][1] . '">' . $invalid['declined'][0] . '</span>: ' . $invalid['declined'][2]);
			} else {
				$this->Session->setFlash(__('The transaction could not be processed. Please review the errors below.'));
			}
		}
	}

}

?>
