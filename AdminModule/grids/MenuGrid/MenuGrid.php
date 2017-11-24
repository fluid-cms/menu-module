<?php

namespace Grapesc\GrapeFluid\MenuModule\Grid;

use Grapesc\GrapeFluid\FluidGrid;
use Nette\Database\Table\ActiveRow;


class MenuGrid extends FluidGrid
{

	protected function build()
	{
		$this->setItemsPerPage(15);
		$this->skipColumns(["class_parent", "class_child"]);
		$this->addRowAction("edit", "Upravit", [$this, 'editMenu']);
		$this->addRowAction("delete", "Smazat", [$this, 'deleteMenu']);
		parent::build();
		$this->addColumn("count");
	}


	public function deleteMenu(ActiveRow $record)
	{
		$record->delete();
		$this->getPresenter()->flashMessage("Menu smazáno", "success");
		$this->getPresenter()->redrawControl("flashMessages");
	}


	public function editMenu(ActiveRow $record)
	{
		$this->getPresenter()->redirect(":Admin:Menu:edit", ["id" => $record->id]);
	}

}