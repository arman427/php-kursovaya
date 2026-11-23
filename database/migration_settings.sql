USE pc_builder;

ALTER TABLE users
    ADD COLUMN full_name VARCHAR(120) NOT NULL DEFAULT '' AFTER password_hash,
    ADD COLUMN phone VARCHAR(40) NOT NULL DEFAULT '' AFTER full_name,
    ADD COLUMN address VARCHAR(255) NOT NULL DEFAULT '' AFTER phone;
