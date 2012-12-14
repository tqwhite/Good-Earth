select 
p.refId, apn.refId, p.chargeTotal,
a.familyName,
u.firstName, u.lastName,
s.firstname, s.lastName,
m.name,
d.title,
sch.name,
g.title

from purchases as p
left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId
left join accounts as a on a.refId=apn.accountRefId
left join users as u on u.accountRefId=a.refId

left join purchaseOrderNodes as pon on pon.purchaseRefId=p.refId
left join orders as o on o.refId=pon.orderRefId

left join students as s on s.refId=o.studentRefId
left join days as d on d.refId=o.dayRefId

left join offerings as of on of.refId=o.offeringRefId
left join meals as m on m.refId=of.mealRefId

left join schools as sch on sch.refId=s.schoolRefId
left join gradeLevels as g on g.refId=s.gradeLevelRefId

order by p.created desc