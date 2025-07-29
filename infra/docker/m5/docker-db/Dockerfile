# ---------- Pull the Base Image From Docker Hub ---------------
FROM postgres:15-alpine as base

LABEL maintainer="li3039"

# ------------------- Arguments Configure ----------------------
ARG SCHEMA_DIR="./docker-db/sql/ddl/00_schema.sql"
ARG LOCAL_DDL_DIR="./docker-db/sql/ddl/*DDL*"
ARG CONFIG_DIR="./docker-db/sql/ddl/901_config.sql"
ARG LOCAL_DML_DIR="./docker-db/sql/dml"

# -------------------- COPY LOCAL DATA --------------------------
COPY ${SCHEMA_DIR} /docker-entrypoint-initdb.d/
COPY ${LOCAL_DDL_DIR} /docker-entrypoint-initdb.d/
COPY ${CONFIG_DIR} /docker-entrypoint-initdb.d/
COPY ${LOCAL_DML_DIR} /docker-entrypoint-initdb.d/