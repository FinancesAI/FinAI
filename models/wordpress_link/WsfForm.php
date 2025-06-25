<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\models\wordpress_link;

use app\components\WordpressLink;
use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "wp_wsf_form".
 *
 * @property int $id
 * @property int $user_id
 * @property string $label
 * @property string $date_added
 * @property string $date_updated
 * @property string|null $date_publish
 * @property string $status
 * @property int $count_stat_view
 * @property int $count_stat_save
 * @property int $count_stat_submit
 * @property int $count_submit
 * @property int $count_submit_unread
 * @property string $published
 * @property string $published_checksum
 * @property string $checksum
 * @property string $version
 */
class WsfForm extends WPForm
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'wp_wsf_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['user_id', 'label', 'published'], 'required'],
            [['user_id', 'count_stat_view', 'count_stat_save', 'count_stat_submit', 'count_submit', 'count_submit_unread'], 'integer'],
            [['date_added', 'date_updated', 'date_publish'], 'safe'],
            [['published'], 'string'],
            [['label'], 'string', 'max' => 1024],
            [['status'], 'string', 'max' => 16],
            [['published_checksum', 'checksum', 'version'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'label' => 'Label',
            'date_added' => 'Date Added',
            'date_updated' => 'Date Updated',
            'date_publish' => 'Date Publish',
            'status' => 'Status',
            'count_stat_view' => 'Count Stat View',
            'count_stat_save' => 'Count Stat Save',
            'count_stat_submit' => 'Count Stat Submit',
            'count_submit' => 'Count Submit',
            'count_submit_unread' => 'Count Submit Unread',
            'published' => 'Published',
            'published_checksum' => 'Published Checksum',
            'checksum' => 'Checksum',
            'version' => 'Version',
        ];
    }

    /**
     * @inheritdoc
     * @return WsfFormQuery the active query used by this AR class.
     */
    public static function find(): WsfFormQuery
    {
        return new WsfFormQuery(static::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->label;
    }

    public function getIsPublished(): bool
    {
        return $this->status === 'publish';
    }

    public function getIsActive(): bool
    {
        /**
         * @var WordpressLink $wp
         */
        $wp = Yii::$app->get('wordpressLink');
        return in_array($this->getId(), $wp->forms, true);
    }

    public function getStatusName(): string
    {
        return Inflector::humanize($this->status);
    }
}
