-- ===================================================================
-- 【ロール・ファンクション割当：role_access_assign】
-- ・以下のロールにファンクションを設定する。
--   ・デジタルバックオフィス管理者
--     → BUTTON_DD_FINAL_APRV（デューデリジェンス二次承認）
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.role_access_assign
(role_organization_id,function_id,created_user_code,created_biz_date) VALUES
(1002,(SELECT id FROM m5.functions WHERE function_code='BUTTON_DD_FINAL_APRV'),
 'SYS',current_setting('my.bizdate')::date);

COMMIT;
