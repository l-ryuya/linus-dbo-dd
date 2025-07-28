-- 権限割り当て
drop table if exists m5.access_assign cascade;

create table m5.access_assign (
  user_group_id bigint not null
  , function_id bigint not null
  , created_user_code character varying(12)
  , created_date_time timestamp default current_timestamp
  , created_biz_date date
  , constraint access_assign_primary_key primary key (user_group_id,function_id)
) ;

-- APIファンクション割り当て
drop table if exists m5.api_function_assign cascade;

create table m5.api_function_assign (
  api_function_id bigint not null
  , function_id bigint not null
  , created_user_code character varying(12)
  , created_date_time timestamp default current_timestamp
  , created_biz_date date
  , constraint api_function_assign_primary_key primary key (api_function_id,function_id)
) ;

-- トークン拒否リスト
drop table if exists m5.deny_list cascade;

create table m5.deny_list (
  id bigint default nextval('m5.deny_list_id_seq') not null
  , token character varying(512) not null
  , ref_date date not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint deny_list_primary_key primary key (id)
) ;

-- ログイン状態
drop table if exists m5.login_status cascade;

create table m5.login_status (
  id bigint default nextval('m5.login_status_id_seq') not null
  , sys_user_code character varying(12) not null
  , token_type character varying(10) not null
  , token character varying(512) not null
  , start_date_time timestamp not null
  , end_date_time timestamp
  , constraint login_status_primary_key primary key (id)
) ;

-- ワンタイムパスワード設定
drop table if exists m5.one_time_passwd_setting cascade;

create table m5.one_time_passwd_setting (
  id bigint default nextval('m5.one_time_passwd_setting_id_seq') not null
  , otp_id bigint not null
  , attempt bigint not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint one_time_passwd_setting_primary_key primary key (id)
) ;

-- 組織管理
drop table if exists m5.organization_mng cascade;

create table m5.organization_mng (
  id bigint default nextval('m5.organization_mng_id_seq') not null
  , target_domain character varying(63) not null
  , organization_level_id bigint
  , read_ope_direction character varying(30)
  , write_ope_direction character varying(30)
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint organization_mng_primary_key primary key (id)
) ;

alter table m5.organization_mng add constraint organization_management_unique1
  unique (target_domain) ;

-- パスワード
drop table if exists m5.passwd cascade;

create table m5.passwd (
  id bigint default nextval('m5.passwd_id_seq') not null
  , user_id bigint not null
  , passwd character varying(64) not null
  , hash_function character varying(10) not null
  , start_date date not null
  , end_date date
  , activated character(1) not null
  , initial_flag boolean default 'FALSE'
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint passwd_primary_key primary key (id)
) ;

-- ポートフォリオ割り当て
drop table if exists m5.portfolio_assign cascade;

create table m5.portfolio_assign (
  id bigint default nextval('m5.portfolio_assign_id_seq') not null
  , portfolio_id bigint not null
  , user_group_id bigint not null
  , permission character varying(3) not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint portfolio_assign_primary_key primary key (id)
) ;

create unique index portfolio_assign_unique1
  on m5.portfolio_assign(portfolio_id,user_group_id);

-- 役職権限割り当て
drop table if exists m5.role_access_assign cascade;

create table m5.role_access_assign (
  role_organization_id bigint not null
  , function_id bigint not null
  , created_user_code character varying(12)
  , created_date_time timestamp default current_timestamp
  , created_biz_date date
  , constraint role_access_assign_primary_key primary key (role_organization_id,function_id)
) ;

-- 役職割り当て
drop table if exists m5.role_assign cascade;

create table m5.role_assign (
  user_id bigint not null
  , role_organization_id bigint not null
  , created_user_code character varying(12)
  , created_date_time timestamp default current_timestamp
  , created_biz_date date
  , constraint role_assign_primary_key primary key (user_id,role_organization_id)
) ;

-- 役職組織
drop table if exists m5.role_organization cascade;

create table m5.role_organization (
  id bigint default nextval('m5.role_organization_id_seq')
  , role_id bigint not null
  , organization_id bigint not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint role_organization_primary_key primary key (id)
) ;

alter table m5.role_organization add constraint role_organization_unique1
  unique (role_id,organization_id) ;

-- サービス組織
drop table if exists m5.service_organization cascade;

create table m5.service_organization (
  id bigint default nextval('m5.service_organization_id_seq') not null
  , service_id bigint not null
  , organization_id bigint not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint service_organization_primary_key primary key (id)
) ;

alter table m5.service_organization add constraint service_organization_unique1
  unique (service_id,organization_id) ;

-- 権限移譲
drop table if exists m5.transfered_authority cascade;

create table m5.transfered_authority (
  id bigint default nextval('m5.transfered_authority_id_seq') not null
  , authority_type character varying(20) not null
  , authority_holder character varying(20) not null
  , authority_holder_code character varying(20) not null
  , from_user character varying(12) not null
  , to_user character varying(12) not null
  , start_date timestamp not null
  , planned_expire_date timestamp not null
  , expired_date timestamp
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint transfered_authority_primary_key primary key (id)
) ;

-- ユーザ割り当て
drop table if exists m5.user_assign cascade;

create table m5.user_assign (
  user_id bigint not null
  , user_group_id bigint not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , constraint user_assign_primary_key primary key (user_id,user_group_id)
) ;

-- ユーザグループ
drop table if exists m5.user_group cascade;

create table m5.user_group (
  id bigint default nextval('m5.user_group_id_seq') not null
  , user_group_code character varying(30) not null
  , organization_id bigint not null
  , user_group_name character varying(50) not null
  , short_name character varying(20) not null
  , mail_address character varying(256)
  , description character varying(256)
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint user_group_primary_key primary key (id)
) ;

alter table m5.user_group add constraint user_group_unique1
  unique (user_group_code) ;

-- APIファンクション
drop table if exists m5.api_function cascade;

create table m5.api_function (
  id bigint default nextval('m5.api_function_id_seq') not null
  , system_code character varying(30) not null
  , resource_code character varying(50) not null
  , http_method character varying(25) not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint api_function_primary_key primary key (id)
) ;

alter table m5.api_function add constraint api_function_unique1
  unique (system_code,resource_code,http_method) ;

-- ファンクション
drop table if exists m5.functions cascade;

create table m5.functions (
  id bigint default nextval('m5.functions_id_seq') not null
  , function_code character varying(30) not null
  , service_id bigint not null
  , function_type character varying(15) not null
  , function_name character varying(100) not null
  , short_name character varying(60) not null
  , parent_function_id bigint
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint functions_primary_key primary key (id)
) ;

alter table m5.functions add constraint functions_unique1
  unique (function_code) ;

-- ワンタイムパスワード
drop table if exists m5.one_time_passwd cascade;

create table m5.one_time_passwd (
  id bigint default nextval('m5.one_time_passwd_id_seq') not null
  , user_id bigint not null
  , use_case character varying(50) not null
  , otp character varying(6) not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint one_time_passwd_primary_key primary key (id)
) ;

-- ポートフォリオ
drop table if exists m5.portfolio cascade;

create table m5.portfolio (
  id bigint default nextval('m5.portfolio_id_seq') not null
  , portfolio_code character varying(30) not null
  , organization_id bigint not null
  , short_name character varying(10) not null
  , portfolio_name character varying(50) not null
  , description character varying(256)
  , sort_order integer not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint portfolio_primary_key primary key (id)
) ;

alter table m5.portfolio add constraint portfolio_unique1
  unique (portfolio_code,organization_id) ;

-- 役職
drop table if exists m5.role cascade;

create table m5.role (
  id bigint not null
  , role_code character varying(30) not null
  , role_name character varying(30) not null
  , organization_id bigint not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint role_primary_key primary key (id)
) ;

-- サービス
drop table if exists m5.service cascade;

create table m5.service (
  id bigint default nextval('m5.service_id_seq') not null
  , service_code character varying(30) not null
  , service_name character varying(50) not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint service_primary_key primary key (id)
) ;

alter table m5.service add constraint service_unique1
  unique (service_code) ;

-- ユーザ
drop table if exists m5.users cascade;

create table m5.users (
  id bigint default nextval('m5.users_id_seq') not null
  , sys_user_code character varying(12) not null
  , organization_id bigint not null
  , emp_code character varying(30) not null
  , user_name character varying(50) not null
  , mail_address character varying(256) not null
  , login_attempt bigint default 0 not null
  , activated character(1) not null
  , lang character(2) not null
  , login_date_time timestamp
  , passwd_update_date date not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint users_primary_key primary key (id)
) ;

alter table m5.users add constraint users_unique2
  unique (emp_code,organization_id) ;

alter table m5.users add constraint users_unique4
  unique (sys_user_code) ;

-- 組織
drop table if exists m5.organization cascade;

create table m5.organization (
  id bigint default nextval('m5.organization_id_seq') not null
  , sys_organization_code character varying(12) not null
  , organization_code character varying(30) not null
  , organization_name character varying(50) not null
  , short_name character varying(30) not null
  , organization_level_id bigint not null
  , parent_organization_id bigint
  , passwd_validity_period bigint
  , failed_login_attempt bigint
  , passwd_nonreusable_period bigint
  , password_complexity character varying(500)
  , accounting_month character varying(9)
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint organization_primary_key primary key (id)
) ;

alter table m5.organization add constraint organization_unique1
  unique (organization_code,parent_organization_id) ;

alter table m5.organization add constraint organization_unique2
  unique (id,organization_code) ;

alter table m5.organization add constraint organization_unique3
  unique (sys_organization_code) ;

-- 組織階層
drop table if exists m5.organization_level cascade;

create table m5.organization_level (
  id bigint default nextval('m5.organization_level_id_seq') not null
  , organization_level_code character varying(30) not null
  , organization_level_name character varying(50) not null
  , additional_info jsonb
  , deleted boolean default 'FALSE' not null
  , created_user_code character varying(12) not null
  , created_date_time timestamp default current_timestamp not null
  , created_biz_date date not null
  , updated_user_code character varying(12) not null
  , updated_date_time timestamp default current_timestamp not null
  , updated_biz_date date not null
  , version bigint not null
  , constraint organization_level_primary_key primary key (id)
) ;

alter table m5.organization_level add constraint organization_level_unique1
  unique (organization_level_code) ;

alter table m5.access_assign
  add constraint access_assign_foreign_key1 foreign key (user_group_id) references m5.user_group(id)
  on update cascade;

alter table m5.access_assign
  add constraint access_assign_foreign_key2 foreign key (function_id) references m5.functions(id)
  on update cascade;

alter table m5.api_function_assign
  add constraint api_function_assign_foreign_key1 foreign key (function_id) references m5.functions(id)
  on update cascade;

alter table m5.api_function_assign
  add constraint api_function_assign_foreign_key2 foreign key (api_function_id) references m5.api_function(id)
  on update cascade;

alter table m5.functions
  add constraint functions_foreign_key1 foreign key (service_id) references m5.service(id)
  on update cascade;

alter table m5.functions
  add constraint functions_foreign_key2 foreign key (parent_function_id) references m5.functions(id)
  on update cascade;

alter table m5.one_time_passwd
  add constraint one_time_passwd_foreign_key1 foreign key (user_id) references m5.users(id)
  on update cascade;

alter table m5.one_time_passwd_setting
  add constraint one_time_passwd_setting_foreign_key1 foreign key (otp_id) references m5.one_time_passwd(id)
  on update cascade;

alter table m5.organization
  add constraint organization_foreign_key1 foreign key (parent_organization_id) references m5.organization(id)
  on update cascade;

alter table m5.organization
  add constraint organization_foreign_key2 foreign key (organization_level_id) references m5.organization_level(id)
  on update cascade;

alter table m5.organization_mng
  add constraint organization_mng_foreign_key1 foreign key (organization_level_id) references m5.organization_level(id)
  on update cascade;

alter table m5.passwd
  add constraint passwd_foreign_key1 foreign key (user_id) references m5.users(id)
  on update cascade;

alter table m5.portfolio
  add constraint portfolio_foreign_key1 foreign key (organization_id) references m5.organization(id)
  on update cascade;

alter table m5.portfolio_assign
  add constraint portfolio_assign_foreign_key1 foreign key (portfolio_id) references m5.portfolio(id)
  on update cascade;

alter table m5.portfolio_assign
  add constraint portfolio_assign_foreign_key2 foreign key (user_group_id) references m5.user_group(id)
  on update cascade;

alter table m5.role
  add constraint role_foreign_key1 foreign key (organization_id) references m5.organization(id)
  on update cascade;

alter table m5.role_access_assign
  add constraint role_access_assign_foreign_key1 foreign key (function_id) references m5.functions(id)
  on update cascade;

alter table m5.role_access_assign
  add constraint role_access_assign_foreign_key2 foreign key (role_organization_id) references m5.role_organization(id)
  on update cascade;

alter table m5.role_assign
  add constraint role_assign_foreign_key1 foreign key (user_id) references m5.users(id)
  on update cascade;

alter table m5.role_assign
  add constraint role_assign_foreign_key2 foreign key (role_organization_id) references m5.role_organization(id)
  on update cascade;

alter table m5.role_organization
  add constraint role_organization_foreign_key1 foreign key (organization_id) references m5.organization(id)
  on update cascade;

alter table m5.role_organization
  add constraint role_organization_foreign_key2 foreign key (role_id) references m5.role(id)
  on update cascade;

alter table m5.service_organization
  add constraint service_organization_foreign_key1 foreign key (service_id) references m5.service(id)
  on update cascade;

alter table m5.service_organization
  add constraint service_organization_foreign_key2 foreign key (organization_id) references m5.organization(id)
  on update cascade;

alter table m5.user_assign
  add constraint user_assign_foreign_key1 foreign key (user_id) references m5.users(id)
  on update cascade;

alter table m5.user_assign
  add constraint user_assign_foreign_key2 foreign key (user_group_id) references m5.user_group(id)
  on update cascade;

alter table m5.user_group
  add constraint user_group_foreign_key1 foreign key (organization_id) references m5.organization(id)
  on update cascade;

alter table m5.users
  add constraint users_foreign_key1 foreign key (organization_id) references m5.organization(id)
  on update cascade;

comment on table m5.access_assign is '権限割り当て:ユーザグループと該当ユーザグループが利用可能なファンクションの紐づけを管理するテーブル';
comment on column m5.access_assign.user_group_id is 'ユーザグループID:ユーザグループID';
comment on column m5.access_assign.function_id is 'ファンクションID:ファンクションID';
comment on column m5.access_assign.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.access_assign.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.access_assign.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';

comment on table m5.api_function_assign is 'APIファンクション割り当て:APIファンクションと該当するファンクションの紐づけを管理するテーブル';
comment on column m5.api_function_assign.api_function_id is 'APIファンクションID:APIファンクションID';
comment on column m5.api_function_assign.function_id is 'ファンクションID:ファンクションID';
comment on column m5.api_function_assign.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.api_function_assign.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.api_function_assign.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';

comment on table m5.deny_list is 'トークン拒否リスト:トークン拒否リストを管理する';
comment on column m5.deny_list.id is 'ID:ID';
comment on column m5.deny_list.token is 'トークン:token値';
comment on column m5.deny_list.ref_date is '基準日';
comment on column m5.deny_list.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.deny_list.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.deny_list.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.deny_list.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.deny_list.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.deny_list.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.deny_list.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.deny_list.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.deny_list.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.login_status is 'ログイン状態:ユーザのログイン状態を管理する';
comment on column m5.login_status.id is 'ID:ID';
comment on column m5.login_status.sys_user_code is 'システムユーザコード:システムユーザコード';
comment on column m5.login_status.token_type is 'トークン種類:token種類';
comment on column m5.login_status.token is 'トークン:token値';
comment on column m5.login_status.start_date_time is '開始日時:開始日時';
comment on column m5.login_status.end_date_time is '終了日時:終了日時';

comment on table m5.one_time_passwd_setting is 'ワンタイムパスワード設定:ワンタイムパスワードの属性情報を管理する';
comment on column m5.one_time_passwd_setting.id is 'ID:ID';
comment on column m5.one_time_passwd_setting.otp_id is 'ワンタイムパスワードID';
comment on column m5.one_time_passwd_setting.attempt is '検証試行回数';
comment on column m5.one_time_passwd_setting.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.one_time_passwd_setting.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.one_time_passwd_setting.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.one_time_passwd_setting.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.one_time_passwd_setting.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.one_time_passwd_setting.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.one_time_passwd_setting.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.one_time_passwd_setting.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.one_time_passwd_setting.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.organization_mng is '組織管理:どのテーブルをどの組織階層で管理するかを保持するテーブル';
comment on column m5.organization_mng.id is 'ID:ID';
comment on column m5.organization_mng.target_domain is '対象ドメイン:対象テーブル';
comment on column m5.organization_mng.organization_level_id is '組織階層ID:組織階層id';
comment on column m5.organization_mng.read_ope_direction is '参照権限バリデーション:各組織が参照可能組織を登録する。UPPER,LOWER,ALL_LEVEL,OWN_ONLYのいずれかが入る。';
comment on column m5.organization_mng.write_ope_direction is '更新権限バリデーション:各組織が操作可能組織を登録する。UPPER,LOWER,ALL_LEVEL,OWN_ONLYのいずれかが入る。';
comment on column m5.organization_mng.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.organization_mng.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.organization_mng.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.organization_mng.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.organization_mng.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.organization_mng.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.organization_mng.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.organization_mng.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.organization_mng.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.passwd is 'パスワード:ユーザのパスワードを管理する';
comment on column m5.passwd.id is 'ID:ID';
comment on column m5.passwd.user_id is 'ユーザID';
comment on column m5.passwd.passwd is 'パスワード:ユーザのパスワードのハッシュ値が格納される。';
comment on column m5.passwd.hash_function is 'ハッシュ関数:パスワードハッシュ化に利用するハッシュ関数';
comment on column m5.passwd.start_date is '利用開始日付';
comment on column m5.passwd.end_date is '利用終了日付';
comment on column m5.passwd.activated is '利用不可区分:A : 利用可能、D : 利用不可
1ユーザに対して利用不可区分がAは1レコードのみである';
comment on column m5.passwd.initial_flag is '初期パスワードフラグ:初期パスワードの場合はtrue';
comment on column m5.passwd.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.passwd.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.passwd.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.passwd.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.passwd.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.passwd.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.passwd.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.passwd.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.passwd.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.portfolio_assign is 'ポートフォリオ割り当て:ユーザグループに紐づくポートフォリオの参照区分を管理する。';
comment on column m5.portfolio_assign.id is 'ID:ID';
comment on column m5.portfolio_assign.portfolio_id is 'ポートフォリオID:ポートフォリオID';
comment on column m5.portfolio_assign.user_group_id is 'ユーザグループID:ユーザグループID';
comment on column m5.portfolio_assign.permission is 'パーミッション:-:NONE
r:READABLE
w:WRITABLE
x:EXECUTABLE

rw:READABLE and WRITABLE';
comment on column m5.portfolio_assign.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.portfolio_assign.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.portfolio_assign.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.portfolio_assign.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.portfolio_assign.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.portfolio_assign.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.portfolio_assign.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.portfolio_assign.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.portfolio_assign.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.role_access_assign is '役職権限割り当て:役職権限割り当てを管理するテーブル';
comment on column m5.role_access_assign.role_organization_id is '役職組織ID';
comment on column m5.role_access_assign.function_id is 'ファンクションID:ファンクションID';
comment on column m5.role_access_assign.created_user_code is 'レコード作成ユーザーコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.role_access_assign.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.role_access_assign.created_biz_date is 'レコード作成業務日時:システム系項目。レコードを作成した業務日付';

comment on table m5.role_assign is '役職割り当て:役職権限割り当て先を管理するテーブル';
comment on column m5.role_assign.user_id is 'ユーザーID:ユーザーID';
comment on column m5.role_assign.role_organization_id is '役職組織ID:役職組織ID';
comment on column m5.role_assign.created_user_code is 'レコード作成ユーザーコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.role_assign.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.role_assign.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';

comment on table m5.role_organization is '役職組織:組織に対して利用可能サービスを割り当てるテーブル';
comment on column m5.role_organization.id is 'ID';
comment on column m5.role_organization.role_id is '役職ID:サービスID';
comment on column m5.role_organization.organization_id is '組織ID:組織ID';
comment on column m5.role_organization.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.role_organization.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.role_organization.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.role_organization.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.role_organization.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.role_organization.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.role_organization.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.role_organization.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.role_organization.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.service_organization is 'サービス組織:組織に対して利用可能サービスを割り当てるテーブル';
comment on column m5.service_organization.id is 'ID';
comment on column m5.service_organization.service_id is 'サービスID:サービスID';
comment on column m5.service_organization.organization_id is '組織ID:組織ID';
comment on column m5.service_organization.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.service_organization.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.service_organization.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.service_organization.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.service_organization.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.service_organization.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.service_organization.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.service_organization.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.service_organization.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.transfered_authority is '権限移譲:権限移譲を管理するテーブル';
comment on column m5.transfered_authority.id is 'ID:ID';
comment on column m5.transfered_authority.authority_type is '権限種類:4つの権限種類(Menu,Screen item,Date,Work Flow)が入る';
comment on column m5.transfered_authority.authority_holder is '権限保持者:Jsonで権限を管理するマスタの情報(''ROLE''or''USER_GROUP'')と、対応するコード(役職コードorユーザーグループコード)が入る';
comment on column m5.transfered_authority.authority_holder_code is '権限保持者コード:権限を移譲される側のユーザーID';
comment on column m5.transfered_authority.from_user is '移譲元ユーザコード:権限を移譲する側のユーザーID';
comment on column m5.transfered_authority.to_user is '移譲先ユーザコード:権限を移譲される側のユーザーID';
comment on column m5.transfered_authority.start_date is '移譲開始日時:権限移譲の開始日';
comment on column m5.transfered_authority.planned_expire_date is '移譲予定終了日時:権限移譲の有効期限';
comment on column m5.transfered_authority.expired_date is '失効日時:権限移譲の終了日(強制終了された場合)';
comment on column m5.transfered_authority.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.transfered_authority.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.transfered_authority.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.transfered_authority.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.transfered_authority.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.transfered_authority.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.transfered_authority.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.transfered_authority.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.transfered_authority.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.user_assign is 'ユーザ割り当て:ユーザとユーザグループの紐づけを管理する';
comment on column m5.user_assign.user_id is 'ユーザID:ユーザID';
comment on column m5.user_assign.user_group_id is 'ユーザグループID:ユーザグループID';
comment on column m5.user_assign.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.user_assign.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.user_assign.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';

comment on table m5.user_group is 'ユーザグループ:ユーザグループを管理する';
comment on column m5.user_group.id is 'ID:ID';
comment on column m5.user_group.user_group_code is 'ユーザグループID:ユーザグループコード';
comment on column m5.user_group.organization_id is '組織ID:組織ID';
comment on column m5.user_group.user_group_name is 'ユーザグループ名称:ユーザグループ名称';
comment on column m5.user_group.short_name is 'ユーザグループ略称:ユーザグループ略称';
comment on column m5.user_group.mail_address is 'メールアドレス:メールアドレス';
comment on column m5.user_group.description is '詳細:詳細';
comment on column m5.user_group.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.user_group.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.user_group.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.user_group.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.user_group.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.user_group.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.user_group.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.user_group.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.user_group.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.api_function is 'APIファンクション:APIハンドラとファンクションを管理する。';
comment on column m5.api_function.id is 'ID:ID';
comment on column m5.api_function.system_code is 'システムコード';
comment on column m5.api_function.resource_code is 'リソースコード';
comment on column m5.api_function.http_method is 'HTTP メソッド';
comment on column m5.api_function.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.api_function.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.api_function.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.api_function.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.api_function.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.api_function.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.api_function.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.api_function.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.api_function.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.functions is 'ファンクション:権限設定が必要な画面表示、画面アクションを管理するテーブル';
comment on column m5.functions.id is 'ID:ID';
comment on column m5.functions.function_code is 'ファンクションコード:ファンクションコード';
comment on column m5.functions.service_id is 'サービスID:サービスマスタID';
comment on column m5.functions.function_type is 'ファンクションタイプ:MENU:メニュー
SCREEN:画面
BUTTON:ボタン
REPORT:帳票
API:API単体';
comment on column m5.functions.function_name is 'ファンクション名称:ファンクション名称';
comment on column m5.functions.short_name is 'ファンクション略称:ファンクション略称';
comment on column m5.functions.parent_function_id is '親ファンクションID:成約入力ファンクションは、制約入力画面ファンクションに紐づくといったような木構造を示すためのカラム。';
comment on column m5.functions.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.functions.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.functions.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.functions.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.functions.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.functions.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.functions.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.functions.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.functions.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.one_time_passwd is 'ワンタイムパスワード:二要素認証などに利用するワンタイムパスワードを管理する';
comment on column m5.one_time_passwd.id is 'ID:ID';
comment on column m5.one_time_passwd.user_id is 'ユーザID';
comment on column m5.one_time_passwd.use_case is 'ユースケース:ユースケース（例: パスワードリセット）';
comment on column m5.one_time_passwd.otp is 'ワンタイムパスワード:数値6桁のコード';
comment on column m5.one_time_passwd.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.one_time_passwd.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.one_time_passwd.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.one_time_passwd.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.one_time_passwd.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.one_time_passwd.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.one_time_passwd.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.one_time_passwd.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.one_time_passwd.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.portfolio is 'ポートフォリオ:組織情報のポートフォリオ情報を管理する';
comment on column m5.portfolio.id is 'ID:ID';
comment on column m5.portfolio.portfolio_code is 'ポートフォリオコード:ポートフォリオコード';
comment on column m5.portfolio.organization_id is '組織ID:組織ID';
comment on column m5.portfolio.short_name is 'ポートフォリオ略称:ポートフォリオ略称';
comment on column m5.portfolio.portfolio_name is 'ポートフォリオ名称:ポートフォリオ名称';
comment on column m5.portfolio.description is '詳細:詳細';
comment on column m5.portfolio.sort_order is 'ソートオーダ:ソート順';
comment on column m5.portfolio.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.portfolio.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.portfolio.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.portfolio.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.portfolio.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.portfolio.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.portfolio.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.portfolio.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.portfolio.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.role is '役職:役職を管理するテーブル';
comment on column m5.role.id is 'ID';
comment on column m5.role.role_code is '役職コード:役職コード';
comment on column m5.role.role_name is '役職名称:役職名称';
comment on column m5.role.organization_id is '組織ID:組織ID';
comment on column m5.role.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.role.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.role.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.role.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.role.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.role.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.role.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.role.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.role.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.service is 'サービス:本製品で取り扱うサービスを管理する。（例：共通、会計、マーケット、与信、債券、為替、コモディティ、金利、資金、株式など）';
comment on column m5.service.id is 'ID:ID';
comment on column m5.service.service_code is 'サービスコード:サービスコード';
comment on column m5.service.service_name is 'サービス名称:サービス名称';
comment on column m5.service.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.service.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.service.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.service.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.service.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.service.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.service.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.service.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.service.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.users is 'ユーザ:組織のユーザを管理する';
comment on column m5.users.id is 'ID:ID';
comment on column m5.users.sys_user_code is 'システムユーザコード:システム内でユーザを一意に特定するコード
USR + サロゲートキー（12桁になるようにゼロ埋め）';
comment on column m5.users.organization_id is '組織ID:組織ID';
comment on column m5.users.emp_code is '社員コード:社員コード';
comment on column m5.users.user_name is 'ユーザ名称:ユーザ名称';
comment on column m5.users.mail_address is 'メールアドレス:ユーザのメールアドレス';
comment on column m5.users.login_attempt is 'ログイン試行回数:ログインミス回数。ログインに成功した場合はゼロクリア。';
comment on column m5.users.activated is '利用不可区分:意図的に該当ユーザをシステム利用不可にする場合にロックするためのフラグ
A : 利用可能、D : 利用不可';
comment on column m5.users.lang is '言語:言語切り替えを行うためのロケール';
comment on column m5.users.login_date_time is 'ログイン日時:ログイン日時ログイン中の場合、当項目に認証成功日時を保管';
comment on column m5.users.passwd_update_date is 'パスワード最終更新日:パスワードの変更を最後に行った日';
comment on column m5.users.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.users.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.users.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.users.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.users.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.users.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.users.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.users.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.users.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.organization is '組織:組織を管理するテーブル';
comment on column m5.organization.id is 'ID:ID';
comment on column m5.organization.sys_organization_code is 'システム組織コード:システム内で組織を一意に特定するコード
ORG + サロゲートキー（12桁になるようにゼロ埋め）';
comment on column m5.organization.organization_code is '組織コード:組織コード';
comment on column m5.organization.organization_name is '組織名称:組織名称';
comment on column m5.organization.short_name is '組織略称:組織略称';
comment on column m5.organization.organization_level_id is '組織階層ID:組織階層ID';
comment on column m5.organization.parent_organization_id is '親組織ID:自身の親組織のIDを保持する。';
comment on column m5.organization.passwd_validity_period is 'パスワード有効期間:パスワード有効期間(日数)';
comment on column m5.organization.failed_login_attempt is 'ログイン失敗許容回数:本回数を超えてログインを失敗するとアカウントをロックする';
comment on column m5.organization.passwd_nonreusable_period is 'パスワード再利用禁止期間:同一パスワードの再利用禁止期間';
comment on column m5.organization.password_complexity is 'パスワード複雑性:パスワード設定ルールを示す正規表現';
comment on column m5.organization.accounting_month is '決済月:会計年度末決済月（MARCH、DECEMBER等）';
comment on column m5.organization.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.organization.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.organization.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.organization.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.organization.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.organization.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.organization.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.organization.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.organization.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';

comment on table m5.organization_level is '組織階層:組織階層（会社グループ、会社、ブランチ、部など）を管理するテーブル';
comment on column m5.organization_level.id is 'ID:ID';
comment on column m5.organization_level.organization_level_code is '組織階層コード:組織の階層コード';
comment on column m5.organization_level.organization_level_name is '組織階層名称:組織の階層名称';
comment on column m5.organization_level.additional_info is '付帯情報:個社別情報を保持するカラム。JSONB型';
comment on column m5.organization_level.deleted is '削除フラグ:削除済みかどうかを表す。TRUE : 削除済み、FALSE : 未削除';
comment on column m5.organization_level.created_user_code is 'レコード作成ユーザコード:システム系項目。レコードを作成したユーザのサロゲートキー';
comment on column m5.organization_level.created_date_time is 'レコード作成日時:システム系項目。レコードを作成した日時';
comment on column m5.organization_level.created_biz_date is 'レコード作成業務日付:システム系項目。レコードを作成した業務日付';
comment on column m5.organization_level.updated_user_code is 'レコード最終更新ユーザコード:システム系項目。レコードを更新したユーザのサロゲートキー';
comment on column m5.organization_level.updated_date_time is 'レコード最終更新日時:システム系項目。レコードを更新した日時';
comment on column m5.organization_level.updated_biz_date is 'レコード最終更新業務日付:システム系項目。レコードを更新した業務日付';
comment on column m5.organization_level.version is 'バージョン番号:排他制御用カラム。Update時にインクリメントする';
