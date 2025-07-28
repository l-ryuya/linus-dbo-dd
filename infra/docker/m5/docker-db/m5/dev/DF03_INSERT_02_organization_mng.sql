-- ===================================================================
-- 【組織管理：organization_mng】
-- ・以下の組織階層に対して管理範囲を設定する。
--    1. CALENDAR：
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.organization_mng (
     target_domain, organization_level_id,
     read_ope_direction, write_ope_direction,
     created_user_code,  created_date_time,  created_biz_date,
     updated_user_code,  updated_date_time,  updated_biz_date,
     version
) VALUES
/* ───────────── PLATFORM (level_id = 1) ───────────── */
('CALENDAR',1,
 '','',
 'SYS',current_timestamp,current_setting('my.bizdate')::date,
 'SYS',current_timestamp,current_setting('my.bizdate')::date,1);

COMMIT;
