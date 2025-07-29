-- ===================================================================
-- 【ユーザー：users】
-- ・以下の組織に所属するユーザー（●）を設定する。
--   デジタルファイナンスプラットフォーム管理企業：株式会社電通総研
--     ├ ● デジタルファイナンスプラットフォーム管理者（df_admin@dentsusoken.com）
--     └ テナント①：株式会社電通総研
--         ├ ● 電通総研管理者（ds_admin@dentsusoken.com）
--         └ Securate運営チーム
--             └ ● Securate運営管理者（securate_admin@dentsusoken.com）
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.users (
      id, sys_user_code, organization_id, emp_code, user_name, mail_address,
      login_attempt, activated, lang, passwd_update_date,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(1,'USR00000001',  1 ,'DFP-0001','DFP管理者'    ,'df_admin@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

(2,'USR00000002', 10,'DS-0001','電通総研管理者'  ,'ds_admin@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

(3,'USR00000003', 11,'DSC-0001','Securate管理者' ,'securate_admin@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1);

COMMIT;
