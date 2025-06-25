<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\models\wordpress_link;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;

/**
 * This is the base model class for the WordpressLink database active records.
 */
trait WordpressLinkDbTrait
{

    /**
     * @return Connection the database connection used by this AR class.
     */
    public static function getDb(): ?Connection
    {
        try {
            $wp = Yii::$app->get('wordpressLink');
        } catch (InvalidConfigException $e) {
            return null;
        }
        if ($wp !== null) {
            return $wp->getDb();
        }
        return null;
    }
}
