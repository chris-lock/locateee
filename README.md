#LocateEE

An ExpressionEngine fieldtype to geolcate addresses.

##Parameters

var_prefix	 - (string)		 - Prefixes the template tags

##Tags

{street}	 - (string)		 - The street address from the field
{city}		 - (string)		 - The city from the field
{state}		 - (string)		 - The state from the field
{zip}		 - (string)		 - The zip from the field

#Example

{locateee_field}
	{street}, {city}, {state} {zip}
{/locateee_field}

{locateee_field var_prefix="place"}
	{place:street}, {place:city}, {place:state} {place:zip}
{/locateee_field}