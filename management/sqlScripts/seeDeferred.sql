select * 
from purchases as p

left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId
left join accounts as a on a.refId=apn.accountRefId

left join users as u on u.accountRefId=a.refId
left join students as s on s.accountRefId=a.refId
where deferredPaymentPreference like '%def%';

select * 
from purchases as p

left join accountPurchaseNodes as apn on apn.purchaseRefId=p.refId
left join accounts as a on a.refId=apn.accountRefId

left join users as u on u.accountRefId=a.refId
#left join students as s on s.accountRefId=a.refId
where deferredPaymentPreference like '%def%';
#where isnull(fdProcessorResponseMessage);