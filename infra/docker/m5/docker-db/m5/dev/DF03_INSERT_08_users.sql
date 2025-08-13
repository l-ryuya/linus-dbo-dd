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

-- 1) デジタルファイナンスプラットフォーム管理者
(1,'USR00000001',  1 ,'DFP-0001','DFP管理者'    ,'df_admin@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

-- 2) 電通総研管理者
(2,'USR00000002', 10,'DS-0001','電通総研管理者'  ,'ds_admin@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

-- 3) Securate 管理者
(3,'USR00000003', 11,'DSS-0001','Securate管理者' ,'securate_admin@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

-- 4) DBO 管理者
(4,'USR00000004', 10,'DS-0002','DBO管理者' ,'dbo_admin@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

-- 5) DBO スタッフ
(5,'USR00000005', 10,'DS-0003','DBOスタッフ' ,'dbo_staff@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

-- 6) AI Agent
(6,'USR00000006', 10, 'DS-0004', 'AI Agent' ,'ai_agent@dentsusoken.com',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1),

-- 7) 電通総研顧客（FINOLAB）
(7,'USR00000007', 22, 'DSCUST-0001', 'FINOLABユーザー1' ,'toshiki.tanaka@finolab.co.jp',
 0,'A','JA',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1);

COMMIT;