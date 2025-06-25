<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\models\wordpress_link;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "wp_wsf_submit".
 *
 * @property int $id
 * @property int $form_id
 * @property string $date_added
 * @property string $date_updated
 * @property string|null $date_expire
 * @property int $user_id
 * @property string $hash
 * @property string $actions
 * @property string $section_repeatable
 * @property int $count_submit
 * @property int $duration
 * @property string $status
 * @property int $preview
 * @property int|null $spam_level
 * @property int $starred
 * @property int $viewed
 * @property int $encrypted
 * @property string $token
 * @property int $token_validated\
 *
 * @property-read string $formName
 * @property-read WsfForm $form {@see WsfFormSubmission::getForm()}
 */
class WsfFormSubmission extends WPFormSubmission
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'wp_wsf_submit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [
                [
                    'form_id',
                    'user_id',
                    'hash',
                    'actions',
                    'section_repeatable',
                    'count_submit',
                    'duration',
                    'status',
                    'preview',
                    'token'
                ],
                'required'
            ],
            [
                [
                    'form_id',
                    'user_id',
                    'count_submit',
                    'duration',
                    'preview',
                    'spam_level',
                    'starred',
                    'viewed',
                    'encrypted',
                    'token_validated'
                ],
                'integer'
            ],
            [['date_added', 'date_updated', 'date_expire'], 'safe'],
            [['actions', 'section_repeatable'], 'string'],
            [['hash', 'token'], 'string', 'max' => 32],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'form_id' => 'Form ID',
            'date_added' => 'Date Added',
            'date_updated' => 'Date Updated',
            'date_expire' => 'Date Expire',
            'user_id' => 'User ID',
            'hash' => 'Hash',
            'actions' => 'Actions',
            'section_repeatable' => 'Section Repeatable',
            'count_submit' => 'Count Submit',
            'duration' => 'Duration',
            'status' => 'Status',
            'preview' => 'Preview',
            'spam_level' => 'Spam Level',
            'starred' => 'Starred',
            'viewed' => 'Viewed',
            'encrypted' => 'Encrypted',
            'token' => 'Token',
            'token_validated' => 'Token Validated',
        ];
    }

    /**
     * @inheritdoc
     * @return WsfFormSubmissionQuery the active query used by this AR class.
     */
    public static function find(): WsfFormSubmissionQuery
    {
        return new WsfFormSubmissionQuery(static::class);
    }

    /**
     * @return ActiveQuery
     */
    public function getForm(): ActiveQuery
    {
        return $this->hasOne(WsfForm::class, ['id' => 'form_id']);
    }

    public function getFormName(): string
    {
        return $this->form->getName();
    }

    public function getIsSynced(): bool
    {
        return (int) $this->starred === 1;
    }
}
