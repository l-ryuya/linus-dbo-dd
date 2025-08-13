-- ===================================================================
-- 【ロール：role】
-- ・以下の組織に管理者ロールを設定する。（●：ロール）
-- 
--   デジタルファイナンスプラットフォーム管理企業：株式会社電通総研
--     ├ ● デジタルファイナンスプラットフォーム管理者
--     └ テナント①：株式会社電通総研
--         ├ ● 電通総研管理者
--         └ Securate運営チーム
--             └ ● Securate運営管理者
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.role (
      id, role_code, role_name, organization_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_date_time, updated_biz_date,
      version
) VALUES
(1,'DFP_ADM','DFP管理者'       ,  1 ,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,
 1),

(2,'DS_ADM' ,'電通総研管理者'   , 10 ,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,
 1),

(3,'SEC_ADM','Securate管理者'  , 11 ,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,
 1),

(4,'DBO_ADM','DBO管理者'  , 10 ,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,
 1);

COMMIT;
