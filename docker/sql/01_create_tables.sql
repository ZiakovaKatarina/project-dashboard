DROP TABLE IF EXISTS users_in_projects;
DROP TABLE IF EXISTS users_in_tasks;
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS attachments;
DROP TABLE IF EXISTS tasks;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id BIGINT AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(250) NOT NULL UNIQUE,
    password VARCHAR(250) NOT NULL,
    admin BOOLEAN NOT NULL,
    PRIMARY KEY (user_id)
);

CREATE TABLE projects (
    project_id BIGINT AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(500),
    status CHAR(1) NOT NULL,
    deadline DATE,
    submission DATE,
    PRIMARY KEY (project_id)
);

CREATE TABLE tasks (
    task_id BIGINT AUTO_INCREMENT,
    project_id BIGINT,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(500),
    status CHAR(1) NOT NULL,
    deadline DATE,
    submission DATE,
    priority INT NOT NULL,
    PRIMARY KEY (task_id),
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
);

CREATE TABLE attachments (
    attachment_id BIGINT AUTO_INCREMENT,
    task_id BIGINT,
    path VARCHAR(100) NOT NULL,
    filename VARCHAR(50) NOT NULL,
    PRIMARY KEY (attachment_id),
    FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE
);

CREATE TABLE comments (
    comment_id BIGINT AUTO_INCREMENT,
    user_id BIGINT,
    task_id BIGINT,
    content VARCHAR(500) NOT NULL,
    creation DATETIME NOT NULL,
    PRIMARY KEY (comment_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE
);

CREATE TABLE users_in_projects (
    user_in_project_id BIGINT AUTO_INCREMENT,
    user_id BIGINT,
    project_id BIGINT,
    rights CHAR(1) NOT NULL,
    PRIMARY KEY (user_in_project_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE,
    UNIQUE (user_id, project_id)
);

CREATE TABLE users_in_tasks (
    user_in_task_id BIGINT AUTO_INCREMENT,
    user_id BIGINT,
    task_id BIGINT,
    state DECIMAL(5, 2) NOT NULL,
    PRIMARY KEY (user_in_task_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE,
    UNIQUE (user_id, task_id)
);