(function($){
    $.fn.qprompt= function(args) {

//     	eg,
//			<input prompt='First Name' name='firstName' value=''>
//     		$('input').qprompt({color:'red'});
//     		$('#divId').find('input').qprompt();

		args=args?args:{};
    var self=this,
    	domObj=$(this),
    	prompt, thisDomObj,
    	color=args.color?args.color:'#bbb',
    	holdColor;

		for (var i=0, len=domObj.length; i<len; i++){
			thisDomObj=$(domObj[i]);

			prompt=thisDomObj.attr('prompt')

			if (prompt && !thisDomObj.val()){

				holdColor=thisDomObj.css('color');
				thisDomObj.data('holdColor', holdColor);

				thisDomObj.css('color', color);
				thisDomObj.val(prompt)
					.focusin(
						function(thisEventObj){
							var domObj=$(this),
								color=domObj.data('holdColor');

							domObj.unbind('focusin')
								.val('')
								.css('color', color);
						}
					);
			}
		}

	return domObj;
    };

})(jQuery);