select  
a.familyName, 
a.created as account, p.created as purchase,
p.lastFour as purch, o.created as 'order'

from accounts as a
left join accountPurchaseNodes as apn on apn.accountRefId=a.refId
left join purchases as p on p.refId=apn.purchaseRefId

left join purchaseOrderNodes as pon on pon.purchaseRefId=p.refId
left join orders as o on o.refId=pon.orderRefId

order by o.created desc limit 10