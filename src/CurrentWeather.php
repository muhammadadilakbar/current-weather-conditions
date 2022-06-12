<?php

namespace BaseSpeedDigital\CurrentWeather;

class CurrentWeather
{
    private $options = array();

	public function __construct() {
        $this->getOptions();
        \add_action( 'admin_menu', array( $this, 'addSettingsMenu' ) );
        \add_action( 'admin_post_bspdi_cuwe_save_options', array( $this, 'processForm' ) );
        \add_action( 'admin_enqueue_scripts', array( $this, 'loadAdminJSScripts'));
        \add_action( 'wp_enqueue_scripts', array( $this, 'loadPublicJSScripts'));
        \add_shortcode( "current_weather_conditions", array( $this, "renderShortCode" ) );
	}
    
    public function loadAdminJSScripts() {
        \wp_enqueue_script( "bspdi-cuwe-fetch-locations", BSPDI_CUWE_URL . "assets/js/fetch-locations.js", array( "jquery", "jquery-ui-core", "jquery-ui-autocomplete" ), "20220608" );
        $apiKey = array( "api_key" => $this->options["api_key"] );
        \wp_localize_script( 'bspdi-cuwe-fetch-locations', 'bspdi_cuwe_api_key', $apiKey );
    }

    public function loadPublicJSScripts() {
        \wp_enqueue_script( "bspdi-cuwe-fetch-current-cond", BSPDI_CUWE_URL . "assets/js/fetch-current-cond.js", array( "jquery" ), "20220609" );
        $apiKeyAndLocs = $this->getOptions();
        $script = "var bspdi_cuwe_api_key_and_locs = ["; //returns an array of arrays
        foreach( $apiKeyAndLocs as $key => $value ) {
            $script = $script . "[\"$key\",\"$value\"],";
        }
        $script = $script . "];";
        \wp_add_inline_script( "bspdi-cuwe-fetch-current-cond", $script, "before" );
        \wp_enqueue_style( "bspdi_cuwe_styles", BSPDI_CUWE_URL . "assets/css/styles.css" );
    }

    public function addSettingsMenu() {
        \add_menu_page( 'Current Weather', 'Current Weather', 'manage_options', 'bspdi_cuwe_settings_page', array( $this, 'renderSettingsPage' ) );
    }

    private function getOptions() {
        $this->options = \get_option( "bspdi_cuwe_options" );
        return $this->options;
    }

    public function renderShortCode() {
        $output = "<div id=\"bspdi_cuwe_output\"></div>";
        return $output;
    }

    public function renderSettingsPage() {
        if( ! current_user_can( "manage_options" ) ) {
            wp_die("Sorry, you don't have necessary permissions to perform this action");
            exit();
        }
        ?>
        <div class="wrap">
            <?php
            if( isset( $_GET[ "status" ] ) && $_GET["status"] == "incomplete" ) {
            ?>
                <div class="notice notice-error is-dismissible">
                    <p>Incomplete information provided. Please try again.</p>
                </div>
            <?php
            }
            ?>
            <h2>Current Weather Conditions</h2>
            <form action="admin-post.php" method="post">
                <input type="hidden" name="action" value="bspdi_cuwe_save_options" />
                <input type="hidden" id="bspdi_cuwe_location_key" name="bspdi_cuwe_location_key" value="" />
                <?php wp_nonce_field( "bspdi_cuwe_save_options_noac", "bspdi_cuwe_save_options_nona" ); ?>
                <p>
                    <label for="bspdi_cuwe_api_key">AccuWeather API Key: </label>
                    <input type="text" id="bspdi_cuwe_api_key" name="bspdi_cuwe_api_key" value="<?php echo $this->options["api_key"] ?>" />
                </p>
                <p>
                    <label for="bspdi_cuwe_new_location">Enter city name: </label>
                    <input type="text" id="bspdi_cuwe_new_location" name="bspdi_cuwe_new_location" />
                </p>
                <?php
                foreach( $this->options as $locationKey => $locationName ) {
                    if( $locationKey === "api_key" ) {
                        //do nothing
                    }
                    else {
                        echo "<p>$locationName Delete this location? "; ?>
                        <input type="checkbox" name="bspdi_cuwe_<?php echo $locationKey ?>" value="<?php echo $locationName ?>" />
                    <?php
                    }
                }
                ?>
                <p><input type="submit" value="Submit" class="button-primary"/></p>
            </form>
        <?php
    }

    public function processForm() {
        if( ! current_user_can( "manage_options" ) ) {
            wp_die("Sorry, you don't have necessary permissions to perform this action");
            exit();
        }
        // Verify nonce
        check_admin_referer( "bspdi_cuwe_save_options_noac", "bspdi_cuwe_save_options_nona" );

        $errors = array();

        if( isset( $_POST["bspdi_cuwe_api_key"] ) && ! empty( $_POST["bspdi_cuwe_api_key"] ) ) {
            $this->options["api_key"] = sanitize_text_field( $_POST["bspdi_cuwe_api_key"] );
        }
        else {
            $errors[] = "Please enter API Key.";
        }
        //location_key is <input type="hidden"
        if( isset( $_POST["bspdi_cuwe_location_key"] ) && ! empty( $_POST["bspdi_cuwe_location_key"] ) ) { 
            if( isset( $_POST["bspdi_cuwe_new_location"] ) && ! empty( $_POST["bspdi_cuwe_new_location"] ) ) {
                $sanitizedLocationName = sanitize_text_field( $_POST["bspdi_cuwe_new_location"] );
            }
            else {
                $errors[] = "Missing city name. Please try again.";
            }
            $sanitizedLocationKey = sanitize_text_field( $_POST["bspdi_cuwe_location_key"] );
            $sanitizedLocationKey = intval( $sanitizedLocationKey );
            if( is_numeric( $sanitizedLocationKey ) ) {
                $this->options[ "$sanitizedLocationKey" ] = $sanitizedLocationName;
            }
        }
        else {
            $errors[] = "Please try again.";
        }

        if( empty( $errors ) ) {
            //Store updated options array to database
            \update_option( "bspdi_cuwe_options", $this->options );
            \wp_redirect( add_query_arg( 'page', 'bspdi_cuwe_settings_page', admin_url() ) );
            exit();
        }
        else {
            \wp_redirect( add_query_arg( array( 'page' => 'bspdi_cuwe_settings_page', 'status' => 'incomplete' ), admin_url() ) );
            exit();
        }
    }
}