<?php

namespace Grapesc\GrapeFluid\MenuModule\Control;

use Grapesc\GrapeFluid\MagicControl\BaseMagicControl;
use Grapesc\GrapeFluid\MenuModel\Model\ItemModel;
use Grapesc\GrapeFluid\MenuModel\Model\MenusModel;
use Nette\Database\Table\ActiveRow;


/**
 * Class MenuControl
 * @package Grapesc\GrapeFluid\MenuModule\Control
 * @usage: {magicControl menu, ['uid', 'name', 'parent class', 'child class', 'anchors only']}
 */
class MenuControl extends BaseMagicControl
{

	/** @var ItemModel @inject */
	public $items;

	/** @var MenusModel @inject */
	public $menus;

	/** @var ActiveRow $menu */
	private $menu;

	/** @var string|null */
	protected $snippetElement = null;

	private $class_parent = "";
	private $class_child = "";
	private $anchorsOnly = false;


	/**
	 * @param array $params
	 */
	public function setParams(array $params = [])
	{
		if (isset($params[1])) {
			if (!$this->menu = $this->menus->getMenu($params[1])) {
				throw new \LogicException("Menu Control - Menu with name '$params[1]' doesn't exists");
			}
		} else {
			throw new \LogicException("Menu Control - Parameter 'name' must be specified");
		}
		$this->class_parent = isset($params[2]) && $params[2] != null ? $params[2] : $this->menu->class_parent;
		$this->class_child = isset($params[3]) && $params[3] != null ? $params[3] : $this->menu->class_child;
		$this->anchorsOnly = isset($params[4]) && $params[4] != null ? $params[4] : false;
	}


	public function render()
	{
		$this->template->setFile(__DIR__ . '/menu.latte');
		$this->template->parent = $this->class_parent;
		$this->template->child = $this->class_child;
		$this->template->anchorsOnly = $this->anchorsOnly;
		$this->template->items = $this->menu->related("menu_item.menu_id")->order("position ASC");
		$this->template->render();
	}


	public function isActive($url, $params, $override = "")
	{
		$presenter = $this->getPresenter();

		$presenterParameters = $presenter->getParameters();
		unset($presenterParameters['action']);

		$params = json_decode($params, true);

		if ($override != "" && strpos(strtolower($presenter->getAction(true)), strtolower($override)) !== false) {
			return true;
		} elseif (strtolower($presenter->getAction(true)) == strtolower($url)) {
			if (!$params OR ($params AND count(array_intersect_assoc($params, $presenterParameters)) == count($params))) {
				return true;
			}
		}

		return false;
	}

}
