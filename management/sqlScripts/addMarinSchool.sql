INSERT INTO schools (refId, name, created, suppressDisplay)
values
('11', 'Marin School', '2013-03-26', '1');

-- select * from schools where refId='11';
-- 
-- delete from schools where refId='11';

INSERT INTO gradeSchoolNodes (refId, created, schoolRefId, gradeLevelRefId)
values
('84', '2013-03-26', '11', '9'),
('85', '2013-03-26','11', '10'),
('86', '2013-03-26','11', '11'),
('87', '2013-03-26','11', '12');



update schools set suppressDisplay=1 where refId=11;


delete from gradeSchoolNodes
where refId in (
'84',
'85',
'86',
'87'
);