-- ===================================================================
-- 【ユーザーグループ：user_group】
--  ・組織ごとにユーザーグループを設定する。
--   デジタルファイナンスプラットフォーム管理企業：株式会社電通総研（DF_PLATFORM_ADM）
--     ├ テナント①：株式会社電通総研（DBO運営部署）（DS_DBO）
--     │   ├ Securate運営チーム（DS_SECURATE）
--     │   ├ UNVEIL運営チーム（DS_UNVEIL）
--     │   └ 電通総研顧客（DS_CUST）
--     ├ テナント②：株式会社電通（DBO運営部署）（DI_DBO）
--     │   ├ サービス①運営部署（DI_SERVICE1）
--     │   ├ サービス②運営部署（DI_SERVICE2）
--     │   └ 電通顧客（DI_CUST）
--     └ テナント③：株式会社電通デジタル（DBO運営部署）（DD_DBO）
--         ├ サービス①運営部署（DD_SERVICE1）
--         ├ サービス②運営部署（DD_SERVICE2）
--         └ 電通デジタル顧客（DD_CUST）
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.user_group (
      id, user_group_code, organization_id, user_group_name, short_name,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES

------------------------------------------------------------
-- デジタルファイナンスプラットフォーム管理法人：株式会社電通総研（DF_PLATFORM_ADM）
------------------------------------------------------------
(  1,'DF_PLATFORM_ADM',     1 ,'DFP管理者'                        ,'DFP-ADM',
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),

------------------------------------------------------------
-- テナント①：株式会社電通総研（DBO運営チーム）（DS_DBO）
--   ├ Securate運営チーム（DS_SECURATE）
--   ├ UNVEIL運営チーム（DS_UNVEIL）
--   └ 電通総研顧客（DS_CUST）
------------------------------------------------------------
( 10,'DENTSU_SOKEN',       10 ,'株式会社電通総研DBO運営部署'          ,'DS' ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 11,'DS_SECURATE',        11 ,'DS Securate運営部署'               ,'DS-SEC' ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 12,'DS_UNVEIL',          12 ,'DS UNVEIL運営部署'                 ,'DS-UNV' ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 21,'DS_CUST',            21 ,'電通総研顧客'                       ,'DS-CUST',
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),

------------------------------------------------------------
-- テナント②：株式会社電通（DBO運営部署）（DI_DBO）
--   ├ サービス①運営部署（DI_SERVICE1）
--   ├ サービス②運営部署（DI_SERVICE2）
--   └ 電通顧客（DI_CUST）
------------------------------------------------------------
( 100,'DENTSU_INC',       100 ,'電通DBO運営部署'                   ,'DI' ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 101,'DI_SERVICE1',      101 ,'DI サービス①運営部署'              ,'DI-S1'  ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 102,'DI_SERVICE2',      102 ,'DI サービス②運営部署'              ,'DI-S2'  ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 103,'DI_CUST',          103 ,'電通顧客'                         ,'DI-CUST',
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),

------------------------------------------------------------
-- テナント③：株式会社電通デジタル（DBO運営部署）（DD_DBO）
--   ├ サービス①運営部署（DD_SERVICE1）
--   ├ サービス②運営部署（DD_SERVICE2）
--   └ 電通デジタル顧客（DD_CUST）
------------------------------------------------------------
( 110,'DENTSU_DIGITAL',   110 ,'電通デジタルDBO運営部署'             ,'DD' ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 111,'DD_SERVICE1',      111 ,'DD サービス①運営'                  ,'DD-S1'  ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 112,'DD_SERVICE2',      112 ,'DD サービス②運営'                  ,'DD-S2'  ,
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1),
( 113,'DD_CUST',          113 ,'電通デジタル顧客'                   ,'DD-CUST',
  'SYS',current_setting('my.bizdate')::date,
  'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
  1);

COMMIT;
