-- ===================================================================
-- 【ロール割当：role_assign】
-- ・以下のユーザーにロールを割り当てる。
--   ● デジタルファイナンスプラットフォーム管理者（dfadmin@dentsusoken.com）
--     → デジタルファイナンスプラットフォーム管理者
--   ● 電通総研管理者（ds_admin@dentsusoken.com）
--     → 電通総研管理者
--   ● Securate管理者（securate_admin@dentsusoken.com）
--     → Securate管理者
--   ● DBO管理者（dbo_admin@dentsusoken.com）
--     → DBO管理者
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.role_assign
(user_id,role_organization_id,created_user_code,created_biz_date) VALUES
(1,1001,'SYS',current_setting('my.bizdate')::date),
(2,1002,'SYS',current_setting('my.bizdate')::date),
(3,1003,'SYS',current_setting('my.bizdate')::date),
(4,1002,'SYS',current_setting('my.bizdate')::date);

COMMIT;
