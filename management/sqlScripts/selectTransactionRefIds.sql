drop table xDeleteTable if exists;

create table xDeleteTable 

select a.refId as accountRefId, 
u.refId as userRefId,
s.refId as studentRefId,
o.refId as orderRefId,
pon.refId as purchaseOrderNodeRefId,
p.refId as purchaseRefId,
apn.refId as accountPurchaseNodeRefId

from      accounts as a
left join users as u on u.accountRefId=a.refId
left join students as s on s.accountRefId=a.refId
left join orders as o on o.studentRefId=s.refId
left join purchaseOrderNodes as pon on pon.orderRefId=o.refId
left join purchases as p on p.refId=pon.purchaseRefId
left join accountPurchaseNodes as apn on apn.accountRefId=a.refId

where familyName='crilly'
or familyName like 'white ii';



set foreign_key_checks=0;

delete from students where refId in (select studentRefId from xDeleteTable);

delete from purchases where refId in (select purchaseRefId from xDeleteTable);
delete from orders where refId in (select orderRefId from xDeleteTable);
delete from purchaseOrderNodes where refId in (select purchaseOrderNodeRefId from xDeleteTable);
delete from users where refId in (select userRefId from xDeleteTable);
delete from accountPurchaseNodes where refId in (select accountPurchaseNodeRefId from xDeleteTable);
delete from accounts where refId in (select accountRefId from xDeleteTable);

set foreign_key_checks=1;