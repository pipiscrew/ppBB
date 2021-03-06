CREATE TABLE "categories" ( "cat_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "cat_parent_id" INTEGER DEFAULT 0, "cat_name" TEXT NOT NULL, "cat_last_update" TEXT, "cat_private" INTEGER, "cat_order" INTEGER DEFAULT 0 )

CREATE TABLE "replies" ( "reply_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "topic_id" INTEGER, "reply_body" TEXT NOT NULL, "reply_dateupd" TEXT, "reply_user_id" INTEGER )

CREATE TABLE "topics" ( "topic_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "category_id" INTEGER, "topic_name" TEXT, "topic_views" INTEGER, "topic_daterec" TEXT )

CREATE TABLE "users" ( "user_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "user_mail" TEXT NOT NULL, "user_password" TEXT NOT NULL, "user_level" TEXT NOT NULL )

CREATE TABLE "events" ( "event_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "date_start" TEXT, "date_end" TEXT, "event_type" INTEGER, "event_description" TEXT, "date_rec" TEXT )