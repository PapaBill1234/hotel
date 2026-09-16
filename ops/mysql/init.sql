-- Phase 2: create the Laravel-owned schema only.
-- PolarIS / hotel tables live in holodb (often named `phpretro` on XAMPP).
-- Never CREATE/ALTER/DROP objects in holodb from this file.

CREATE DATABASE IF NOT EXISTS hotel_cms_sys
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
