-- ===================================================================
-- 【ユーザー・ユーザーグループ割当：user_assign】
-- ・以下のユーザーにユーザーグループを割り当てる。
--   ● デジタルファイナンスプラットフォーム管理者（dfadmin@dentsusoken.com）
--     → DF_PLATFORM_ADM
--   ● 電通総研管理者（ds_admin@dentsusoken.com）
--     → DENTSU_SOKEN
--   ● Securate運営管理者（securate_admin@dentsusoken.com）
--     → DS_SECURATE
--   ● DBO管理者（dbo_admin@dentsusoken.com）
--     → DENTSU_SOKEN
--   ● DBOスタッフ（dbo_staff@dentsusoken.com）
--     → DENTSU_SOKEN
--   ● AI Agent（ai_agent@dentsusoken.com）
--     → DENTSU_SOKEN
--   ● 電通総研顧客（FINOLAB）（toshiki.tanaka@finolab.co.jp）
--     → DS_CUST
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.user_assign (
      user_id, user_group_id,
      created_user_code, created_date_time, created_biz_date
) VALUES
(1,  1 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date),
(2, 10 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date),
(3, 11 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date),
(4, 10 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date),
(5, 10 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date),
(6, 10 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date),
(7, 21 ,'SYS',CURRENT_TIMESTAMP,current_setting('my.bizdate')::date);

COMMIT;
