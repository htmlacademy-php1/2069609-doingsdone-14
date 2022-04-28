CREATE DATABASE doingsdone;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(255) NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  password CHAR(64) NOT NULL
);

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  user_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id)
);
CREATE UNIQUE INDEX index_project_user ON projects (name, user_id);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  date_of_create TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  status INTEGER DEFAULT 0,
  name VARCHAR(255) NOT NULL,
  link_to_file VARCHAR(255),
  due_date DATE NOT NULL,
  user_id INT NOT NULL,
  project_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users (id),
  FOREIGN KEY (project_id) REFERENCES projects (id)
);
ALTER TABLE tasks
  MODIFY COLUMN due_date DATE;

ALTER TABLE tasks
 ADD FULLTEXT task_search(name);















