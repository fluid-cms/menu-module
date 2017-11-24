<?php

namespace Grapesc\GrapeFluid\MenuModule;

use Grapesc\GrapeFluid\MenuModel\Model\MenusModel;
use Grapesc\GrapeFluid\FluidFormControl\FluidForm;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;


class MenuForm extends FluidForm
{

	/** @var MenusModel @inject */
	public $menus;


	protected function build(Form $form)
	{
		$form->addHidden("id");

		$form->addText("title", "Titulek")
			->setAttribute("cols", 6)
			->setRequired("Musíte vyplnit titulek")
			->addRule(Form::MAX_LENGTH, "Maximální délka titulku je %s znaků", 100);

		$form->addText("name", "Jméno / Identifikátor")
			->setAttribute("cols", 6)
			->setAttribute("help", "Slouží pro volání a identifikaci v kódu (může obsahovat pouze písmena [a-z] a být dlouhý 3 až 20 znaků)")
			->setRequired("Jméno / Identifikátor je povinné pole")
			->addRule(Form::PATTERN, "Zadaný identifikátor je neplatný", "[a-z]{3,20}");

		$form->addText("class_parent", "Třída rodiče (tag <ul>)")
			->setRequired(false)
			->setAttribute("cols", 6)
			->addRule(Form::MAX_LENGTH, "Délka třídy může být max. %s znaků", 100)
			->setAttribute("help", "Nastavte pouze v případě, že víte co děláte!");

		$form->addText("class_child", "Třída dětí (tag <li>)")
			->setRequired(false)
			->setAttribute("cols", 6)
			->addRule(Form::MAX_LENGTH, "Délka třídy může být max. %s znaků", 100)
			->setAttribute("help", "Nastavte pouze v případě, že víte co děláte!");
	}


	public function onSubmitEvent(Control $control, Form $form)
	{
		parent::onSubmitEvent($control, $form);

		$presenter = $control->getPresenter();
		$values = $form->getValues(true);

		$box = $this->menus->getMenu($values['name']);

		if (($values['id'] == "" && $box) || ($values['id'] != "" && $box && $box->id != $values['id'])) {
			$form->addError("Jméno / Identifikátor je již použit u jiného boxu!");
			$presenter->redrawControl("menuControl");
			return;
		}

		$id = null;
		$values['name'] = strtolower($values['name']);

		if ($values['id'] == "") {
			unset($values['id']);
			/** @var ActiveRow $menu */
			$menu = $this->menus->insert($values);
			$presenter->flashMessage("Menu úspěšně vytvořeno, nyní můžete přidávat položky.", "success");
			$presenter->redirect(":Admin:Menu:edit", ["id" => $menu->id]);
		} else {
			$this->menus->update($values, $values['id']);
			$presenter->flashMessage("Změny byly uloženy", "success");
			$presenter->redrawControl("flashMessages");
		}
	}

}