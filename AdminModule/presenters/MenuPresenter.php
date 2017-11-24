<?php

namespace Grapesc\GrapeFluid\AdminModule\Presenters;

use Grapesc\GrapeFluid\FluidFormControl\FluidFormControl;
use Grapesc\GrapeFluid\MenuModel\Model\ItemModel;
use Grapesc\GrapeFluid\MenuModel\Model\MenusModel;
use Grapesc\GrapeFluid\MenuModule\ItemForm;
use Grapesc\GrapeFluid\MenuModule\Grid\MenuGrid;
use Grapesc\GrapeFluid\MenuModule\MenuForm;
use Nette\Database\Table\ActiveRow;


class MenuPresenter extends BasePresenter
{

	/** @var MenusModel @inject */
	public $menus;

	/** @var ItemModel @inject */
	public $items;

	/** @var MenuForm @inject */
	public $menuForm;

	/** @var ItemForm @inject */
	public $itemForm;


	public function actionEdit($id = 0, $item_id = 0)
	{
		$menu = $this->menus->getItem($id);

		if ($menu) {
			$this->getComponent("menuForm")->setDefaults($this->menus->getItem($id));
			if ($item_id) {
				$this->getComponent("itemForm")->setDefaults($this->items->getItem($item_id));
			} else {
				$this->getComponent("itemForm")->setDefaults(["menu_id" => $id]);
			}

			$this->template->collections = $this->collector->getAllCollections();
			$this->template->menu = $menu;
			$this->template->items = $menu->related('menu_item.menu_id')->order("position ASC");
		} else {
			$this->flashMessage("Požadované menu neexistuje", "warning");
			$this->redirect(":Admin:Menu:default");
		}
	}


	public function createComponentMenuForm()
	{
		return new FluidFormControl($this->menuForm);
	}


	public function createComponentItemForm()
	{
		return new FluidFormControl($this->itemForm);
	}


	public function handleDeleteItem($item_id = null)
	{
		/** @var ActiveRow $item */
		if ($item_id !== null && $item = $this->items->getItem($item_id))
		{
			$item->delete();
			$this->flashMessage("Položka odstraněna", "info");
			$this->redrawControl("flashMessages");
			$this->redrawControl("items");
		}
	}


	public function handlePromoteMain($item_id = null)
	{
		/** @var ActiveRow $item */
		if ($item_id !== null && $item = $this->items->getItem($item_id))
		{
			$this->items->unsetMainPages();
			$item->update(["main" => 1]);
			$this->flashMessage("Hlavní stránka změněna", "info");
			$this->redrawControl("flashMessages");
			$this->redrawControl("items");
		}
	}


	public function handleEditItem($item_id = null)
	{
		/** @var ActiveRow $item */
		if ($item_id !== null && $item = $this->items->getItem($item_id))
		{
			/** @var FluidFormControl $itemComponent */
			$itemComponent = $this->getComponent("itemForm");
			$itemComponent->getForm()->setDefaults($this->items->getItem($item));

			$this->template->edit = $item_id;

			$this->redrawControl("itemControl");
			$this->redrawControl("items");
		}
	}


	public function handleReorderMenuItems()
	{
		foreach ($this->getHttpRequest()->getPost("item") as $pos => $slide) {
			$this->items->update(["position" => $pos], $slide);
		}
		$this->redrawControl("flashMessages");
		$this->redrawControl("items");
	}


	protected function createComponentMenuGrid()
	{
		return new MenuGrid($this->menus, $this->translator);
	}

}