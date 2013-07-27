

-- update accounts as a
-- left join accountPurchaseNodes as apn on apn.accountRefId=a.refId
-- left join purchases as p on p.refId=apn.purchaseRefId
-- set p.alreadyInHelix=NULL
-- where a.familyName in ('Tede',
-- 'Sluzky',
-- 'Holscher',
-- 'Leavitt',
-- 'Yukawa',
-- 'Briggs',
-- 'George',
-- 'McInerney',
-- 'Vaughan'
-- );

select p.alreadyInHelix, a.familyName
from accounts as a
left join accountPurchaseNodes as apn on apn.accountRefId=a.refId
left join purchases as p on p.refId=apn.purchaseRefId
where a.familyName in ('Tede',
'Sluzky',
'Holscher',
'Leavitt',
'Yukawa',
'Briggs',
'George',
'McInerney',
'Vaughan'
);