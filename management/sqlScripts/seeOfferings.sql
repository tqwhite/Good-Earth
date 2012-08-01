use test1;
select o.name, s.name as 'school', gl.title as 'grade level', d.title as 'day', m.name as 'meal', o.created
from offerings as o
left join offeringSchoolNodes as osl on osl.offeringRefId=o.refId
left join offeringDayNodes as odl on odl.offeringRefId=o.refId
left join offeringGradeLevelNodes as ogl on ogl.offeringRefId=o.refId
left join gradeLevels as gl on gl.refId=ogl.gradeLevelRefId
left join days as d on d. refId=odl.dayRefId
left join schools as s on s.refId=osl.schoolRefId
left join meals as m on m.refId=o.mealRefId
