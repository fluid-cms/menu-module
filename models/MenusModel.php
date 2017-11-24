<?php

namespace Grapesc\GrapeFluid\MenuModel\Model;

use Grapesc\GrapeFluid\Model\BaseModel;


class MenusModel extends BaseModel
{

	/**
	 * @param $name
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 */
	public function getMenu($name)
	{
		return $this->getItemBy($name, "name");
	}

}