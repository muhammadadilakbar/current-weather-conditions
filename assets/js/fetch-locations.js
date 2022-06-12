jQuery( document ).ready( function($)
{
  $( "#bspdi_cuwe_new_location" ).autocomplete(
  {
    source: function( request, response )
    {
      $.ajax(
      {
        url: "http://dataservice.accuweather.com/locations/v1/cities/autocomplete",
        dataType: "json",
        data: {
          apikey: bspdi_cuwe_api_key.api_key,
          q: request.term
        },
        success: function( data )
        {
          let localizedNames = [];
          data.forEach( element => {
            let probableLocation = {};
            probableLocation.label = element.LocalizedName + ", " + element.AdministrativeArea.LocalizedName + ", " + element.Country.LocalizedName;
            probableLocation.value = probableLocation.label;
            probableLocation.locationKey = element.Key;
            localizedNames.push( probableLocation );
          });
          response( localizedNames );
        }
      } );
    },
    minLength: 2,
    select: function( event, ui ) {
      $("#bspdi_cuwe_location_key").attr('value', ui.item.locationKey);
    }
  } );
} );