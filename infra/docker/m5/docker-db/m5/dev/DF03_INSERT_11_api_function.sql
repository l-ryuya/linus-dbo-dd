-- ===================================================================
-- 【APIファンクション：api_function】
-- ・以下の API ファンクションを設定する。
--   ・API_INVOICEROWS_REGIST：https://dbo-billing.dev.dsbizdev.com/api/billing/usages
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.api_function (
      id, system_code, resource_code, http_method,
      created_user_code, created_date_time, created_biz_date,
      updated_user_code, updated_date_time, updated_biz_date,
      version
) VALUES
(1,'BILLING','/api/billing/usages','POST',
 'SYS', CURRENT_TIMESTAMP,              current_setting('my.bizdate')::date,
 'SYS', CURRENT_TIMESTAMP,              current_setting('my.bizdate')::date,
 1);

COMMIT;
