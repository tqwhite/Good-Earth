select 
sch.name as 'school',
gl.title as 'grade level',
s.firstName, s.lastName, 

of.name as 'offering', 

d.title as 'day', p.chargeTotal as 'chargeTotal',

 of.refId as 'offering ID',s.refId as 'student ID',sch.refId as 'school ID'

from purchases as p
left join purchaseOrderNodes as po on po.purchaseRefId=p.refId
left join orders as o on o.refId=po.orderRefId
left join offerings as of on of.refId=o.offeringRefId
left join days as d on d.refId=o.dayRefId
left join students as s on s.refId=o.studentRefId
left join gradeLevels as gl on gl.refId=s.gradeLevelRefId
left join schools as sch on sch.refId=s.schoolRefId
order by o.created desc;
