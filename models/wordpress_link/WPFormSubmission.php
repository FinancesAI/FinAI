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
 * This is base model class for WordpressLink form submissions.
 */
abstract class WPFormSubmission extends ActiveRecord implements WPFormSubmissionInterface
{
    use WordpressLinkDbTrait;
}
