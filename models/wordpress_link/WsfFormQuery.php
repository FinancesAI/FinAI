<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\models\wordpress_link;

use yii\db\ActiveQuery;

class WsfFormQuery extends ActiveQuery
{
    public static function find(): WsfFormQuery
    {
        return new WsfFormQuery(static::class);
    }

    public function init(): void
    {
        parent::init();
        $this->alias('f');
        $this->select('f.*');
    }

    /**
     * @inheritdoc
     * @return WsfForm[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return WsfForm|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
