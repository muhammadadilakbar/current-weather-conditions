<?php

namespace BaseSpeedDigital\CurrentWeather;

class Activation {
    public static function activate()
    {
        //check if this plugin is being installed for first time
        if( get_option( "bspdi_cuwe_options" ) === FALSE ) {
            add_option( "bspdi_cuwe_options", array( "api_key" => "" ) );
        }
    }
}