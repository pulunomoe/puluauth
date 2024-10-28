CREATE TABLE administrators
(
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code       VARCHAR(255) NOT NULL UNIQUE,
    name       VARCHAR(255) NOT NULL UNIQUE,
    email      VARCHAR(255) NOT NULL UNIQUE,
    api_key    VARCHAR(255) NOT NULL UNIQUE,
    secret_key VARCHAR(255) NOT NULL UNIQUE
);

CREATE VIEW administrators_view AS
SELECT id,
       code,
       name,
       email
FROM administrators;
