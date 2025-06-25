<?php

use yii\db\Migration;

class m160331_081802_initial extends Migration
{
    public function up()
    {
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `field` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `name` varchar(250) NOT NULL,
  `original` tinyint(1) DEFAULT '0',
  `enable_table` tinyint(1) DEFAULT '0',
  `enable_form` tinyint(1) DEFAULT '0',
  `role` tinyint(1) DEFAULT '0',
  `filter_type` smallint(100) DEFAULT '0',
  `sort_order` int(255) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type` (`type`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=502 ;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `loan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `person_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `term` int(11) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `source` varchar(250) DEFAULT NULL,
  `product` tinyint(1) DEFAULT '0',
  `deal_stage` tinyint(2) DEFAULT NULL,
  `refferal` varchar(250) DEFAULT NULL,
  `query_string` varchar(250) DEFAULT NULL,
  `ip_ountry` varchar(250) DEFAULT NULL,
  `received` varchar(250) DEFAULT NULL,
  `is_email` tinyint(1) DEFAULT '0',
  `is_sms` tinyint(1) DEFAULT '0',
  `is_call` tinyint(1) DEFAULT '0',
  `description` text,
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  `close_time` int(10) DEFAULT NULL,
  `waiting_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `person_id` (`person_id`),
  KEY `refferal` (`refferal`),
  KEY `amount` (`amount`),
  KEY `term` (`term`),
  KEY `status` (`status`),
  KEY `source` (`source`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` smallint(100) NOT NULL,
  `data` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL DEFAULT '0',
  `language` varchar(255) NOT NULL DEFAULT '',
  `translation` text,
  PRIMARY KEY (`id`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `surname` varchar(250) NOT NULL,
  `personal_code` varchar(250) NOT NULL,
  `income` int(11) NOT NULL,
  `outcome` int(11) NOT NULL,
  `dependants` smallint(10) DEFAULT '0',
  `phone` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `credit_history` tinyint(1) DEFAULT '0',
  `description` text,
  `loan_count` int(11) DEFAULT '0',
  `update_time` int(10) DEFAULT NULL,
  `create_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_code` (`personal_code`),
  KEY `name` (`name`),
  KEY `surname` (`surname`),
  KEY `income` (`income`),
  KEY `outcome` (`outcome`),
  KEY `dependants` (`dependants`),
  KEY `phone` (`phone`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `setting` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `value` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `source_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;")->execute();
		
		$this->db->createCommand("CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(250) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `auth` varchar(300) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) DEFAULT NULL,
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;")->execute();
		
		$this->db->createCommand("ALTER TABLE `loan`
  ADD CONSTRAINT `loan_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `person` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;")->execute();

		$this->db->createCommand("ALTER TABLE `message`
  ADD CONSTRAINT `fk_source_message_message` FOREIGN KEY (`id`) REFERENCES `source_message` (`id`) ON DELETE CASCADE;")->execute();
		
		$this->db->createCommand("INSERT INTO `field` (`id`, `type`, `name`, `original`, `enable_table`, `enable_form`, `role`, `filter_type`, `sort_order`) VALUES
(361, 1, 'name', 1, 1, 1, 0, 1, 1),
(362, 1, 'surname', 1, 1, 1, 0, 1, 2),
(363, 1, 'personal_code', 1, 1, 1, 0, 1, 3),
(364, 1, 'income', 1, 1, 1, 0, 0, 4),
(365, 1, 'outcome', 1, 1, 1, 0, 0, 5),
(366, 1, 'dependants', 1, 1, 1, 0, 2, 6),
(367, 1, 'phone', 1, 1, 1, 0, 0, 7),
(368, 1, 'email', 1, 1, 1, 0, 0, 8),
(369, 1, 'loan_count', 1, 1, 0, 0, 0, 11),
(370, 2, 'person.name', 1, 1, 1, 0, 0, 17),
(372, 2, 'person.surname', 1, 1, 1, 0, 1, 19),
(373, 2, 'person.income', 1, 1, 1, 0, 0, 23),
(374, 2, 'person.outcome', 1, 1, 1, 0, 0, 24),
(375, 2, 'person.dependants', 1, 1, 1, 0, 0, 25),
(412, 2, 'term', 1, 1, 1, 0, 0, 33),
(413, 2, 'status', 1, 1, 0, 0, 2, 35),
(414, 2, 'source', 1, 1, 0, 1, 2, 36),
(467, 1, 'credit_history', 1, 1, 0, 0, 0, 9),
(468, 1, 'description', 1, 0, 0, 0, 0, 10),
(469, 1, 'update_time', 1, 1, 0, 0, 0, 12),
(470, 2, 'person_id', 1, 0, 0, 0, 0, 31),
(471, 2, 'user_id', 1, 1, 0, 0, 0, 34),
(473, 2, 'product', 1, 1, 0, 1, 0, 37),
(474, 2, 'deal_stage', 1, 1, 0, 1, 0, 38),
(475, 2, 'refferal', 1, 1, 0, 1, 0, 39),
(476, 2, 'query_string', 1, 1, 0, 1, 0, 40),
(477, 2, 'ip_ountry', 1, 1, 0, 1, 0, 41),
(478, 2, 'received', 1, 0, 0, 0, 0, 42),
(479, 2, 'is_email', 1, 0, 0, 0, 0, 43),
(480, 2, 'is_sms', 1, 0, 0, 0, 0, 44),
(481, 2, 'is_call', 1, 0, 0, 0, 0, 45),
(482, 2, 'description', 1, 0, 0, 0, 0, 46),
(483, 2, 'create_time', 1, 1, 0, 0, 0, 47),
(484, 2, 'update_time', 1, 0, 0, 0, 0, 48),
(485, 2, 'close_time', 1, 0, 0, 0, 0, 49),
(486, 2, 'waiting_time', 1, 1, 0, 0, 0, 16),
(487, 2, 'person.personal_code', 1, 1, 1, 0, 0, 20),
(488, 2, 'person.phone', 1, 1, 1, 0, 0, 21),
(489, 2, 'person.email', 1, 1, 0, 0, 0, 22),
(490, 2, 'person.credit_history', 1, 0, 0, 0, 0, 26),
(491, 2, 'person.description', 1, 0, 0, 0, 0, 27),
(492, 2, 'person.loan_count', 1, 1, 0, 0, 0, 28),
(493, 2, 'person.update_time', 1, 0, 0, 0, 0, 29),
(494, 2, 'person.create_time', 1, 0, 0, 0, 0, 30),
(497, 1, 'create_time', 0, 1, 0, 0, 0, 14),
(500, 2, 'amount', 0, 1, 1, 0, 0, 32);")->execute();
		
		$this->db->createCommand("INSERT INTO `setting` (`id`, `name`, `value`) VALUES
(1, 'waiting_time_info', '10'),
(2, 'waiting_time_warning', '15'),
(3, 'waiting_time_danger', '20'),
(4, 'default_loan_url', '&sort=-waiting_time&LoanSearch%5Bstatus%5D=0'),
(5, 'table_class', 'table table-striped table-bordered table-hover'),
(6, 'export_role', '1'),
(7, 'page_limit', '30'),
(8, 'date_format', 'd.m.Y H:m:s'),
(11, 'language', 'en'),
(12, 'application_name', 'CRM');")->execute();
		
    }

    public function down()
    {
        echo "m160331_081802_initial cannot be reverted.\n";

        return false;
    }
}
