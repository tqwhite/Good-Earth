(function($) {
	$.fn.html5tooltips = function(args) {

		// animateFunction - Choose one of the available animate functions: fadein, foldin, foldout, roll, scalein, slidein, spin
		// color - Choose one of the available predefined colors: daffodil, daisy, mustard, citrus-zest, pumpkin, tangerine, salmon, persimmon, rouge, scarlet, hot-pink, princess, petal, lilac, lavender, violet, cloud, dream, gulf, turquoise, indigo, navy, sea-foam, teal, peacock, ceadon, olive, bamboo, grass, kelly, forrest, chocolate, terra-cotta, camel, linen, stone, smoke, steel, slate, charcoal, black, white, metalic-silver, metalic-gold, metalic-copper; or any CSS color.
		// contentText - Text for a tooltip; HTML may be applied.
		// contentMore - Text for the expanded version of a tooltip which shows up when focused on a target element; HTML may be applied.
		// disableAnimation - Disable the animation: true or false
		// stickTo - Choose one of the available stick values: bottom, left, right, top
		// stickDistance - The number of pixels that represent the distance between the tooltip and a target element.
		// targetSelector - A CSS selector which is used to catch a target element in the document.
		// targetXPath - An xPath value which is used to catch a target element in the document.
		// maxWidth - The maximum width of the expanded version of the tooltip.

		// data-tooltip - Value for the contentText parameter.
		// data-tooltip-animate-function - Value for the animateFunction parameter.
		// data-tooltip-color - Value for the color parameter.
		// data-tooltip-more - Value for contentMore parameter.
		// data-tooltip-stickto - Value for stickTo parameter.
		// data-tooltip-maxwidth - Value for maxWidth parameter.

		this.each(function(a, b, c) {

			var replacementObject = {},
				thisObj = $(this);

			for (var i = 0, len = args.metaParameters.requiredAttributeNames.length; i < len; i++) {
				var element = args.metaParameters.requiredAttributeNames[i];
				var value = thisObj.attr(element);
				if (value) {
					replacementObject[element] = value;
				}
			}

			for (var i = 0, len = args.metaParameters.requiredPropertyNames.length; i < len; i++) {
				var element = args.metaParameters.requiredPropertyNames[i];
				var value = thisObj.prop(element);
				if (value) {
					replacementObject[element] = value;
				}
			}

			var tooltipArgs = $.extend({}, args);
			delete tooltipArgs.metaParameters;

			for (var argName in args) {
				var argValue = args[argName];
				if (typeof (argValue) == 'function') {
					argValue = argValue(this, argName, replacementObject);
				} else {
					if (typeof (argValue) != 'string') {
						continue;
					}

					for (var pattern in replacementObject) {
						var replacement = replacementObject[pattern];
						argValue = argValue.replace("<!" + pattern + "!>", replacement);

					}
				}
				tooltipArgs[argName] = argValue;
			}

			html5tooltips(tooltipArgs);

		});

	};

})(jQuery);




