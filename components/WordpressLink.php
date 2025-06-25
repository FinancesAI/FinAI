<?php
/**
 * @author Nils <deele@tuta.io>
 */

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\db\Connection;
use yii\db\Exception;

/**
 * WordpressLink component
 */
class WordpressLink extends Component
{
    /**
     * @var string Use when WP uses Contact Form 7 plugin with CFDB7 to store form submissions
     */
    public const PLUGIN_CF7 = 'cf7';
    /**
     * @var string Use when WP uses WS Forms plugin
     */
    public const PLUGIN_WSF = 'wsf';

    /**
     * @var string database connection component
     */
    public string $database = 'db';

    /**
     * @var string Plugin used by the form
     */
    public string $plugin = '';

    /**
     * @var array Form IDs to capture submissions from
     */
    public array $forms = [];

    /**
     * @var Connection|null Database connection instance
     */
    protected ?Connection $_db = null;

    /**
     * @param array $config
     *
     * @throws InvalidConfigException|NotSupportedException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        if (empty($this->plugin)) {
            throw new InvalidConfigException('Plugin is required');
        }
        if ($this->getIsPluginCF7()) {
            throw new NotSupportedException('CF7 plugin is not supported yet');
        }
        if (empty($this->forms)) {
            throw new InvalidConfigException('Forms are required');
        }
        if (empty($this->database) || Yii::$app->get($this->database, false) === null) {
            throw new InvalidConfigException('Database component is required');
        }
        Yii::$app->on(Yii::$app::EVENT_AFTER_REQUEST, function () {
            $this->disconnect();
        });
    }

    protected function connect(): bool
    {
        if ($this->_db === null) {
            try {
                $this->_db = Yii::$app->get($this->database, false);
            } catch (InvalidConfigException $e) {
                Yii::error("Database component not found: " . $e->getMessage());
                return false;
            }
            try {
                $this->_db->open();
            } catch (Exception $e) {
                Yii::error("Connection failed: " . $e->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * @return Connection instance related to WordpressLink
     */
    public function getDb(): Connection
    {
        $this->connect();
        return $this->_db;
    }

    public function getIsOnline(): bool
    {
        return $this->connect();
    }

    protected function disconnect(): bool
    {
        if ($this->_db !== null) {
            $this->_db->close();
            $this->_db = null;
            return true;
        }
        return false;
    }

    public function getIsPluginCF7(): bool
    {
        return $this->plugin === self::PLUGIN_CF7;
    }

    public function getIsPluginWSF(): bool
    {
        return $this->plugin === self::PLUGIN_WSF;
    }

    /**
     * @return array|null
     *
     * @throws InvalidConfigException
     */
    public function getSubmissions()
    {
        if ($this->getIsPluginCF7()) {
            $query = $this->_db->createCommand(
                'SELECT * FROM `wp_db7_forms` WHERE form_id IN (:ids) and is_send = 0'
            );
        } elseif ($this->getIsPluginWSF()) {
            $query = $this->_db->createCommand(
                'SELECT * FROM `wp_wsf_submit` WHERE form_id IN (:ids) and starred = 0'
            );
        } else {
            throw new InvalidConfigException('Unknown plugin');
        }
        $query->bindValue(':ids', $this->forms);
        try {
            $submissions = $query->queryAll();
        } catch (Exception $e) {
            Yii::error("Submissions query failed: " . $e->getMessage());
            return null;
        }
//        foreach ($submissions as $submission) {
//            $submission['@meta'] = [];
//            $query = $this->db->createCommand(
//                'SELECT * FROM `wp_wsf_submit_meta` WHERE parent_id = :id'
//            );
//            $query->bindValue(':id', $submission['id']);
//            try {
//                $metaFields = $query->queryAll();
//            } catch (Exception $e) {
//                echo "Submission meta query failed: " . $e->getMessage();
//                exit;
//            }
//            foreach ($metaFields as $metaField) {
//                $submission['@meta'][$metaField['meta_key']] = $metaField;
//            }
//        }

        return $submissions;
    }
}
