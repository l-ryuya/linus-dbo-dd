-- ===================================================================
-- 【組織：organization】
-- ・以下の組織を設定する。
--   デジタルファイナンスプラットフォーム管理法人：株式会社電通総研
--     ├ テナント①：株式会社電通総研（DBO運営部署）
--     │   ├ Securate運営部署
--     │   ├ UNVEIL運営部署
--     │   └ 電通総研顧客
--     ├ テナント②：株式会社電通（DBO運営部署）
--     │   ├ サービス①運営部署
--     │   ├ サービス②運営部署
--     │   └ 電通顧客
--     └ テナント③：株式会社電通デジタル（DBO運営部署）
--         ├ サービス①運営部署
--         ├ サービス②運営部署
--         └ 電通デジタル顧客
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

------------------------------------------------------------
-- デジタルファイナンスプラットフォーム管理法人：株式会社電通総研
------------------------------------------------------------
INSERT INTO m5.organization (
      id, organization_code, organization_name, organization_level_id, parent_organization_id,
      passwd_validity_period, failed_login_attempt,
      sys_organization_code, short_name,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time, version
) VALUES
( 1 , 'SYSORG_00000001' , 'デジタルファイナンスプラットフォーム' , 1 , NULL ,
 90 , 10 ,
 'ORG00000001' , 'DFP' ,
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP ,
 1 );

------------------------------------------------------------
--  テナント①：株式会社電通総研（DBO運営部署）
--    ├ Securate運営部署
--    ├ UNVEIL運営部署
--    └ 電通総研顧客
------------------------------------------------------------
INSERT INTO m5.organization (
      id, organization_code, organization_name, organization_level_id, parent_organization_id,
      sys_organization_code, short_name,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time, version
) VALUES
( 10 , 'SYSORG_00000010' , '株式会社電通総研'    , 2 , 1   ,
 'ORG00000010' , 'DS'      ,
  'SYS' , current_setting('my.bizdate')::date ,
  'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
( 11 , 'SYSORG_00000015' , 'Securate運営部署' , 3 , 10  ,
 'ORG00000015' , 'DS-SEC' ,
  'SYS' , current_setting('my.bizdate')::date ,
  'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
( 12 , 'SYSORG_00000016' , 'UNVEIL運営部署'   , 3 , 10  ,
 'ORG00000016' , 'DS-UNV' ,
  'SYS' , current_setting('my.bizdate')::date ,
  'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
( 21 , 'SYSORG_00000021' , '電通総研顧客'       , 3 , 10  ,
 'ORG00000021' , 'DS-CUST',
  'SYS' , current_setting('my.bizdate')::date ,
  'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 );

------------------------------------------------------------
-- テナント②：株式会社電通（DBO運営部署）
--   ├ サービス①運営部署
--   ├ サービス②運営部署
--   └ 電通顧客
------------------------------------------------------------
INSERT INTO m5.organization (
      id, organization_code, organization_name, organization_level_id, parent_organization_id,
      sys_organization_code, short_name,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time, version
) VALUES
(100 , 'SYSORG_00000100' , '株式会社電通'      , 2 , 1   , 
 'ORG00000100' , 'DI'      ,
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
(101 , 'SYSORG_00000102' , 'サービス①運営部署' , 3 , 100 ,
 'ORG00000102' , 'DI-S1'  ,
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
(102 , 'SYSORG_00000103' , 'サービス②運営部署' , 3 , 100 ,
 'ORG00000103' , 'DI-S2'  ,
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
(103 , 'SYSORG_00000104' , '電通顧客'          , 3 , 100 ,
 'ORG00000104' , 'DI-CUST',
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 );

------------------------------------------------------------
-- テナント③：株式会社電通デジタル（DBO運営部署）
--   ├ サービス①運営部署
--   ├ サービス②運営部署
--   └ 電通デジタル顧客
------------------------------------------------------------
INSERT INTO m5.organization (
      id, organization_code, organization_name, organization_level_id, parent_organization_id,
      sys_organization_code, short_name,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time, version
) VALUES
(110 , 'SYSORG_00000110' , '株式会社電通デジタル' , 2 , 1   ,
 'ORG00000110' , 'DD'      ,
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
(111 , 'SYSORG_00000112' , 'サービス①運営部署'   , 3 , 110 ,
 'ORG00000112' , 'DD-S1'  ,
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
(112 , 'SYSORG_00000113' , 'サービス②運営部署'   , 3 , 110 ,
 'ORG00000113' , 'DD-S2'  ,
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 ),
(113 , 'SYSORG_00000114' , '電通デジタル顧客'     , 3 , 110 ,
 'ORG00000114' , 'DD-CUST',
 'SYS' , current_setting('my.bizdate')::date ,
 'SYS' , current_setting('my.bizdate')::date , CURRENT_TIMESTAMP , 1 );

COMMIT;
