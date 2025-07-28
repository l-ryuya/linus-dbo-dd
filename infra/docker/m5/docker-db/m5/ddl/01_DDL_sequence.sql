-- 組織
drop sequence if exists m5.organization_id_seq;
create sequence m5.organization_id_seq START 1;

-- ユーザ
drop sequence if exists m5.users_id_seq;
create sequence m5.users_id_seq MAXVALUE 999999999 START 1;

-- ユーザグループ
drop sequence if exists m5.user_group_id_seq;
create sequence m5.user_group_id_seq START 1;

-- サービス
drop sequence if exists m5.service_id_seq;
create sequence m5.service_id_seq START 1;

-- サービス組織
drop sequence if exists m5.service_organization_id_seq;
create sequence m5.service_organization_id_seq START 1;

-- ファンクション
drop sequence if exists m5.functions_id_seq;
create sequence m5.functions_id_seq START 1;

-- APIファンクション
drop sequence if exists m5.api_function_id_seq;
create sequence m5.api_function_id_seq START 1;

-- 権限移譲
drop sequence if exists m5.transfered_authority_id_seq;
create sequence m5.transfered_authority_id_seq START 1;

-- 組織階層
drop sequence if exists m5.organization_level_id_seq;
create sequence m5.organization_level_id_seq START 1;

-- 組織管理
drop sequence if exists m5.organization_mng_id_seq;
create sequence m5.organization_mng_id_seq START 1;

-- ポートフォリオ
drop sequence if exists m5.portfolio_id_seq;
create sequence m5.portfolio_id_seq START 1;

-- ポートフォリオ割り当て
drop sequence if exists m5.portfolio_assign_id_seq;
create sequence m5.portfolio_assign_id_seq START 1;

-- 役職
drop sequence if exists m5.role_id_seq;
create sequence m5.role_id_seq START 1;

-- 役職組織
drop sequence if exists m5.role_organization_id_seq;
create sequence m5.role_organization_id_seq START 1;

-- パスワード
drop sequence if exists m5.passwd_id_seq;
create sequence m5.passwd_id_seq START 1;

-- ワンタイムパスワード
drop sequence if exists m5.one_time_passwd_id_seq;
create sequence m5.one_time_passwd_id_seq START 1;

-- ワンタイムパスワード設定
drop sequence if exists m5.one_time_passwd_setting_id_seq;
create sequence m5.one_time_passwd_setting_id_seq START 1;

-- トークン拒否
drop sequence if exists m5.deny_list_id_seq;
create sequence m5.deny_list_id_seq START 1;

-- ログイン状態
drop sequence if exists m5.login_status_id_seq;
create sequence m5.login_status_id_seq START 1;

-- コードカテゴリー
drop sequence if exists m5.code_category_id_seq;
create sequence m5.code_category_id_seq START 1;

-- コードバリュー
drop sequence if exists m5.code_value_id_seq;
create sequence m5.code_value_id_seq START 1;

-- オペレーションヒストリ
drop sequence if exists m5.operation_history_id_seq;
create sequence m5.operation_history_id_seq START 1;

-- オペレーションヒストリ設定
drop sequence if exists m5.operation_history_config_id_seq;
create sequence m5.operation_history_config_id_seq START 1;

-- 付帯情報設定
drop sequence if exists m5.additional_info_setting_id_seq;
create sequence m5.additional_info_setting_id_seq START 1;