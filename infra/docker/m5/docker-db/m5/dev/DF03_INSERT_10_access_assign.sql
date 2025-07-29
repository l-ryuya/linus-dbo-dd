-- ===================================================================
-- 【ユーザーグループ・ファンクション割当：access_assign】
-- ・以下のユーザーグループにファンクションを設定する。
--    ● デジタルファイナンスプラットフォーム管理法人：DF_PLATFORM_ADM
--      ├ ● テナント①：株式会社電通総研：DENTSU_SOKEN
--      ├ ● テナント②：株式会社電通：DENTSU_INC
--      └ ● テナント③：株式会社電通デジタル：DENTSU_DIGITAL
-- 
--   ・MENU_CUSTOMERS（顧客管理）
--       ├ SCR_CUST_LISTINQ（顧客一覧照会）
--       ├ SCR_CUST_DETAILS（顧客詳細照会）
--       ├ SCR_CUST_REGIST（顧客登録）
--       └ SCR_CUST_EDIT（顧客編集）
--   ・MENU_DD（デューデリジェンス管理）
--       ├ SCR_DD_LISTINQ（デューデリジェンス一覧照会）
--       ├ SCR_DD_DETAILS（デューデリジェンス詳細照会）
--       │  └ BUTTON_DD_PRIMARY_APRV（デューデリジェンス一次承認）
--       ├ SCR_DD_RESULT_SUMMARY（デューデリジェンス結果一覧）
--       ├ SCR_DD_RESULT_ENTITY（デューデリジェンス結果法人）
--       └ SCR_DD_RESULT_INDIVIDUAL（デューデリジェンス結果個人）
--   ・MENU_DDCUST（顧客追加情報管理）
--       ├ SCR_DDCUST_LISTINQ（顧客追加情報一覧照会）
--       ├ SCR_DDCUST_DETAILS（顧客追加情報詳細照会）
--       ├ SCR_DDCUST_REGIST（顧客追加情報登録）
--       └ SCR_DDCUST_EDIT（顧客追加情報編集）
--   ・MENU_CONTRACT（契約管理）
--       ├ SCR_CONTRACT_LISTINQ（サービス契約一覧照会）
--       ├ SCR_CONTRACT_DETAILS（サービス契約詳細照会）
--       ├ SCR_CONTRACT_REGIST（サービス契約登録）
--       └ SCR_CONTRACT_EDIT（サービス契約編集）
--   ・MENU_INVOICE（請求管理）
--       ├ SCR_INVOICE_LISTINQ（請求書一覧照会）
--       ├ SCR_INVOICE_DETAILS（請求書詳細照会）
--       ├ SCR_INVOICE_SEND（請求書送信）
--       └ SCR_INVOICE_EDIT（請求書編集）
--   ・MENU_SERVICESALES（口座管理）
--       └ SCR_SERVICESALES_INQ（サービス口座照会）
--   ・MENU_USERS（ユーザー管理）
--       ├ SCR_USERS_LISTINQ（ユーザー一覧照会）
--       ├ SCR_USERS_DETAILS（ユーザー詳細照会）
--       ├ SCR_USERS_REGIST（ユーザー登録）
--       └ SCR_USERS_EDIT（ユーザー編集）
--   ・MENU_INVOICEROWS（請求明細取込ログ）
--       ├ SCR_INVOICEROWS_LISTINQ（請求明細一覧照会）
--       ├ SCR_INVOICEROWS_DETAILS（請求明細詳細照会）
--       ├ API_INVOICEROWS_REGIST（請求明細登録API）
--       └ SCR_INVOICEROWS_EDIT（請求明細編集）
--   ・MENU_AUDITLOG（監査ログ）
--       ├ SCR_AUDITLOG_LISTINQ（監査ログ一覧照会）
--       └ SCR_AUDITLOG_DETAILS（監査ログ詳細照会）
--
-- ・以下のユーザーグループにファンクションを設定する。
--   デジタルファイナンスプラットフォーム管理企業：株式会社電通総研
--     ├ テナント①：株式会社電通総研
--     │   ├ ● Securate運営チーム：DS_SECURATE
--     │   ├ ● UNVEIL運営チーム：DS_UNVEIL
--     ├ テナント②：株式会社電通
--     │   ├ ● サービス①運営部署：DI_SERVICE1
--     │   └ ● サービス②運営部署：DI_SERVICE2
--     └ テナント③：株式会社電通デジタル
--         ├ ● サービス①運営部署：DD_SERVICE1
--         └ ● サービス②運営部署：DD_SERVICE2
-- 
--   ・MENU_CUSTOMERS（顧客管理）
--       ├ SCR_CUST_LISTINQ（顧客一覧照会）
--       ├ SCR_CUST_DETAILS（顧客詳細照会）
--       ├ SCR_CUST_REGIST（顧客登録）
--       └ SCR_CUST_EDIT（顧客編集）
--   ・MENU_DD（デューデリジェンス管理）
--       ├ SCR_DD_LISTINQ（デューデリジェンス一覧照会）
--       ├ SCR_DD_DETAILS（デューデリジェンス詳細照会）
--       ├ SCR_DD_RESULT_SUMMARY（デューデリジェンス結果一覧）
--       ├ SCR_DD_RESULT_ENTITY（デューデリジェンス結果法人）
--       └ SCR_DD_RESULT_INDIVIDUAL（デューデリジェンス結果個人）
--   ・MENU_DDCUST（顧客追加情報管理）
--       ├ SCR_DDCUST_LISTINQ（顧客追加情報一覧照会）
--       ├ SCR_DDCUST_DETAILS（顧客追加情報詳細照会）
--       ├ SCR_DDCUST_REGIST（顧客追加情報登録）
--       └ SCR_DDCUST_EDIT（顧客追加情報編集）
--   ・MENU_CONTRACT（契約管理）
--       ├ SCR_CONTRACT_LISTINQ（サービス契約一覧照会）
--       ├ SCR_CONTRACT_DETAILS（サービス契約詳細照会）
--       ├ SCR_CONTRACT_REGIST（サービス契約登録）
--       └ SCR_CONTRACT_EDIT（サービス契約編集）
--   ・MENU_INVOICE（請求管理）
--       ├ SCR_INVOICE_LISTINQ（請求書一覧照会）
--       ├ SCR_INVOICE_DETAILS（請求書詳細照会）
--       ├ SCR_INVOICE_SEND（請求書送信）
--       └ SCR_INVOICE_EDIT（請求書編集）
--   ・MENU_SERVICESALES（口座管理）
--       └ SCR_SERVICESALES_INQ（サービス口座照会）
--   ・MENU_USERS（ユーザー管理）
--       ├ SCR_USERS_LISTINQ（ユーザー一覧照会）
--       ├ SCR_USERS_DETAILS（ユーザー詳細照会）
--       ├ SCR_USERS_REGIST（ユーザー登録）
--       └ SCR_USERS_EDIT（ユーザー編集）
--   ・MENU_INVOICEROWS（請求明細取込ログ）
--       ├ SCR_INVOICEROWS_LISTINQ（請求明細一覧照会）
--       ├ SCR_INVOICEROWS_DETAILS（請求明細詳細照会）
--       ├ API_INVOICEROWS_REGIST（請求明細登録API）
--       └ SCR_INVOICEROWS_EDIT（請求明細編集）
--   ・MENU_AUDITLOG（監査ログ）
--       ├ SCR_AUDITLOG_LISTINQ（監査ログ一覧照会）
--       └ SCR_AUDITLOG_DETAILS（監査ログ詳細照会）
-- 
-- ・以下のユーザーグループにファンクションを設定する。
--   デジタルファイナンスプラットフォーム管理企業：株式会社電通総研
--     ├ テナント①：株式会社電通総研
--     │   └ 電通総研顧客
--     ├ テナント②：株式会社電通
--     │   └ 電通顧客
--     └ テナント③：株式会社電通デジタル
--         └ 電通デジタル顧客
-- 
--   ・MENU_DDCUST（顧客追加情報管理）
--       ├ SCR_DDCUST_LISTINQ（顧客追加情報一覧照会）
--       ├ SCR_DDCUST_DETAILS（顧客追加情報詳細照会）
--       ├ SCR_DDCUST_REGIST（顧客追加情報登録）
--       └ SCR_DDCUST_EDIT（顧客追加情報編集）
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

------------------------------------------------------------
-- (A) フルアクセス 4 グループ
------------------------------------------------------------
INSERT INTO m5.access_assign (
      user_group_id, function_id,
      created_user_code, created_date_time, created_biz_date
)
SELECT g.id, f.id,
       'SYS', CURRENT_TIMESTAMP, current_setting('my.bizdate')::date
  FROM m5.user_group g
  JOIN m5.functions  f ON true            -- ← 必須
 WHERE g.user_group_code IN
 ('DF_PLATFORM_ADM','DENTSU_SOKEN','DENTSU_INC','DENTSU_DIGITAL');

------------------------------------------------------------
-- (B) 運営サービス別グループ
------------------------------------------------------------
INSERT INTO m5.access_assign (
      user_group_id, function_id,
      created_user_code, created_date_time, created_biz_date
)
SELECT g.id, f.id,
       'SYS', CURRENT_TIMESTAMP, current_setting('my.bizdate')::date
  FROM m5.user_group g
  JOIN m5.functions  f ON true
 WHERE g.user_group_code IN ('DS_SECURATE','DS_UNVEIL','DI_SERVICE1','DI_SERVICE2','DD_SERVICE1','DD_SERVICE2')
   AND f.function_code IN (
     'MENU_CUSTOMERS','SCR_CUST_LISTINQ','SCR_CUST_DETAILS','SCR_CUST_REGIST','SCR_CUST_EDIT',
     'MENU_DD','SCR_DD_LISTINQ','SCR_DD_DETAILS','SCR_DD_RESULT_SUMMARY','SCR_DD_RESULT_ENTITY','SCR_DD_RESULT_INDIVIDUAL',
     'MENU_DDCUST','SCR_DDCUST_LISTINQ','SCR_DDCUST_DETAILS','SCR_DDCUST_REGIST','SCR_DDCUST_EDIT',
     'MENU_CONTRACT','SCR_CONTRACT_LISTINQ','SCR_CONTRACT_DETAILS','SCR_CONTRACT_REGIST','SCR_CONTRACT_EDIT',
     'MENU_INVOICE','SCR_INVOICE_LISTINQ','SCR_INVOICE_DETAILS','SCR_INVOICE_SEND','SCR_INVOICE_EDIT',
     'MENU_SERVICESALES','SCR_SERVICESALES_INQ','MENU_USERS','SCR_USERS_LISTINQ','SCR_USERS_DETAILS',
     'MENU_INVOICEROWS','SCR_INVOICEROWS_LISTINQ','SCR_INVOICEROWS_DETAILS','API_INVOICEROWS_REGIST','SCR_INVOICEROWS_EDIT',
     'MENU_AUDITLOG','SCR_AUDITLOG_LISTINQ','SCR_AUDITLOG_DETAILS');

------------------------------------------------------------
-- (C) 顧客グループ（追加情報のみ）
------------------------------------------------------------
INSERT INTO m5.access_assign (
      user_group_id, function_id,
      created_user_code, created_date_time, created_biz_date
)
SELECT g.id, f.id,
       'SYS', CURRENT_TIMESTAMP, current_setting('my.bizdate')::date
  FROM m5.user_group g
  JOIN m5.functions  f ON true
 WHERE g.user_group_code IN ('DS_CUST','DI_CUST','DD_CUST')
   AND f.function_code IN
 ('MENU_DDCUST','SCR_DDCUST_LISTINQ','SCR_DDCUST_DETAILS','SCR_DDCUST_REGIST','SCR_DDCUST_EDIT');

COMMIT;
