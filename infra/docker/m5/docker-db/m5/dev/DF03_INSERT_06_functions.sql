-- ===================================================================
-- 【サービス別ファンクション定義：functions】
-- ・以下のファンクションを設定する。
--   ・MENU_CUSTOMERS（顧客管理）
--       ├ SCR_CUST_LISTINQ（顧客一覧照会）
--       ├ SCR_CUST_DETAILS（顧客詳細照会）
--       ├ SCR_CUST_REGIST（顧客登録）
--       └ SCR_CUST_EDIT（顧客編集）
--   ・MENU_DD（デューデリジェンス管理）
--       ├ SCR_DD_LISTINQ（DD一覧照会）
--       ├ SCR_DD_CASE（DDケースサマリー）
--       │  ├ BUTTON_PREDD_APRV（DD準備承認）
--       │  ├ BUTTON_ADDINFO_APRV（追加入力承認）
--       │  └ BUTTON_DD_APRV（DD承認）
--       ├ SCR_DD_STEP_RESULT（DDステップ結果）
--       ├ SCR_DD_RELATIONS_INQ（DD関係者照会）
--       ├ SCR_DD_RELATIONS_REGIST（DD関係者登録）
--       ├ SCR_DD_RELATIONS_EDIT（DD関係者編集）
--       └ SCR_DD_RELATIONS_DEL（DD関係者削除）
--   ・MENU_DDCUST（顧客追加情報管理）
--       ├ SCR_DDCUST_LISTINQ（顧客追加情報一覧照会）
--       ├ SCR_DDCUST_DETAILS（顧客追加情報詳細照会）
--       ├ SCR_DDCUST_REGIST（顧客追加情報登録）
--       ├ SCR_DDCUST_EDIT（顧客追加情報編集）
--       └ SCR_DDCUST_DEL（顧客追加情報削除）
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
-- ・採番ルールは以下の通り。
--    1000 – 1999  : 1階層目メニュー
--    2000 – 2999  : MENU_CUSTOMERS 配下
--    3000 – 3999  : MENU_DD 配下
--    4000 – 4999  : DDCUST 配下
--    5000 – 5999  : CONTRACT 配下
--    6000 – 6999  : INVOICE 配下
--    7000 – 7999  : USERS 配下
--    8000 – 8999  : INVOICEROWS 配下
--    9000 – 9999: AUDITLOG 配下
--    ※必要に応じて拡張してください
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

/* ───── 1. メニュー（固定 ID） ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(1000,'MENU_CUSTOMERS'   ,1,'MENU','顧客'            ,'MENU_CUSTOMERS'  ,NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(1100,'MENU_DD'          ,1,'MENU','デューデリジェンス','MENU_DD'         ,NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(1200,'MENU_DDCUST'      ,1,'MENU','顧客追加情報管理'  ,'MENU_DDCUST'     ,NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(1300,'MENU_CONTRACT'    ,1,'MENU','契約'            ,'MENU_CONTRACT'   ,NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(1400,'MENU_INVOICE'     ,1,'MENU','請求書'           ,'MENU_INVOICE'    ,NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(1500,'MENU_USERS'       ,1,'MENU','ユーザー'         ,'MENU_USERS'      ,NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(1600,'MENU_INVOICEROWS' ,1,'MENU','請求明細'         ,'MENU_INVOICEROWS',NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(1700,'MENU_AUDITLOG'    ,1,'MENU','監査ログ'         ,'MENU_AUDITLOG'   ,NULL,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 2. MENU_CUSTOMERS 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(2000,'SCR_CUST_LISTINQ' ,1,'SCREEN','顧客一覧照会','SCR_CUST_LISTINQ' ,1000,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(2001,'SCR_CUST_DETAILS' ,1,'SCREEN','顧客詳細照会','SCR_CUST_DETAILS' ,1000,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(2002,'SCR_CUST_REGIST'  ,1,'SCREEN','顧客登録'   ,'SCR_CUST_REGIST'  ,1000,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(2003,'SCR_CUST_EDIT'    ,1,'SCREEN','顧客編集'   ,'SCR_CUST_EDIT'    ,1000,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 3. MENU_DD 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(3000,'SCR_DD_LISTINQ'         ,1,'SCREEN','DD一覧照会'   ,'SCR_DD_LISTINQ'         ,1100,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3001,'SCR_DD_CASE'            ,1,'SCREEN','DDケース'     ,'SCR_DD_CASE'            ,1100,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3002,'SCR_DD_STEP_RESULTS'    ,1,'SCREEN','DDステップ結果','SCR_DD_STEP_RESULT'     ,1100,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3003,'SCR_DD_RELATIONS_INQ'   ,1,'SCREEN','DD関係者照会'  ,'SCR_DD_RELATIONS_INQ'   ,1100,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3004,'SCR_DD_RELATIONS_REGIST',1,'SCREEN','DD関係者追加'  ,'SCR_DD_RELATIONS_REGIST',1100,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3005,'SCR_DD_RELATIONS_EDIT'  ,1,'SCREEN','DD関係者編集'  ,'SCR_DD_RELATIONS_EDIT'  ,1100,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3006,'SCR_DD_RELATIONS_DEL'   ,1,'SCREEN','DD関係者削除'  ,'SCR_DD_RELATIONS_DEL'   ,1100,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ─── DD 詳細配下の承認ボタン ─── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(3007,'BUTTON_PREDD_APRV'  ,1,'BUTTON','DD準備承認' ,'BUTTON_PREDD_APRV'   ,3001,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3008,'BUTTON_ADDINFO_APRV',1,'BUTTON','追加入力承認','BUTTON_ADDINFO_APRV' ,3001,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(3009,'BUTTON_DD_APRV'     ,1,'BUTTON','DD承認'     ,'BUTTON_DD_APRV'      ,3001,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 4. MENU_DDCUST 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(4000,'SCR_DDCUST_LISTINQ',1,'SCREEN','顧客追加情報一覧照会','SCR_DDCUST_LISTINQ',1200,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(4001,'SCR_DDCUST_DETAILS',1,'SCREEN','顧客追加情報詳細照会','SCR_DDCUST_DETAILS',1200,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(4002,'SCR_DDCUST_REGIST' ,1,'SCREEN','顧客追加情報登録'   ,'SCR_DDCUST_REGIST' ,1200,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(4003,'SCR_DDCUST_EDIT'   ,1,'SCREEN','顧客追加情報編集'   ,'SCR_DDCUST_EDIT'   ,1200,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(4004,'SCR_DDCUST_DEL'    ,1,'SCREEN','顧客追加情報削除'   ,'SCR_DDCUST_DEL'    ,1200,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 5. MENU_CONTRACT 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(5000,'SCR_CONTRACT_LISTINQ',1,'SCREEN','契約一覧照会','SCR_CONTRACT_LISTINQ',1300,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(5001,'SCR_CONTRACT_DETAILS',1,'SCREEN','契約詳細照会','SCR_CONTRACT_DETAILS',1300,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(5002,'SCR_CONTRACT_REGIST' ,1,'SCREEN','契約登録'   ,'SCR_CONTRACT_REGIST' ,1300,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(5003,'SCR_CONTRACT_EDIT'   ,1,'SCREEN','契約編集'   ,'SCR_CONTRACT_EDIT'   ,1300,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 6. MENU_INVOICE 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(6000,'SCR_INVOICE_LISTINQ',1,'SCREEN','請求書一覧照会','SCR_INVOICE_LISTINQ',1400,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(6001,'SCR_INVOICE_DETAILS',1,'SCREEN','請求書詳細照会','SCR_INVOICE_DETAILS',1400,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(6002,'SCR_INVOICE_SEND'   ,1,'SCREEN','請求書送信'   ,'SCR_INVOICE_SEND'   ,1400,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(6003,'SCR_INVOICE_EDIT'   ,1,'SCREEN','請求書編集'   ,'SCR_INVOICE_EDIT'   ,1400,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 7. MENU_USERS 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(7000,'SCR_USERS_LISTINQ',1,'SCREEN','ユーザー一覧照会','SCR_USERS_LISTINQ',1500,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(7001,'SCR_USERS_DETAILS',1,'SCREEN','ユーザー詳細照会','SCR_USERS_DETAILS',1500,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(7002,'SCR_USERS_REGIST' ,1,'SCREEN','ユーザー登録'   ,'SCR_USERS_REGIST' ,1500,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(7003,'SCR_USERS_EDIT'   ,1,'SCREEN','ユーザー編集'   ,'SCR_USERS_EDIT'   ,1500,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 8. MENU_INVOICEROWS 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(8000,'SCR_INVOICEROWS_LISTINQ',1,'SCREEN','請求明細一覧照会','SCR_INVOICEROWS_LISTINQ',1600,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(8001,'SCR_INVOICEROWS_DETAILS',1,'SCREEN','請求明細詳細照会','SCR_INVOICEROWS_DETAILS',1600,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(8002,'SCR_INVOICEROWS_EDIT'   ,1,'SCREEN','請求明細編集'   ,'SCR_INVOICEROWS_EDIT'   ,1600,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(8003,'API_INVOICEROWS_REGIST' ,1,'API'   ,'請求明細登録API','API_INVOICEROWS_REGIST' ,1600,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

/* ───── 9. MENU_AUDITLOG 配下 ───── */
INSERT INTO m5.functions (
      id, function_code, service_id, function_type, function_name, short_name, parent_function_id,
      created_user_code, created_biz_date,
      updated_user_code, updated_biz_date, updated_date_time,
      version
) VALUES
(9000,'SCR_AUDITLOG_LISTINQ',1,'SCREEN','監査ログ一覧照会','SCR_AUDITLOG_LISTINQ',1700,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1),
(9001,'SCR_AUDITLOG_DETAILS',1,'SCREEN','監査ログ詳細照会','SCR_AUDITLOG_DETAILS',1700,'SYS',current_setting('my.bizdate')::date,'SYS',current_setting('my.bizdate')::date,CURRENT_TIMESTAMP,1);

COMMIT;
