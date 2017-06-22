<?php
class Jarlssen_ChooserWidget_Adminhtml_Jarlssen_ChooserWidget_CustomerChooserController extends Mage_Adminhtml_Controller_Action {
	/**
	 * Chooser Source action
	 */
	public function chooserAction(){
		$uniqId        = $this->getRequest()->getParam('uniq_id');
		$massAction    = $this->getRequest()->getParam('use_massaction', false);

		$customerGrid = $this->getLayout()->createBlock('jarlssen_chooser_widget/customerChooser',
			'',
			array(
				'id'              => $uniqId,
				'use_massaction'  => $massAction,
			));

		$html = $customerGrid->toHtml();

		$this->getResponse()->setBody($html);
	}

	protected function _isAllowed(){
		return true;
	}
}