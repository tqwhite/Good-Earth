select 
#apn.purchaseRefId, apn.accountRefId, a.familyName, o.created as 'order', 
s.name,
d.title,
m.name,
eaters.firstName, eaters.lastName,
p.chargeTotal/100

from purchases as p

left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId
left join accounts as a on a.refId=apn.accountRefId

left join users as u on u.accountRefId=a.refId
#left join students as s on s.accountRefId=a.refId

left join purchaseOrderNodes as pon on pon.purchaseRefId=p.refId
left join orders as o on o.refId=pon.orderRefId

left join students as eaters on eaters.refId=o.studentRefId
left join offerings as o2 on o2.refId=o.offeringRefId

left join meals as m on m.refId=o2.mealRefId

left join offeringDayNodes as odn on odn.offeringRefId=o2.refId
left join days as d on d.refId=odn.dayRefId

left join offeringGradeLevelNodes as ogn on ogn.offeringRefId=o2.refId
left join gradeLevels as g on g.refId=ogn.gradeLevelRefId

left join offeringSchoolNodes as osn on osn.offeringRefId=o2.refId
left join schools as s on s.refId=osn.schoolRefid

order by s.name, d.title, m.name, eaters.lastName, eaters.firstName