CREATE DATABASE `mysql_php` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_ja_0900_as_cs_ks */ /*!80016 DEFAULT ENCRYPTION='N' */;

-- mysql_php.customers definition

CREATE TABLE `customers` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '会員ID',
  `name` varchar(255) NOT NULL COMMENT '会員名',
  `name_kana` varchar(255) NOT NULL COMMENT 'フリガナ',
  `gender` enum('male','female') NOT NULL COMMENT '性別',
  `uuid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '会員識別番号',
  `status` enum('temporary','member') NOT NULL COMMENT '会員ステータス',
  `email` varchar(255) NOT NULL COMMENT 'メールアドレス',
  `phone_number` varchar(13) NOT NULL COMMENT '電話番号',
  `password` varchar(255) NOT NULL COMMENT 'パスワード',
  `zipcode` char(7) NOT NULL COMMENT '郵便番号',
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci COMMENT '住所',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新日時',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_UN` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='会員';


-- mysql_php.salons definition

CREATE TABLE `salons` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT 'サロンID',
  `name` varchar(255) NOT NULL COMMENT 'サロン名',
  `description` text NOT NULL COMMENT 'サロン詳細',
  `zipcode` char(7) NOT NULL COMMENT '郵便番号',
  `address` text NOT NULL COMMENT '住所',
  `phone_number` varchar(13) NOT NULL COMMENT '電話番号',
  `start_time` time NOT NULL COMMENT '営業開始時間',
  `closing_time` time NOT NULL COMMENT '営業終了時間',
  `holiday` set('0','1','2','3','4','5','6') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT '定休日',
  `payment_methods` set('Cash','Visa','Mastercard','JCB','American Express','PayPay','LINE Pay','交通IC','iD','Edy','WAON','nanaco') NOT NULL COMMENT '支払い方法',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新日時',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='サロン';


-- mysql_php.menus definition

CREATE TABLE `menus` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT 'メニューID',
  `salon_id` bigint NOT NULL COMMENT 'サロンID',
  `name` varchar(255) NOT NULL COMMENT 'メニュー名',
  `description` text NOT NULL COMMENT 'メニュー詳細',
  `operation_time` int unsigned NOT NULL COMMENT '施術時間',
  `deadline time` time NOT NULL COMMENT '予約期限時間【時】',
  `amount` int unsigned NOT NULL DEFAULT '0' COMMENT '金額【税込】',
  `is_coupon` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'クーポン区分',
  `conditions` enum('anyone','female','male') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'anyone' COMMENT '限定条件',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新日時',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`id`),
  KEY `menus_FK` (`salon_id`),
  CONSTRAINT `menus_FK` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='サロン';


-- mysql_php.stylists definition

CREATE TABLE `stylists` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT 'スタイリストID',
  `salon_id` bigint NOT NULL COMMENT '所属サロンID',
  `name` varchar(255) NOT NULL COMMENT 'スタイリスト名',
  `name_kana` varchar(255) NOT NULL COMMENT 'フリガナ',
  `gender` enum('male','female') NOT NULL COMMENT '性別',
  `appoint_fee` int unsigned DEFAULT NULL COMMENT '指名料(税込)',
  `stylist_history` int unsigned DEFAULT NULL COMMENT 'スタイリスト歴',
  `skill` text COMMENT '得意な技術',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新日時',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`id`),
  KEY `stylists_FK` (`salon_id`),
  CONSTRAINT `stylists_FK` FOREIGN KEY (`salon_id`) REFERENCES `salons` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='スタイリスト';


-- mysql_php.reservations definition

CREATE TABLE `reservations` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '予約ID',
  `customer_id` bigint NOT NULL COMMENT '所属サロンID',
  `menu_id` bigint NOT NULL COMMENT 'メニューID',
  `stylist_id` bigint DEFAULT NULL COMMENT 'スタイリストID',
  `is_first` tinyint(1) NOT NULL DEFAULT '0' COMMENT '初回予約',
  `total_amount` int unsigned NOT NULL DEFAULT '0' COMMENT '合計金額(税込)',
  `visit_at` timestamp NOT NULL COMMENT '来店日時',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新日時',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '削除日時',
  PRIMARY KEY (`id`),
  KEY `reservations_FK` (`customer_id`),
  KEY `reservations_FK_1` (`menu_id`),
  KEY `reservations_FK_2` (`stylist_id`),
  CONSTRAINT `reservations_FK` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  CONSTRAINT `reservations_FK_1` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`),
  CONSTRAINT `reservations_FK_2` FOREIGN KEY (`stylist_id`) REFERENCES `stylists` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='予約';