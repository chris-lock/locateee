(function() {
	Locateee = function() {}

	var locateeeFieldClass = 'locateee-field';
	var locateeeLocationButtonClass = 'locateee-button';
	var locateeeLocationButtonDisabledClass = 'locateee-disabled';
	var locateeeRequiredClass = 'locateee-required';
	var locateeeLatClass = 'locateee-lat';
	var locateeeLngClass = 'locateee-lng';
	var locateeeErrorClass = 'locateee-error';
	var locateeeErrorMessageDataLater = 'later';
	var locateeeErrorMessageDataAddress = 'address';
	var locateeeErrorMessageClass = 'locateee-error-message';
	var locateeeErrorCloseClass = 'locateee-close';
	var locateeeSlideSpeed = 400;
	var locateeeInputClass = 'locateee-text';
	var locateeeTdClass = 'locateee';
	var locateeeTdFocussed = 'locateee-focussed';

	Locateee.prototype.init = function init()
	{
		$('.' + locateeeLocationButtonClass).click(getLocation);
		$('.' + locateeeErrorCloseClass).click(closeError);
		$('.' + locateeeInputClass)
			.focus(toggleInputHighlight)
			.blur(toggleInputHighlight);
	}

	function getLocation()
	{
		if ($(this).hasClass(locateeeLocationButtonDisabledClass))
			return false;

		$(this).addClass(locateeeLocationButtonDisabledClass)

		var locateeeGeocoder = new google.maps.Geocoder();
		var locateeeFieldId = $(this).parents('.' + locateeeFieldClass).attr('id');
		var locateeeAddress = getAddress(locateeeFieldId);

		if (locateeeAddress) {
			closeError(false, locateeeFieldId);
			locateeeGeocoder.geocode(
				{
					'address': locateeeAddress
				},
				function(response, status)
				{
					if (status != google.maps.GeocoderStatus.OK)
						return showError(
							locateeeFieldId,
							locateeeErrorMessageDataLater
						);
					
					resetLocationButton(locateeeFieldId);
					setLatLng(
						locateeeFieldId,
						response[0].geometry.location.lat(),
						response[0].geometry.location.lng()
					);
				}
			);
		}

		return false;
	}

	function getAddress(locateeeFieldId)
	{
		var locateeeAddress = [];
		
		$('#' + locateeeFieldId + ' .' + locateeeRequiredClass).each(function()
		{
			if ($(this).val())
				locateeeAddress.push($(this).val());
		});

		locateeeAddress = (locateeeAddress.length)
			? locateeeAddress.join()
			: null;

		if (! locateeeAddress)
			showError(
				locateeeFieldId,
				locateeeErrorMessageDataAddress
			);

		return locateeeAddress;
	}

	function showError(locateeeFieldId, locateeeErrorMessageType)
	{
		closeError(false, locateeeFieldId);
		resetLocationButton(locateeeFieldId);
		setLatLng(locateeeFieldId, '', '');

		var locateeeErrorMessage = 
			$('#' + locateeeFieldId).find('.' + locateeeErrorClass).data(locateeeErrorMessageType);

		$('#' + locateeeFieldId).find('.' + locateeeErrorMessageClass).html(locateeeErrorMessage);
		$('#' + locateeeFieldId).find('.' + locateeeErrorClass).slideDown(locateeeSlideSpeed);
	}

	function resetLocationButton(locateeeFieldId)
	{
		$('#' + locateeeFieldId + ' .' + locateeeLocationButtonClass)
			.removeClass(locateeeLocationButtonDisabledClass);
	}

	function setLatLng(locateeeFieldId, locateeeLat, locateeeLng)
	{
		$('#' + locateeeFieldId + ' .' + locateeeLatClass).val(locateeeLat);
		$('#' + locateeeFieldId + ' .' + locateeeLngClass).val(locateeeLng);
	}

	function initErrorClose()
	{
		$('.' + locateeeErrorCloseClass).click(closeError);
	}

	function closeError(click, locateeeFieldId)
	{
		var locateeeFieldId = (click)
			? $(this).parents('.' + locateeeFieldClass).attr('id') 
			: locateeeFieldId;

		$('#' + locateeeFieldId).find('.' + locateeeErrorClass).hide();

		return false;
	}

	function toggleInputHighlight()
	{
		$(this).parent('td.' + locateeeTdClass).toggleClass(locateeeTdFocussed);
	}

	var locateeeObject = new Locateee();

	locateeeObject.init();
})();