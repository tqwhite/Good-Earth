select 
a.created, a.familyName, p.chargeTotal/100
from purchases as p
left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId
left join accounts as a on a.refId=apn.accountRefId

#left join purchaseOrderNodes as pon on pon.purchaseRefId=p.refId
#left join orders as o on o.refId=pon.orderRefId

where p.refId='003C226B-1D5C-CAA9-CC5F-386B2069140C'
order by a.created desc limit 10