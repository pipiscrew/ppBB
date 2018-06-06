CREATE TABLE "categories" ( "cat_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "cat_name" TEXT NOT NULL, "cat_last_update" TEXT, "cat_private" INTEGER, "cat_order" INTEGER )

CREATE TABLE "replies" ( "reply_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "topic_id" INTEGER, "reply_body" TEXT NOT NULL, "reply_dateupd" TEXT, "reply_user_id" INTEGER )

CREATE TABLE "topics" ( "topic_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "category_id" INTEGER, "topic_name" TEXT, "topic_views" INTEGER, "topic_daterec" TEXT )

CREATE TABLE "users" ( "user_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, "user_mail" TEXT NOT NULL, "user_password" TEXT NOT NULL, "user_level" TEXT NOT NULL )