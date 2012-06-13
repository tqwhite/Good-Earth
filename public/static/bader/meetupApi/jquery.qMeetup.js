(function($){
    $.fn.meetup=function(args){
    
    var self=this,
    	defaultTemplate=args.defaultTemplate,
    	specialTemplates=args.specialTemplates,
    	blockTemplate=args.blockWrapperTemplate,
    	venueTemplate=venueTemplate?venueTemplate:"<div style='color:black;font-size:12pt;margin-bottom:15px; margin-left:20px;'><!street1!>, <!city!>, <!zip!></div>",
    	highlightStyles=args.highlightStyles,
    	highlightClassName=args.highlightClassName,
    	accessParameters=args.accessParameters
    	undefinedVenueString=args.undefinedVenueString?args.undefinedVenueString:'check back later for location'
    	;
    	
    	accessParameters.url=accessParameters.url?accessParameters.url:"https://api.meetup.com/2/events";
    	if (!accessParameters.key){alert('you must specify a meetup access key')};
    	if (!accessParameters.group_urlname){alert('you must specify a meetup group_urlname')};
    	accessParameters.page=accessParameters.page?accessParameters.page:1000;
    
	var request = $.ajax({
	  url: accessParameters.url,
	  type: "GET",
	  data: {
				key:accessParameters.key,
				sign:true,
				group_urlname:accessParameters.group_urlname,
				page:accessParameters.page
			
			},
	  dataType: "jsonp",
	  crossDomain:true
	});
	
	request.done(function(inData) {
		var outString='',
			list=inData.results,
			uniqueClassSuffix='_'+Math.floor(Math.random()*9999999).toString();
			
		for(var i=0, len=list.length; i<len; i++){
			var pattern, venueString, infoString, timeString, skipThisOne,
				name=list[i],
				time=new Date(name.time),
				yearString=new Date().getFullYear(),
				url=name.event_url,
				id=name.id,
				venue=name.venue,
				template=defaultTemplate,
				templateList=specialTemplates,
				localHighlightStyles=highlightStyles,
				clearHightlightStyles={},
				localHighlightClassName=highlightClassName,
				dateTimeFormat=args.dateTimeFormat?args.dateTimeFormat:0,
				exclusionStringList=args.exclusionStringList?args.exclusionStringList:[],
				inclusionStringList=args.inclusionStringList?args.inclusionStringList:[]
				;
			
			switch (dateTimeFormat.toString()){
				case '0':
				default:
					timeString=time.toLocaleString().replace(':00 '+yearString, '');
					break;
				case '1':
					timeString=time.toLocaleString().replace(':00', '');
					break;
				case '2':
					timeString=time.toLocaleString();
					break;
			}
				
			if (localHighlightStyles){
				clearHightlightStyles=$.extend({}, localHighlightStyles);
			
				for (var k in clearHightlightStyles){
					clearHightlightStyles[k]='';
				}
			}
			
			for (var j=0, len2=templateList.length; j<len2; j++){
				pattern=new RegExp(templateList[j].selectionString);
				if (name.name.match(pattern)){
					template=templateList[j].template;
				}
			}
			

			for (var j=0, len2=exclusionStringList.length; j<len2; j++){
				pattern=new RegExp(exclusionStringList[j].selectionString);
				if (name.name.match(pattern)){
					skipThisOne=true;
					break;
				}
				else{
					skipThisOne=false;
				}
			}
					
			for (var j=0, len2=inclusionStringList.length; j<len2; j++){
				pattern=new RegExp(inclusionStringList[j].selectionString);
				if (name.name.match(pattern)){
					skipThisOne=false;
					break;
				}
				else{
					skipThisOne=true;
				}
			}

			if (skipThisOne){continue;}
			
			if (typeof(venue)!='undefined'){			
				venueString=venueTemplate
					.replace(/<!street1!>/, venue.address_1)
					.replace(/<!street2!>/, venue.address_2)
					.replace(/<!city!>/, venue.city)
					.replace(/<!zip!>/, venue.zip);
			}
			else{
				venueString=undefinedVenueString;
			}
			
			infoString=template
			.replace(/<!title!>/, name.name)
			.replace(/<!url!>/, url)
			.replace(/<!dateTime!>/, timeString);
			
			outString+="<div id='"+id+"' class='meetupListing"+uniqueClassSuffix+"'><div>"+infoString+"</div><div class='venue"+uniqueClassSuffix+"' style='display:none;'>"+venueString+"</div></div>";		
		}
		
		if (blockTemplate){
			outString=blockTemplate.replace(/<!blockText!>/, outString);
		}
		
		$(self).html(outString);

		$('.meetupListing'+uniqueClassSuffix).hover(
			function(eventObj){
			var localSuffix=uniqueClassSuffix;
				if (localHighlightClassName){
					$(this).addClass(localHighlightClassName);
				}
				else{
					$(this).css(localHighlightStyles);
				}
				
				$(this).find('.venue'+localSuffix).show();
			},
			
			function(eventObj){
			var localSuffix=uniqueClassSuffix;
				if (localHighlightClassName){
					$(this).removeClass(localHighlightClassName);
				}
				else{
					$(this).css(clearHightlightStyles);
				}
				
				$(this).find('.venue'+localSuffix).hide();
			}
		);
	});
	
	request.fail(function(jqXHR, textStatus) {
	  $(self).html( "Meetup.com failed to respond: " + textStatus + ". Please try again later.");
	});
	
	return this;
	}
})(jQuery);

