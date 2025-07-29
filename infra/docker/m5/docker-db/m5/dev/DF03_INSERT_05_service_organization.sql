-- ===================================================================
-- 【サービス・組織：service_organization】
-- ・全組織について、デジタルファイナンスサービス（DF）を使用可能とする。
-- ===================================================================

BEGIN;

SET LOCAL my.bizdate TO '2025-06-11';

INSERT INTO m5.service_organization (
      service_id, organization_id,
      created_user_code, created_date_time, created_biz_date,
      updated_user_code, updated_date_time, updated_biz_date,
      version
)
SELECT
  1,                                   -- service_id (DF)
  org.id,                              -- organization_id
  'SYS',                               -- created_user_code
  CURRENT_TIMESTAMP,                   -- created_date_time
  current_setting('my.bizdate')::date, -- created_biz_date
  'SYS',                               -- updated_user_code
  CURRENT_TIMESTAMP,                   -- updated_date_time
  current_setting('my.bizdate')::date, -- updated_biz_date
  1                                    -- version
FROM m5.organization AS org;

COMMIT;
