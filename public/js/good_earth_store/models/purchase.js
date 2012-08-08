steal('jquery/model', function(){

/**
 * @class GoodEarthStore.Models.Purchase
 * @parent index
 * @inherits jQuery.Model
 * Wraps backend purchase services.
 */
$.Model('GoodEarthStore.Models.Purchase',
/* @Static */
{

	process:function(data, success, error){

		success=success?success:function(){alert('success');};
		error=error?error:this.defaultError;


		var errors=this.validate(data.cardData);
		if (errors.length>0){
			success({status:-1, messages:errors, data:{}});
			return;
		}

var outOrders=[];

	var list=data.purchase.orders;
	for (var i=0, len=list.length; i<len; i++){
		var element=list[i];
		outOrders.push({
			day:{refId:element.day.refId},
			offer:{refId:element.offering.refId},
			student:{refId:element.student.refId},
			refId:element.refId

		});
	}

var outObj={};
for (var i in data){
	if (i=='purchase'){continue;} //skip this
	outObj[i]=data[i];
}

outObj['orders']=outOrders;
outObj['refId']=data.purchase.refId;
		$.ajax({
				url: '/purchase/pay',
				type: 'post',
				dataType: 'json',
				data: {data:outObj},
				success: success,
				error: error,
				fixture: false
			});

},

validate:function(inData){
	var name, datum,
		errors=[];
	name='cardName';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Name is required"]);}

	name='street';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Street is required"]);}

	name='city';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "City is required"]);}

	name='state';
	datum=inData[name];
	if (datum.toLowerCase()!='ca')
	{errors.push([name, "We only serve California"]);}

	name='zip';
	datum=inData[name];
	if (!datum || datum=='00000')
	{errors.push([name, "Zip Code is required"]);}
		else if (datum.length!=5)
		{errors.push([name, "Zip must be five digits (00000)"]);}
		else if (!datum.match(/\d{5}/))
		{errors.push([name, "Zip must be five digits (00000)"]);}

	name='phoneNumber';
	datum=inData[name];
	if (!datum || datum=='000-000-0000')
	{errors.push([name, "Phone Number is required"]);}
		else if (datum.length!=12)
		{errors.push([name, "Phone Number must be 000-000-0000"]);}
		else if (!datum.match(/\d{3}[ -]\d{3}[- ]\d{4}/))
		{errors.push([name, "Phone Number must be 000-000-0000"]);}


	name='cardNumber';
	datum=inData[name];
	if (!datum)
	{errors.push([name, "Card Number is required"]);}
		else if (datum.replace(/ /g, '', datum).length<15)
		{errors.push([name, "Card number is invalid"]);}

	name='expMonth';
	datum=inData[name];
	if (!datum || datum=='MM')
	{errors.push([name, "Expiration Date is required"]);}
		else if (!datum.match(/\d{2}/))
		{errors.push([name, "Month must be two numbers"]);}
		else if (datum<1 || datum>12)
		{errors.push([name, "Month must be 1 to 12"]);}

	name='expYear';
	datum=inData[name];
	if (!datum || datum=='YY')
	{errors.push([name, "year is required"]);}
		else if (!datum.match(/\d{2}/))
		{errors.push([name, "Year must be two numbers"]);}

	return errors;
}


},
/* @Prototype */
{});

})