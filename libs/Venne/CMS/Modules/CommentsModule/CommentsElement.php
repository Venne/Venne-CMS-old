<?php

/**
 * Venne:CMS (version 2.0-dev released on $WCDATE$)
 *
 * Copyright (c) 2011 Josef Kříž pepakriz@gmail.com
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace Venne\CMS\Elements;

use Venne;

/**
 * @author Josef Kříž
 */
class CommentsElement extends Venne\CMS\Developer\Element\ContentExtensionElement {


	public function startup()
	{
		parent::startup();
		if ($this->getPresenter()->getUser()->isAllowed("element-comments")) {

			$item = $this->getContext()->comments->getRepository()->findOneBy(
							array(
								"moduleItemId" => $this->moduleItemId,
								"moduleName" => $this->moduleName,
					));
			if ($item) {
				$this->template->show = true;

				$this->template->items = $this->getContext()->entityManager->getRepository($this->getContext()->params["venneModulesNamespace"] . "CommentsItem")->findBy(
								array(
									"key" => $this->moduleName . "-" . $this->moduleItemId
						));
			}
		}
	}


	public function createComponentForm($name)
	{
		$form = new Venne\Application\UI\Form($this, $name);
		$form->addText("author", "Name")->addRule(Venne\Application\UI\Form::FILLED, "Enter name");
		$form->addTextArea("text", "Comment")->addRule(Venne\Application\UI\Form::FILLED, "Enter comment");
		$form->addSubmit("submit", "Send");
		$form->onSuccess[] = array($this, "handleSave");
		return $form;
	}


	public function handleSave($form)
	{
		$entity = new \Venne\CMS\Modules\CommentsItem;
		$entity->author = $form["author"]->getValue();
		$entity->text = $form["text"]->getValue();
		$entity->key = $this->moduleName . "-" . $this->moduleItemId;
		$entity->order = 1;

		$this->getContext()->entityManager->persist($entity);
		$this->getContext()->entityManager->flush();

		$this->flashMessage("Comment has been saved", "success");
		$this->redirect("this");
	}
	
	public function handleDelete($id)
	{
		$item = $this->getContext()->entityManager->getRepository($this->getContext()->params["venneModulesNamespace"] . "CommentsItem")->find($id);
		$this->getContext()->entityManager->remove($item);
		$this->getContext()->entityManager->flush();
		$this->flashMessage("Comment has been removed", "success");
		$this->redirect("this");
	}

}
