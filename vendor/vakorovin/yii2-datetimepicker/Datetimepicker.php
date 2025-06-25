<?php

/**
 * Yii2 DateTimePicker
 * 
 * @link https://github.com/vakorovin/yii2-datetimepicker
 * @license https://github.com/vakorovin/yii2-datetimepicker/blob/master/LICENSE MIT
 * @author Vladimir Korovin <rolan1986@gmail.com>
 * @see http://xdsoft.net/jqplugins/datetimepicker
 */

namespace vakorovin\datetimepicker;

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\InputWidget;

class Datetimepicker extends InputWidget
{
	public $options = [];

	public function init()
    {
		parent::init();
        Html::addCssClass($this->options, 'form-control');
	}

	public function run()
    {
		Assets::register($this->getView());

		if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }

		$options = "";
		if (!empty($this->options)) {
			$options .= "{\n";
			foreach ((array) $this->options as $key => $value) {
                if (is_array($value)) {
                    $values = [];
                    foreach ($value as $_key => $_value) {
                        if (is_int($_value) || is_float($_value)) {
                            $values[] = $_value; // хотя по факту не нужно, используется только для передачи дат
                        } else {
                            $values[] = "'{$_value}'";
                        }
                    }
                    $value = "[" . implode(', ', $values) . "]";
                    $options .= "    {$key}: {$value},\n";
                } elseif (is_int($value) || is_float($value)) {
                    $options .= "    {$key}: {$value},\n";
                } else {
                    $options .= "    {$key}: '{$value}',\n";
                }
            }
			$options .= "}\n";
		}

		$JavaScript = "jQuery('";
		$JavaScript .= '#'.$this->options['id'];
		$JavaScript .= "').datetimepicker({$options});";

		$this->getView()->registerJs($JavaScript, View::POS_END);
	}
}
