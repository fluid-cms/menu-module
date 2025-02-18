<?php

namespace Grapesc\GrapeFluid\MenuModule;

use Grapesc\GrapeFluid\CoreModule\AclFormTrait;
use Grapesc\GrapeFluid\MenuModel\Model\ItemModel;
use Grapesc\GrapeFluid\FluidFormControl\FluidForm;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Strings;


class ItemForm extends FluidForm
{

	use AclFormTrait;

	/** @var ItemModel @inject */
	public $items;


	protected function build(Form $form)
	{
		$form->addHidden("menu_id");
		$form->addHidden("id");

		$form->addText("label", "Titulek")
			->setAttribute("cols", 6)
			->setRequired("Musíte vyplnit titulek")
			->addRule(Form::MAX_LENGTH, "Maximální délka titulku je %s znaků", 50);

		$form->addText("slug", "Identifikátor (SEO slug)")
			->setRequired(false)
			->setAttribute("cols", 6)
			->setAttribute("help", "Bude automaticky převedeno na SEO friendly URL")
			->addRule(Form::MAX_LENGTH, "Maximální velikost je %s znaků", 200);

		$form->addSelect("icon", "Ikona")
			->setAttribute("buttons", true)
			->setItems([
				"empty", "television", "plug", "building", "facebook-official", "smile-o", "pencil", "check", "times"
			], false)
			->setDefaultValue("empty");

		$form->addText("url", "Odkaz")
			->setRequired("Odkaz je povinné pole")
			->addRule(Form::MAX_LENGTH, "Délka odkazu může být max. %s znaků", 255)
			->setAttribute("help", "Zadávejte v Nette Formátu - ':Modul:Presenter:akce'")
			->setAttribute("data-advanced", true);

		$form->addCheckbox("custom", "Odkaz ve vlastním tvaru - např. http://grapesc.cz")
			->setDefaultValue(0)
			->setAttribute("data-advanced", true);

		$form->addText("params", "Parametry")
			->setRequired(false)
			->setDefaultValue('{"id":null}')
			->addRule(Form::MAX_LENGTH, "Délka parametrů může být max. %s znaků", 255)
			->setAttribute("help", "Parametry zadávejte v JSON formátu. Fungují pouze v případě Nette Odkazu.")
			->setAttribute("data-advanced", true);

		$form->addText("override", "Odkaz pro aktivaci")
			->setRequired(false)
			->setDefaultValue("")
			->addRule(Form::MAX_LENGTH, "Délka aktivace může být max. %s znaků,", 250)
			->setAttribute("help", "Zadávejte v Nette formátu. Pokud bude odkaz obsahovat uvedený zápis, poté se v menu zaktivuje.")
			->setAttribute("data-advanced", true);

        $form->addText("class", "CSS třída prvku")
            ->setRequired(false)
            ->setDefaultValue("")
            ->addRule(Form::MAX_LENGTH, "CSS třída prvku nesmí být delší než %s znaků", 100)
            ->setAttribute("data-advanced", true);

		$form->addCheckbox("main", "Hlavní stránka");

		$this->addAclInput('menu.item');
	}


	protected function submit(Control $control, Form $form)
	{
		$presenter = $control->getPresenter();
		$values    = $form->getValues('array');

		if ($form->hasErrors()) {
			$presenter->redrawControl('itemControl');
		} else {
			/** @var ActiveRow $item */
			if ($values['main']) {
				$this->items->unsetMainPages();
			}
			if ($values['id']) {
				$values['slug'] = Strings::webalize($values['slug'] == "" ? $values['label'] : $values['slug']);
				$this->items->update($this->items->clearingValues($values), $values['id']);
				$presenter->flashMessage("Odkaz byl úspěšně upraven", "success");
				$presenter->redrawControl("itemControl");
			} else  {
				unset($values['id']);
				$values['slug'] = Strings::webalize($values['slug'] == "" ? $values['label'] : $values['slug']);
				$this->createdId = $this->items->insert($this->items->clearingValues($values));
				$presenter->flashMessage("Odkaz byl úspěšně přidán", "success");
			}

			$this->saveAcl();
			$presenter->getComponent("itemForm")->getForm()->setValues(["menu_id" => $presenter->getParameter("id"), "icon" => "empty"], true);

			$presenter->redrawControl("flashMessages");
			$presenter->redrawControl("items");
		}
	}

}