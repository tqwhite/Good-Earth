select s.firstName, s.lastName, of.name, d.title, p.amountTendered as 'amount', gl.title as 'grade level', o.* 
from purchases as p
left join purchaseOrderNodes as po on po.purchaseRefId=p.refId
left join orders as o on o.refId=po.orderRefId
left join offerings as of on of.refId=o.offeringRefId
left join days as d on d.refId=o.dayRefId
left join students as s on s.refId=o.studentRefId
left join gradeLevels as gl on gl.refId=s.gradeLevelRefId