
#update offerings set isActiveFlag=0 where created='0000-00-00 00:00:00'


ALTER TABLE accounts ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE users ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE orders ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE offeringGradeLevelNodes ADD modified DATETIME NOT NULL;

ALTER TABLE gradeLevels ADD modified DATETIME NOT NULL;

ALTER TABLE schools ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE students ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE gradeSchoolNodes ADD modified DATETIME NOT NULL;

ALTER TABLE accountPurchaseNodes ADD modified DATETIME NOT NULL;

ALTER TABLE days ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE purchases ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE offerings ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL;

ALTER TABLE offeringDayNodes ADD modified DATETIME NOT NULL;

ALTER TABLE purchaseOrderNodes ADD modified DATETIME NOT NULL;

ALTER TABLE offeringSchoolNodes ADD modified DATETIME NOT NULL;

ALTER TABLE meals ADD helixId VARCHAR(36) DEFAULT NULL,
     ADD auditInfo MEDIUMTEXT DEFAULT NULL,
     ADD modified DATETIME NOT NULL 