select 
#apn.purchaseRefId, apn.accountRefId, a.familyName, o.created as 'order', 
eaters.lastName, eaters.firstName, a.familyName, p.created, 
(select count(*) from students where accountRefId=eaters.accountRefId and firstName=eaters.firstName) as count
, a.refId

from accounts as a

left join students as eaters on eaters.accountRefId=a.refId
left join users as u on u.accountRefId=a.refId

left join gradeLevels as gl on gl.refId=eaters.gradeLevelRefId
left join schools as s on s.refId=eaters.schoolRefId

left join orders as o on o.studentRefId=eaters.refId
left join offerings as of on of.refId=o.offeringRefId
left join meals as m on m.refId=of.mealRefId

left join offeringDayNodes as odn on odn.offeringRefId=of.refId
left join days as d on d.refId=odn.dayRefId

left join purchaseOrderNodes as pon on pon.orderRefId=o.refId
left join purchases as p on p.refId=pon.purchaseRefId

left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId

where not isnull(p.refId)
and (select count(*) from students where accountRefId=eaters.accountRefId and firstName=eaters.firstName and isActiveFlag=true)>1
and p.refId>'2014-07-01'


group by eaters. firstName, eaters.refId, p.refId

order by a.familyName, eaters.firstName, p.created
limit 100000