update accountPurchaseNodes set dirtyForHelix=1 where isnull(dirtyForHelix);
update accounts set dirtyForHelix=1 where isnull(dirtyForHelix);
update orders set dirtyForHelix=1 where isnull(dirtyForHelix);
update purchaseOrderNodes set dirtyForHelix=1 where isnull(dirtyForHelix);
update purchases set dirtyForHelix=1 where isnull(dirtyForHelix);
update students set dirtyForHelix=1 where isnull(dirtyForHelix);
update users set dirtyForHelix=1 where isnull(dirtyForHelix);
