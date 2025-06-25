<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\models\wordpress_link;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Connection;

/**
 * This is base model class for WordpressLink forms.
 */
abstract class WPForm extends ActiveRecord implements WPFormInterface
{
    use WordpressLinkDbTrait;
}
