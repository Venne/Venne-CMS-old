<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace AdminModule\StaModule;

/**
 * @author Josef Kříž
 * @resource AdminModule\StaModule\Security
 */
class DefaultPresenter extends BasePresenter {

	/** @persistent */
	public $id = NULL;
	protected $items;
	protected $paginator;


	public function startup()
	{
		parent::startup();

		if(!$this->type){
			$this->type = "news";
		}
		
		$vpg = new \Venne\Utils\VisualPaginator($this, "paginator");
		$this->paginator = $vpg->getPaginator();
		$this->paginator->setItemsPerPage(20);

		$this->items = $this->context->databaseService->table("staItem")->where(array("type" => $this->type))->order("id DESC")->limit(15, $this->paginator->getOffset());
	}


	public function createComponentForm($name)
	{
		$form = new \Venne\Application\UI\Form($this, $name);
		foreach ($this->items as $item) {
			$form->addSubmit("edit_" . $item->id, "Edit");
			$form->addSubmit("gallery_" . $item->id, "Edit gallery");
			$form->addSubmit("delete_" . $item->id, "Delete");
		}
		$form->onSubmit[] = array($this, "handleDo");
		return $form;
	}


	public function handleDelete($id)
	{
		$service = $this->context->services->{"sta" . ucfirst($this->type)};
		
		$service->delete($service->repository->find($this->getParam("id")));
		$this->flashMessage("Odstraněn prvek s ID " . $id, "success");
		$this->redirect("this");
	}
	
	public function renderDefault()
	{
		$this->template->items = $this->items;
	}

}
