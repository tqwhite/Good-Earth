update accountPurchaseNodes set alreadyInHelix=1 where isnull(alreadyInHelix);
update accounts set alreadyInHelix=1 where isnull(alreadyInHelix);
update orders set alreadyInHelix=1 where isnull(alreadyInHelix);
update purchaseOrderNodes set alreadyInHelix=1 where isnull(alreadyInHelix);
update purchases set alreadyInHelix=1 where isnull(alreadyInHelix);
update students set alreadyInHelix=1 where isnull(alreadyInHelix);
update users set alreadyInHelix=1 where isnull(alreadyInHelix);
