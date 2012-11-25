select * from offerings as o1

left join offeringGradeLevelNodes as ogl on ogl.offeringRefId=o1.refId
left join gradeLevels as gl on gl.refId=ogl.gradeLevelRefId

where o1.refId in 
(select o.refId
from offerings as o
left join offeringSchoolNodes as osn on osn.offeringRefId=o.refId
left join schools as s on s.refId=osn.schoolRefId

left join offeringDayNodes as odn on odn.offeringRefId=o.refId
left join days as d on d.refId=odn.dayRefId

where s.name like '%green%'
and d.refId='2'
)