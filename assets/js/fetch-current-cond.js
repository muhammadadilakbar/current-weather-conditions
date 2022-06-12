jQuery( document ).ready( function($)
{
  bspdi_cuwe_api_key_and_locs.forEach(element => { //element is an array
    if( element[0] === "api_key" ) {
      // do nothing
    }
    else
    {
      $.ajax(
        {
          url: "http://dataservice.accuweather.com/currentconditions/v1/" + element[0],
          dataType: "json",
          data: {
            apikey: bspdi_cuwe_api_key_and_locs[0][1],
            details: "true"
          },
          success: function( data )
          {
            let output = "" + element[1] + " </br />";
            output = output + data[0].WeatherText + "<br />";
            output = output + "Temperature: " + data[0].Temperature.Metric.Value + "C <br />";
            output = output + "Relative Humidity: " + data[0].RelativeHumidity + "% <br />";
            output = output + "Wind: " + data[0].Wind.Direction.English + " " + data[0].Wind.Speed.Metric.Value + "km/h <br />";
            output = output + "Pressure: " + data[0].Pressure.Metric.Value + "mb <br />";
            output = output + "Wet Bulb Temperature: " + data[0].WetBulbTemperature.Metric.Value + "C <br />";
            //link
            $( "#bspdi_cuwe_output" ).append( "<p>" + output + "</p>" );
          }
      } );
    }
  } );
} );