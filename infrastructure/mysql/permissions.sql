revoke `cloudsqlsuperuser`@`%` from 'production'@'%';
revoke `cloudsqlsuperuser`@`%` from 'staging'@'%';
revoke `cloudsqlsuperuser`@`%` from 'qa'@'%';

GRANT ALL on `legendsports-prod`.* to 'production'@'%';
GRANT ALL on `legendsports-staging`.* to 'staging'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, CREATE VIEW, CREATE ROUTINE, ALTER ROUTINE, REFERENCES ON *.* TO `qa`@`%`;
REVOKE SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, CREATE VIEW, CREATE ROUTINE, ALTER ROUTINE, REFERENCES ON `legendsports-prod`.* FROM `qa`@`%`;
REVOKE INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, CREATE VIEW, CREATE ROUTINE, ALTER ROUTINE ON `mysql`.* FROM `qa`@`%`;
REVOKE INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, CREATE VIEW, CREATE ROUTINE, ALTER ROUTINE ON `sys`.* FROM `qa`@`%`;
revoke SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES, CREATE VIEW, CREATE ROUTINE, ALTER ROUTINE, REFERENCES ON `legendsports-staging`.* from qa;