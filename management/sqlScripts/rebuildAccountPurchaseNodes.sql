#insert into accountPurchaseNodes
#(accountRefId, purchaseRefId, refId, created)

select a.refId, p.refId, p.refId, p.created
from purchaseOrderNodes as pon

left join orders as o on o.refId=pon.orderRefId
left join students as s on s.refId=o.studentRefId

left join accounts as a on a.refId=s.accountRefId

left join purchases as p on p.refId=pon.purchaseRefId

left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId
where isnull(apn.purchaseRefId)

group by p.refId
