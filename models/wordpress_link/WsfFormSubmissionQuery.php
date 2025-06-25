<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\models\wordpress_link;

use yii\db\ActiveQuery;

class WsfFormSubmissionQuery extends ActiveQuery
{
    public static function find(): WsfFormSubmissionQuery
    {
        return new WsfFormSubmissionQuery(static::class);
    }

    public function init(): void
    {
        parent::init();
        $this->alias('fs');
        $this->select('fs.*');
    }

    /**
     * @inheritdoc
     * @return WsfFormSubmission[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return WsfFormSubmission|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
