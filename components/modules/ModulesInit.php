<?php
namespace app\components\modules;

class ModulesInit {

	public function setBehavior($definedBehaviors, $type) {
		return array_merge($definedBehaviors, $this->_findModules($type));
	}
	
	private function _findModules($type) {
		$modules = scandir(__DIR__);
		$enabledModules = \Yii::$app->params["modules"];
		
		$enabledModulesAdd = [];
		foreach ($modules as $module) {
			if (in_array($module, [".", "..", "ModulesInit.php"]))
				continue;
			
			if (substr($module, 0, strlen($type)) !== ucfirst($type))
				continue;
			
			foreach ($enabledModules as $enabledModuleName => $enabledModuleValue) {
				if ($enabledModuleName == str_replace(".php", "", $module)) {
					$enabledModulesAdd[] = ["class" => "app\\components\\modules\\{$enabledModuleName}"];
				}
			}
			
		}
		return $enabledModulesAdd;
	}
}