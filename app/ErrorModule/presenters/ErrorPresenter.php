<?php

namespace ErrorModule;

use Nette\Environment;

class ErrorPresenter extends \Venne\Developer\Presenter\FrontPresenter {


	/**
	 * @param  Exception
	 * @return void
	 */
	public function renderDefault($exception)
	{
		if ($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = TRUE;
			$this->terminate();

		} elseif ($exception instanceof \Nette\Application\BadRequestException) {
			$code = $exception->getCode();
			
		} else {
			$code = 500;
			Debugger::log($exception, Debugger::ERROR); // and log exception
		}
		
		$this->template->error = $this->getContext()->cms->error->model->getError($code);
		$this->template->code = $code;
		if($this->template->error){
			$this->setView("error");
		}
		
		
	}

}