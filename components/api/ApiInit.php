<?php
namespace app\components\api;

class ApiInit {
	
	/**
	 * Init api tabs
	 * Returns array for yii2 bootstrap tabs
	 * @param Loan $model
	 * @return array
	 */
	public function run($model) {
		$api = $this->_findModules();
		$tabs = [];
		
		foreach ($api as $appClassName) {
			$apiClass = new $appClassName["class"]($model);
			$tabs[] = [
					'label' => $apiClass->getLabel(),
					'content' => $apiClass->getContent(),
			];
		}

		return $tabs;
	}
	
	/**
	 * get enabled api
	 * @return array
	 */
	private function _findModules() {
		$modules = scandir(__DIR__);
		$enabledModules = \Yii::$app->params["api"];
		$enabledModulesAdd = [];
		foreach ($modules as $module) {
			if (in_array($module, [".", "..", "ApiInit.php", "ApiModel.php"]))
				continue;

			foreach ($enabledModules as $enabledModuleName => $enabledModuleValue) {
				if ($enabledModuleValue && $enabledModuleName == str_replace(".php", "", $module)) {
					$enabledModulesAdd[] = ["class" => "app\\components\\api\\{$enabledModuleName}"];
				}
			}
			
		}
		return $enabledModulesAdd;
	}
}