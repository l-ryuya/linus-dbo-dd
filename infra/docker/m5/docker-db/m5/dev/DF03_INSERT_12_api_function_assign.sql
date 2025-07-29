-- ===================================================================
-- 【APIファンクション割当：api_function_assign】
-- ・以下の API ファンクションをファンクションに設定する。
--   ・API_INVOICEROWS_REGIST：https://dbo-billing.dev.dsbizdev.com/api/billing/usages
--   ・MENU_INVOICEROWS（請求明細取込ログ）
--       └ API_INVOICEROWS_REGIST（請求明細登録API）
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.api_function_assign
(api_function_id,function_id,created_user_code,created_biz_date)
VALUES (1,9003,'SYS',current_setting('my.bizdate')::date);

COMMIT;
