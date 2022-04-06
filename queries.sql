INSERT INTO users (email, name, password) VALUES ('vasya@mail.ru', 'Вася', 'secret');
INSERT INTO users (email, name, password) VALUES ('anna@mail.ru', 'Анютка', 'qwerty');
INSERT INTO users (email, name, password) VALUES ('dasha@mail.ru', 'Дарья1988', '765ggg');

INSERT INTO projects (name, user_id) VALUES ('Входящие', '2');
INSERT INTO projects (name, user_id) VALUES ('Учеба', '3');
INSERT INTO projects (name, user_id) VALUES ('Домашние дела', '1');
INSERT INTO projects (name, user_id) VALUES ('Авто', '3');
INSERT INTO projects (name, user_id) VALUES ('Работа', '2');

INSERT INTO tasks (status, due_date, user_id, project_id, name) VALUES ('1', '2022-04-06', '2', '5', 'Собеседование в IT компании');
INSERT INTO tasks (status, due_date, user_id, project_id, name) VALUES ('0', '2022-04-15', '2', '5', 'Выполнить тестовое задание');
INSERT INTO tasks (status, due_date, user_id, project_id, name) VALUES ('1', '2022-05-06', '3', '2', 'Сделать задание первого раздела');
INSERT INTO tasks (status, due_date, user_id, project_id, name) VALUES ('0', '2022-06-06', '2', '1', 'Встреча с другом');
INSERT INTO tasks (status, due_date, user_id, project_id, name) VALUES ('0', '2022-03-06', '1', '3', 'Купить корм для кота');
INSERT INTO tasks (status, due_date, user_id, project_id, name) VALUES ('0', '2022-07-26', '1', '3', 'Заказать пиццу');
INSERT INTO tasks (status, due_date, user_id, project_id, name) VALUES ('0', '2022-06-01', '3', '4', 'Помыть машину');


-- обновить название задачи по её идентификатору.
UPDATE tasks SET name = 'Заказать пиццу и колу' WHERE id = 6;

-- пометить задачу как выполненную;
UPDATE tasks SET status = '1' WHERE id = 7;

-- получить список из всех задач для одного проекта;
SELECT name FROM tasks WHERE project_id=5;

-- получить список из всех проектов для одного пользователя;
SELECT name FROM projects WHERE user_id=3;
