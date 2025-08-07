-- ===================================================================
-- 【ロール組織：role_organization】
-- ・以下の組織に管理者ロールを設定する。（●：ロール組織）
-- 
--   ● デジタルファイナンスプラットフォーム管理企業：株式会社電通総研
--     ├ デジタルファイナンスプラットフォーム管理者
--     └ ● テナント①：株式会社電通総研
--         ├ 電通総研管理者
--         ├ DBO管理者
--         └ ● Securate運営チーム
--             └ Securate運営管理者
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.role_organization (
      id, role_id, organization_id,
      created_user_code, created_date_time, created_biz_date,
      updated_user_code, updated_date_time, updated_biz_date,
      version
) VALUES
(1001, 1,  1 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,
              'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,1),

(1002, 2, 10,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,
              'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,1),

(1003, 3, 11,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,
              'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date,1);

COMMIT;
