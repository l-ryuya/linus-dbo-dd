-- m5ユーザに接続用の権限を付与
GRANT CONNECT ON DATABASE postgres TO m5;

-- m5ユーザにSchema用の権限を付与
GRANT USAGE ON SCHEMA m5 TO m5;

-- m5ユーザにTBL用の権限を付与
GRANT ALL ON ALL TABLES IN SCHEMA m5 TO m5;
GRANT ALL ON ALL SEQUENCES IN SCHEMA m5 TO m5;

-- search_path
ALTER ROLE m5 SET search_path TO m5;