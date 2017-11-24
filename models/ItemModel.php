<?php

namespace Grapesc\GrapeFluid\MenuModel\Model;

use Grapesc\GrapeFluid\Model\BaseModel;


class ItemModel extends BaseModel
{

	/**
	 * Všechny stránky označené jako hlavní stránka odznačí
	 * a nastaví vybranou jako hlavní
	 */
	public function unsetMainPages()
	{
		$this->getTableSelection()->update(["main" => 0]);
	}

}