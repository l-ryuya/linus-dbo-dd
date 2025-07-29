-- ===================================================================
-- 【組織階層：organization_level】
-- ・以下の組織階層を設定する。
--   1. プラットフォーム運営（PLATFORM）
--   2. テナント（TENANT）
--   3. 部署・顧客（DEPTCUST）
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.organization_level
(id, organization_level_code, organization_level_name,
 created_user_code, created_biz_date,
 updated_user_code, updated_biz_date, updated_date_time,
 version)
VALUES
(1, 'PLATFORM', 'プラットフォーム運営',
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date, CURRENT_TIMESTAMP,
 1),
(2, 'TENANT', 'テナント',
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date, CURRENT_TIMESTAMP,
 1),
(3, 'DEPTCUST', '部署・顧客',
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date, CURRENT_TIMESTAMP,
 1);

COMMIT;
