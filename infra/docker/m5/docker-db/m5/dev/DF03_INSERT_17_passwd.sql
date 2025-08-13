-- ===================================================================
-- 【パスワード：passwd】
-- ・以下のユーザーにパスワードを設定する。
--   ● デジタルファイナンスプラットフォーム管理者（dfadmin@dentsusoken.com）
--   ● 電通総研管理者（ds_admin@dentsusoken.com）
--   ● Securate運営管理者（securate_admin@dentsusoken.com）
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.passwd (
      user_id,            -- 紐づく m5.users.id
      passwd,             -- 初期パスワード（平文 or ハッシュ）
      hash_function,      -- 'PLAINTEXT', 'SHA256' など
      start_date,
      activated,
      initial_flag,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date,
      version
) VALUES

-- 1) デジタルファイナンスプラットフォーム管理者
(1, 'e53d6ae94bf7713a891358ae48bc90dfc61944bc6b4660aed658b2cb7a79a243', 'SHA-256',
 current_setting('my.bizdate')::date,
 'A', TRUE,
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date,
 1),

-- 2) 電通総研管理者
(2, 'e53d6ae94bf7713a891358ae48bc90dfc61944bc6b4660aed658b2cb7a79a243', 'SHA-256',
 current_setting('my.bizdate')::date,
 'A', TRUE,
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date,
 1),

-- 3) Securate 管理者
(3, 'e53d6ae94bf7713a891358ae48bc90dfc61944bc6b4660aed658b2cb7a79a243', 'SHA-256',
 current_setting('my.bizdate')::date,
 'A', TRUE,
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date,
 1),

-- 4) DBO 管理者
(4, 'e53d6ae94bf7713a891358ae48bc90dfc61944bc6b4660aed658b2cb7a79a243', 'SHA-256',
 current_setting('my.bizdate')::date,
 'A', TRUE,
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date,
 1),

-- 5) DBO スタッフ
(5, 'e53d6ae94bf7713a891358ae48bc90dfc61944bc6b4660aed658b2cb7a79a243', 'SHA-256',
 current_setting('my.bizdate')::date,
 'A', TRUE,
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date,
 1),

-- 6) AI Agent
(6, 'e53d6ae94bf7713a891358ae48bc90dfc61944bc6b4660aed658b2cb7a79a243', 'SHA-256',
 current_setting('my.bizdate')::date,
 'A', TRUE,
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date,
 1),

-- 7) 電通総研顧客（FINOLAB）
(7, 'e53d6ae94bf7713a891358ae48bc90dfc61944bc6b4660aed658b2cb7a79a243', 'SHA-256',
 current_setting('my.bizdate')::date,
 'A', TRUE,
 'SYS', current_setting('my.bizdate')::date,
 'SYS', current_setting('my.bizdate')::date,
 1);


COMMIT;
