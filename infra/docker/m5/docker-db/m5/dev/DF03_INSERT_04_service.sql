-- ===================================================================
-- 【サービス：service】
-- ・以下のサービスを設定する。
--   ● デジタルファイナンスサービス：DF
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.service (
      id, service_code, service_name,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(1,'DF','デジタルファイナンスサービス',
 'SYS',current_setting('my.bizdate')::date,
 'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,
 1);

COMMIT;
