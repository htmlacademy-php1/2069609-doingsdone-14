CREATE DATABASE doingsdone;
CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_of_create TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status TINYINT DEFAULT 0,
  name_of_task CHAR(50),
  link_to_file CHAR(255),
  due_date DATE,
  user_id INT,
  project_id INT
);

CREATE TABLE projects
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_of_project CHAR(50),
  user_id INT
);

CREATE TABLE users
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(128) NOT NULL UNIQUE,
  username CHAR(128),
  password CHAR(64)
);
CREATE INDEX index_email ON users (email);
