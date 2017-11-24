<?php

namespace Grapesc\GrapeFluid\MenuModule\Presenters;

use Grapesc\GrapeFluid\MenuModel\Model\ItemModel;
use Grapesc\GrapeFluid\ModuleRepository;
use Nette\Application\BadRequestException;
use Nette\Http\IResponse;


class PagePresenter extends \Grapesc\GrapeFluid\Application\BasePresenter
{

	/** @var ItemModel @inject */
	public $items;

	/** @var ModuleRepository @inject */
	public $moduleRepository;


	public function actionDefault($slug = "")
	{
		if ($slug != "" && $item = $this->items->getItemBy($slug, "slug")) {
			$this->checkPermission('menu.item', 'read.' . $item->id);
			$this->forward($item->url, json_decode($item->params, true));
		} elseif ($slug == "" && $mainPage = $this->items->getItemBy(1, "main")) {
			$this->checkPermission('menu.item', 'read.' . $mainPage->id);
			$this->forward($mainPage->url, json_decode($mainPage->params, true));
		} elseif ($this->moduleRepository->moduleExist('content')) {
			$page = $this->context->getByType('\Grapesc\GrapeFluid\ContentModule\Model\PageModel')
				->getTableSelection()->select('id')->where("slug", $slug)->fetch();
			if($page) {
				$this->checkPermission('content.page.item', 'read.' . $page->id);
				$this->forward(':Content:Content:default', ['id' => $page->id]);
			}
		}

		throw new BadRequestException("Požadovaná stránka neexistuje!");
	}


	/**
	 * @param $resource
	 * @param $privilege
	 * @throws BadRequestException
	 */
	private function checkPermission($resource, $privilege)
	{
		if (!$this->user->isAllowed($resource, $privilege)) {
			throw new BadRequestException("Nemáte oprávnění pro zobrazení požadované stránky", IResponse::S403_FORBIDDEN);
		}
	}

}