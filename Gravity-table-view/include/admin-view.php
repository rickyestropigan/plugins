<?php

if( !class_exists( 'GravityView_Extension' ) ) {

		if( class_exists('GravityView_Plugin') && is_callable(array('GravityView_Plugin', 'include_extension_framework')) ) {
			GravityView_Plugin::include_extension_framework();
			//include( plugin_dir_path( __FILE__ ) .  'includes/admin-view.php'); 
		} else {
			// We prefer to use the one bundled with GravityView, but if it doesn't exist, go here.
			//include_once plugin_dir_path( __FILE__ ) . 'lib/class-gravityview-extension.php';
				//echo "GravityView_Extension";
		}
	}

class MySettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );	
		add_action( 'show_user_profile',array( $this, 'gravity_extra_profile_fields' ));
		add_action( 'edit_user_profile', array( $this, 'gravity_extra_profile_fields' ));
		
		add_action( 'show_user_profile',array( $this, 'gravity_billing_profile_fields' ));
		add_action( 'edit_user_profile', array( $this, 'gravity_billing_profile_fields' ));
		
		
		add_action( 'personal_options_update', array( $this, 'save_gravity_extra_profile_fields' ));
		add_action( 'edit_user_profile_update', array( $this, 'save_gravity_extra_profile_fields' ));
	
   

		
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Gravity View Setting', 
            'manage_options', 
            'my-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'my_option_name' );
		$forms = RGFormsModel::get_forms(1);
	
        ?>
        <div class="wrap">
            <h1>Gravity View Setting</h1>
            <form method="post" action="options.php">
			
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );
                do_settings_sections( 'my-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
		
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
	$forms = RGFormsModel::get_forms(1);	
	
	
	
        register_setting(
            'my_option_group', // Option group
            'my_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
/* 
        add_settings_section(
            'setting_section_id', // ID
            '', // Title
            array( $this, 'print_section_info' ), // Callback
            'my-setting-admin' // Page
        );   */
 foreach( $forms as $array){
	  add_settings_section(
							'setting_'+ $array->id, // ID
							'', // Title
							array( $this, 'print_section_info' ), // Callback
							'my-setting-admin' // Page
						);  
						
        add_settings_field(
           $array->id, // ID
		   $array->title, // Title
			array($this, "id_number_callback"), // Callback
			'my-setting-admin', // Page
			'setting_'+ $array->id,
			array (
				'field' =>  $array->id
			)					
        );       
}
      /*   add_settings_field(
            'title', 
            'Show Column', 
            array( $this, 'title_callback' ), 
            'my-setting-admin', 
            'setting_section_id'
        );    */    
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
		$forms = RGFormsModel::get_forms(1);	
		
		 foreach( $forms as $array)
		 {
			$input_id =   $array->id;
			//echo $input_id;
			if( isset( $input[$input_id] ) )
				$new_input[$input_id] = sanitize_text_field( $input[$input_id] );
		}
      

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
       // print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function id_number_callback($field)
    {
	//	$field['special']
        printf(
            '<input style="width: 317px;padding: 6px;" type="text" id="'.$field['field'].'" name="my_option_name['.$field['field'].']" value="%s" />',
            isset( $this->options[$field['field']] ) ? esc_attr( $this->options[$field['field']]) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function title_callback()
    {
        printf(
            '<textarea id="title" name="my_option_name[title]" value />%s</textarea>',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }


function gravity_extra_profile_fields( $user ) { ?>
    <h3>Gravity Information</h3>
    <table class="form-table">
        <tr>
            <th><label for="gtv_first_name">First Name</label></th>
            <td>
                <input type="text" name="gtv_first_name" id="gtv_first_name" value="<?php echo esc_attr( get_the_author_meta( 'gtv_first_name', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Please enter your First Name.</span>
            </td>
        </tr>
		  <tr>
            <th><label for="gtv_last_name">Last Name</label></th>
            <td>
                <input type="text" name="gtv_last_name" id="gtv_last_name" value="<?php echo esc_attr( get_the_author_meta( 'gtv_last_name', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Please enter your Last Name.</span>
            </td>
        </tr>
		  <tr>
            <th><label for="phone_number">Phone Number</label></th>
            <td>
                <input type="text" name="phone_number" id="phone_number" value="<?php echo esc_attr( get_the_author_meta( 'phone_number', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Please enter your Phone Number.</span>
            </td>
        </tr>
	
		<tr>
            <th><label for="gtv_websites">Website</label></th>
            <td>
                <input type="text" name="gtv_websites" id="gtv_websites" value="<?php echo esc_attr( get_the_author_meta( 'gtv_websites', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Please enter your Website.</span>
            </td>
        </tr>
		 
    </table>
	

    <?php
}


public function country_option($user,$meta_key='gtv_billing_country')
{
$return_data_selection =
"<option value='Afghanistan' ".selected( 'Afghanistan', get_the_author_meta($meta_key, $user->ID ) )." >Afghanistan</option>
<option value='Albania' ".selected( "Albania", get_the_author_meta($meta_key, $user->ID ) )." >Albania</option>
<option value='Algeria' ".selected( "Algeria", get_the_author_meta($meta_key, $user->ID ) )." >Algeria</option>
<option value='American Samoa' ".selected( "American Samoa", get_the_author_meta($meta_key, $user->ID ) )." >American Samoa</option>
<option value='Andorra' ".selected( "Andorra", get_the_author_meta($meta_key, $user->ID ) )." >Andorra</option>
<option value='Angola' ".selected( "Angola", get_the_author_meta($meta_key, $user->ID ) )." >Angola</option>
<option value='Antigua and Barbuda' ".selected( "Antigua and Barbuda", get_the_author_meta($meta_key, $user->ID ) )." >Antigua and Barbuda</option>
<option value='Argentina' ".selected( "Argentina", get_the_author_meta($meta_key, $user->ID ) )." >Argentina</option>
<option value='Armenia' ".selected( "Armenia", get_the_author_meta($meta_key, $user->ID ) )." >Armenia</option>
<option value='Australia' ".selected( "Australia", get_the_author_meta($meta_key, $user->ID ) )." >Australia</option>
<option value='Austria' ".selected( "Austria", get_the_author_meta($meta_key, $user->ID ) )." >Austria</option>
<option value='Azerbaijan' ".selected( "Azerbaijan", get_the_author_meta($meta_key, $user->ID ) )." >Azerbaijan</option>
<option value='Bahamas' ".selected( "Bahamas", get_the_author_meta($meta_key, $user->ID ) )." >Bahamas</option>
<option value='Bahrain' ".selected( "Bahrain", get_the_author_meta($meta_key, $user->ID ) )." >Bahrain</option>
<option value='Bangladesh' ".selected( "Bangladesh", get_the_author_meta($meta_key, $user->ID ) )." >Bangladesh</option>
<option value='Barbados' ".selected( "Barbados", get_the_author_meta($meta_key, $user->ID ) )." >Barbados</option>
<option value='Belarus' ".selected( "Belarus", get_the_author_meta($meta_key, $user->ID ) )." >Belarus</option>
<option value='Belgium' ".selected( "Belgium", get_the_author_meta($meta_key, $user->ID ) )." >Belgium</option>
<option value='Belize' ".selected( "Belize", get_the_author_meta($meta_key, $user->ID ) )." >Belize</option>
<option value='Benin' ".selected( "Benin", get_the_author_meta($meta_key, $user->ID ) )." >Benin</option>
<option value='Bermuda' ".selected( "Bermuda", get_the_author_meta($meta_key, $user->ID ) )." >Bermuda</option>
<option value='Bhutan' ".selected( "Bhutan", get_the_author_meta($meta_key, $user->ID ) )." >Bhutan</option>
<option value='Bolivia' ".selected( "Bolivia", get_the_author_meta($meta_key, $user->ID ) )." >Bolivia</option>
<option value='Bosnia and Herzegovina' ".selected( "Bosnia and Herzegovina", get_the_author_meta($meta_key, $user->ID ) )." >Bosnia and Herzegovina</option>
<option value='Botswana' ".selected( "Botswana", get_the_author_meta($meta_key, $user->ID ) )." >Botswana</option>
<option value='Brazil' ".selected( "Brazil", get_the_author_meta($meta_key, $user->ID ) )." >Brazil</option>
<option value='Brunei' ".selected( "Brunei", get_the_author_meta($meta_key, $user->ID ) )." >Brunei</option>
<option value='Bulgaria' ".selected(Bulgaria)." >Bulgaria</option>
<option value='Burkina Faso' ".selected( "Burkina Faso", get_the_author_meta($meta_key, $user->ID ) )." >Burkina Faso</option>
<option value='Burundi' ".selected( "Burundi", get_the_author_meta($meta_key, $user->ID ) )." >Burundi</option>
<option value='Cambodia' ".selected( "Cambodia", get_the_author_meta($meta_key, $user->ID ) )." >Cambodia</option>
<option value='Cameroon' ".selected( "Cameroon", get_the_author_meta($meta_key, $user->ID ) )." >Cameroon</option>
<option value='Canada' ".selected( "Canada", get_the_author_meta($meta_key, $user->ID ) )." >Canada</option>
<option value='Cape Verde' ".selected( "Cape Verde", get_the_author_meta($meta_key, $user->ID ) )." >Cape Verde</option>
<option value='Cayman Islands' ".selected( "Cayman Islands", get_the_author_meta($meta_key, $user->ID ) )." >Cayman Islands</option>
<option value='Central African Republic' ".selected( "Central African Republic", get_the_author_meta($meta_key, $user->ID ) )." >Central African Republic</option>
<option value='Chad' ".selected( "Chad", get_the_author_meta($meta_key, $user->ID ) )." >Chad</option>
<option value='Chile' ".selected( "Chile", get_the_author_meta($meta_key, $user->ID ) )." >Chile</option>
<option value='China' ".selected( "China", get_the_author_meta($meta_key, $user->ID ) )." >China</option>
<option value='Colombia' ".selected( "Colombia", get_the_author_meta($meta_key, $user->ID ) )." >Colombia</option>
<option value='Comoros' ".selected( "Comoros", get_the_author_meta($meta_key, $user->ID ) )." >Comoros</option>
<option value='Congo, Democratic Republic of the' ".selected( "Congo, Democratic Republic of the", get_the_author_meta($meta_key, $user->ID ) )." >Congo, Democratic Republic of the</option>
<option value='Congo, Republic of the' ".selected( "Congo, Republic of the", get_the_author_meta($meta_key, $user->ID ) )." >Congo, Republic of the</option>
<option value='Costa Rica' ".selected( "Costa Rica", get_the_author_meta($meta_key, $user->ID ) )." >Costa Rica</option>
<option value='Côte d'Ivoire' ".selected( "Côte d'Ivoire", get_the_author_meta($meta_key, $user->ID ) )." >Côte d'Ivoire</option>
<option value='Croatia' ".selected( "Croatia", get_the_author_meta($meta_key, $user->ID ) )." >Croatia</option>
<option value='Cuba' ".selected( "Cuba", get_the_author_meta($meta_key, $user->ID ) )." >Cuba</option>
<option value='Curaçao' ".selected( "Curaçao", get_the_author_meta($meta_key, $user->ID ) )." >Curaçao</option>
<option value='Cyprus' ".selected( "Cyprus", get_the_author_meta($meta_key, $user->ID ) )." >Cyprus</option>
<option value='Czech Republic' ".selected( "Czech Republic", get_the_author_meta($meta_key, $user->ID ) )." >Czech Republic</option>
<option value='Denmark' ".selected( "Denmark", get_the_author_meta($meta_key, $user->ID ) )." >Denmark</option>
<option value='Djibouti' ".selected( "Djibouti", get_the_author_meta($meta_key, $user->ID ) )." >Djibouti</option>
<option value='Dominica' ".selected( "Dominica", get_the_author_meta($meta_key, $user->ID ) )." >Dominica</option>
<option value='Dominican Republic' ".selected( "Dominican Republic", get_the_author_meta($meta_key, $user->ID ) )." >Dominican Republic</option>
<option value='East Timor' ".selected( "East Timor", get_the_author_meta($meta_key, $user->ID ) )." >East Timor</option>
<option value='Ecuador' ".selected( "Ecuador", get_the_author_meta($meta_key, $user->ID ) )." >Ecuador</option>
<option value='Egypt' ".selected( "Egypt", get_the_author_meta($meta_key, $user->ID ) )." >Egypt</option>
<option value='El Salvador' ".selected( "El Salvador", get_the_author_meta($meta_key, $user->ID ) )." >El Salvador</option>
<option value='Equatorial Guinea' ".selected( "Equatorial Guinea", get_the_author_meta($meta_key, $user->ID ) )." >Equatorial Guinea</option>
<option value='Eritrea' ".selected( "Eritrea", get_the_author_meta($meta_key, $user->ID ) )." >Eritrea</option>
<option value='Estonia' ".selected( "Estonia", get_the_author_meta($meta_key, $user->ID ) )." >Estonia</option>
<option value='Ethiopia' ".selected( "Ethiopia", get_the_author_meta($meta_key, $user->ID ) )." >Ethiopia</option>
<option value='Faroe Islands' ".selected( "Faroe Islands", get_the_author_meta($meta_key, $user->ID ) )." >Faroe Islands</option>
<option value='Fiji' ".selected( "Fiji", get_the_author_meta($meta_key, $user->ID ) )." >Fiji</option>
<option value='Finland' ".selected( "Finland", get_the_author_meta($meta_key, $user->ID ) )." >Finland</option>
<option value='France' ".selected( "France", get_the_author_meta($meta_key, $user->ID ) )." >France</option>
<option value='French Polynesia' ".selected( "French Polynesia", get_the_author_meta($meta_key, $user->ID ) )." >French Polynesia</option>
<option value='Gabon' ".selected( "Gabon", get_the_author_meta($meta_key, $user->ID ) )." >Gabon</option>
<option value='Gambia' ".selected( "Gambia", get_the_author_meta($meta_key, $user->ID ) )." >Gambia</option>
<option value='Georgia' ".selected( "Georgia", get_the_author_meta($meta_key, $user->ID ) )." >Georgia</option>
<option value='Germany' ".selected( "Germany", get_the_author_meta($meta_key, $user->ID ) )." >Germany</option>
<option value='Ghana' ".selected( "Ghana", get_the_author_meta($meta_key, $user->ID ) )." >Ghana</option>
<option value='Greece' ".selected( "Greece", get_the_author_meta($meta_key, $user->ID ) )." >Greece</option>
<option value='Greenland' ".selected( "Greenland", get_the_author_meta($meta_key, $user->ID ) )." >Greenland</option>
<option value='Grenada' ".selected( "Grenada", get_the_author_meta($meta_key, $user->ID ) )." >Grenada</option>
<option value='Guam' ".selected( "Guam", get_the_author_meta($meta_key, $user->ID ) )." >Guam</option>
<option value='Guatemala' ".selected( "Guatemala", get_the_author_meta($meta_key, $user->ID ) )." >Guatemala</option>
<option value='Guinea' ".selected( "Guinea", get_the_author_meta($meta_key, $user->ID ) )." >Guinea</option>
<option value='Guinea-Bissau' ".selected( "Guinea-Bissau", get_the_author_meta($meta_key, $user->ID ) )." >Guinea-Bissau</option>
<option value='Guyana' ".selected( "Guyana", get_the_author_meta($meta_key, $user->ID ) )." >Guyana</option>
<option value='Haiti' ".selected( "Haiti", get_the_author_meta($meta_key, $user->ID ) )." >Haiti</option>
<option value='Honduras' ".selected( "Honduras", get_the_author_meta($meta_key, $user->ID ) )." >Honduras</option>
<option value='Hong Kong' ".selected( "Hong Kong", get_the_author_meta($meta_key, $user->ID ) )." >Hong Kong</option>
<option value='Hungary' ".selected( "Hungary", get_the_author_meta($meta_key, $user->ID ) )." >Hungary</option>
<option value='Iceland' ".selected( "Iceland", get_the_author_meta($meta_key, $user->ID ) )." >Iceland</option>
<option value='India' ".selected( "India", get_the_author_meta($meta_key, $user->ID ) )." >India</option>
<option value='Indonesia' ".selected( "Indonesia", get_the_author_meta($meta_key, $user->ID ) )." >Indonesia</option>
<option value='Iran' ".selected( "Iran", get_the_author_meta($meta_key, $user->ID ) )." >Iran</option>
<option value='Iraq' ".selected( "Iraq", get_the_author_meta($meta_key, $user->ID ) )." >Iraq</option>
<option value='Ireland' ".selected( "Ireland", get_the_author_meta($meta_key, $user->ID ) )." >Ireland</option>
<option value='Israel' ".selected( "Israel", get_the_author_meta($meta_key, $user->ID ) )." >Israel</option>
<option value='Italy' ".selected( "Italy", get_the_author_meta($meta_key, $user->ID ) )." >Italy</option>
<option value='Jamaica' ".selected( "Jamaica", get_the_author_meta($meta_key, $user->ID ) )." >Jamaica</option>
<option value='Japan' ".selected( "Japan", get_the_author_meta($meta_key, $user->ID ) )." >Japan</option>
<option value='Jordan' ".selected( "Jordan", get_the_author_meta($meta_key, $user->ID ) )." >Jordan</option>
<option value='Kazakhstan' ".selected( "Kazakhstan", get_the_author_meta($meta_key, $user->ID ) )." >Kazakhstan</option>
<option value='Kenya' ".selected( "Kenya", get_the_author_meta($meta_key, $user->ID ) )." >Kenya</option>
<option value='Kiribati' ".selected( "Kiribati", get_the_author_meta($meta_key, $user->ID ) )." >Kiribati</option>
<option value='North Korea' ".selected( "North Korea", get_the_author_meta($meta_key, $user->ID ) )." >North Korea</option>
<option value='South Korea' ".selected( "South Korea", get_the_author_meta($meta_key, $user->ID ) )." >South Korea</option>
<option value='Kosovo' ".selected( "Kosovo", get_the_author_meta($meta_key, $user->ID ) )." >Kosovo</option>
<option value='Kuwait' ".selected( "Kuwait", get_the_author_meta($meta_key, $user->ID ) )." >Kuwait</option>
<option value='Kyrgyzstan' ".selected( "Kyrgyzstan", get_the_author_meta($meta_key, $user->ID ) )." >Kyrgyzstan</option>
<option value='Laos' ".selected( "Laos", get_the_author_meta($meta_key, $user->ID ) )." >Laos</option>
<option value='Latvia' ".selected( "Latvia", get_the_author_meta($meta_key, $user->ID ) )." >Latvia</option>
<option value='Lebanon' ".selected( "Lebanon", get_the_author_meta($meta_key, $user->ID ) )." >Lebanon</option>
<option value='Lesotho' ".selected( "Lesotho", get_the_author_meta($meta_key, $user->ID ) )." >Lesotho</option>
<option value='Liberia' ".selected( "Liberia", get_the_author_meta($meta_key, $user->ID ) )." >Liberia</option>
<option value='Libya' ".selected( "Libya", get_the_author_meta($meta_key, $user->ID ) )." >Libya</option>
<option value='Liechtenstein' ".selected( "Liechtenstein", get_the_author_meta($meta_key, $user->ID ) )." >Liechtenstein</option>
<option value='Lithuania' ".selected( "Lithuania", get_the_author_meta($meta_key, $user->ID ) )." >Lithuania</option>
<option value='Luxembourg' ".selected( "Luxembourg", get_the_author_meta($meta_key, $user->ID ) )." >Luxembourg</option>
<option value='Macedonia' ".selected( "Macedonia", get_the_author_meta($meta_key, $user->ID ) )." >Macedonia</option>
<option value='Madagascar' ".selected( "Madagascar", get_the_author_meta($meta_key, $user->ID ) )." >Madagascar</option>
<option value='Malawi' ".selected( "Malawi", get_the_author_meta($meta_key, $user->ID ) )." >Malawi</option>
<option value='Malaysia' ".selected( "Malaysia", get_the_author_meta($meta_key, $user->ID ) )." >Malaysia</option>
<option value='Maldives' ".selected( "Maldives", get_the_author_meta($meta_key, $user->ID ) )." >Maldives</option>
<option value='Mali' ".selected( "Mali", get_the_author_meta($meta_key, $user->ID ) )." >Mali</option>
<option value='Malta' ".selected( "Malta", get_the_author_meta($meta_key, $user->ID ) )." >Malta</option>
<option value='Marshall Islands' ".selected( "Marshall Islands", get_the_author_meta($meta_key, $user->ID ) )." >Marshall Islands</option>
<option value='Mauritania' ".selected( "Mauritania", get_the_author_meta($meta_key, $user->ID ) )." >Mauritania</option>
<option value='Mauritius' ".selected( "Mauritius", get_the_author_meta($meta_key, $user->ID ) )." >Mauritius</option>
<option value='Mexico' ".selected( "Mexico", get_the_author_meta($meta_key, $user->ID ) )." >Mexico</option>
<option value='Micronesia' ".selected( "Micronesia", get_the_author_meta($meta_key, $user->ID ) )." >Micronesia</option>
<option value='Moldova' ".selected( "Moldova", get_the_author_meta($meta_key, $user->ID ) )." >Moldova</option>
<option value='Monaco' ".selected( "Monaco", get_the_author_meta($meta_key, $user->ID ) )." >Monaco</option>
<option value='Mongolia' ".selected( "Mongolia", get_the_author_meta($meta_key, $user->ID ) )." >Mongolia</option>
<option value='Montenegro' ".selected( "Montenegro", get_the_author_meta($meta_key, $user->ID ) )." >Montenegro</option>
<option value='Morocco' ".selected( "Morocco", get_the_author_meta($meta_key, $user->ID ) )." >Morocco</option>
<option value='Mozambique' ".selected( "Mozambique", get_the_author_meta($meta_key, $user->ID ) )." >Mozambique</option>
<option value='Myanmar' ".selected( "Myanmar", get_the_author_meta($meta_key, $user->ID ) )." >Myanmar</option>
<option value='Namibia' ".selected( "Namibia", get_the_author_meta($meta_key, $user->ID ) )." >Namibia</option>
<option value='Nauru' ".selected( "Nauru", get_the_author_meta($meta_key, $user->ID ) )." >Nauru</option>
<option value='Nepal' ".selected( "Nepal", get_the_author_meta($meta_key, $user->ID ) )." >Nepal</option>
<option value='Netherlands' ".selected( "Netherlands", get_the_author_meta($meta_key, $user->ID ) )." >Netherlands</option>
<option value='New Zealand' ".selected( "New Zealand", get_the_author_meta($meta_key, $user->ID ) )." >New Zealand</option>
<option value='Nicaragua' ".selected( "Nicaragua", get_the_author_meta($meta_key, $user->ID ) )." >Nicaragua</option>
<option value='Niger' ".selected( "Niger", get_the_author_meta($meta_key, $user->ID ) )." >Niger</option>
<option value='Nigeria' ".selected( "Nigeria", get_the_author_meta($meta_key, $user->ID ) )." >Nigeria</option>
<option value='Northern Mariana Islands' ".selected( "Northern Mariana Islands", get_the_author_meta($meta_key, $user->ID ) )." >Northern Mariana Islands</option>
<option value='Norway' ".selected( "Norway", get_the_author_meta($meta_key, $user->ID ) )." >Norway</option>
<option value='Oman' ".selected( "Oman", get_the_author_meta($meta_key, $user->ID ) )." >Oman</option>
<option value='Pakistan' ".selected( "Pakistan", get_the_author_meta($meta_key, $user->ID ) )." >Pakistan</option>
<option value='Palau' ".selected( "Palau", get_the_author_meta($meta_key, $user->ID ) )." >Palau</option>
<option value='Palestine, State of' ".selected( "Palestine, State of", get_the_author_meta($meta_key, $user->ID ) )." >Palestine, State of</option>
<option value='Panama' ".selected( "Panama", get_the_author_meta($meta_key, $user->ID ) )." >Panama</option>
<option value='Papua New Guinea' ".selected( "Papua New Guinea", get_the_author_meta($meta_key, $user->ID ) )." >Papua New Guinea</option>
<option value='Paraguay' ".selected( "Paraguay", get_the_author_meta($meta_key, $user->ID ) )." >Paraguay</option>
<option value='Peru' ".selected( "Peru", get_the_author_meta($meta_key, $user->ID ) )." >Peru</option>
<option value='Philippines' ".selected( "Philippines", get_the_author_meta($meta_key, $user->ID ) )." >Philippines</option>
<option value='Poland' ".selected( "Poland", get_the_author_meta($meta_key, $user->ID ) )." >Poland</option>
<option value='Portugal' ".selected( "Portugal", get_the_author_meta($meta_key, $user->ID ) )." >Portugal</option>
<option value='Puerto Rico' ".selected( "Puerto Rico", get_the_author_meta($meta_key, $user->ID ) )." >Puerto Rico</option>
<option value='Qatar' ".selected( "Qatar", get_the_author_meta($meta_key, $user->ID ) )." >Qatar</option>
<option value='Romania' ".selected( "Romania", get_the_author_meta($meta_key, $user->ID ) )." >Romania</option>
<option value='Russia' ".selected( "Russia", get_the_author_meta($meta_key, $user->ID ) )." >Russia</option>
<option value='Rwanda' ".selected( "Rwanda", get_the_author_meta($meta_key, $user->ID ) )." >Rwanda</option>
<option value='Saint Kitts and Nevis' ".selected( "Saint Kitts and Nevis", get_the_author_meta($meta_key, $user->ID ) )." >Saint Kitts and Nevis</option>
<option value='Saint Lucia' ".selected( "Saint Lucia", get_the_author_meta($meta_key, $user->ID ) )." >Saint Lucia</option>
<option value='Saint Vincent and the Grenadines' ".selected( "Saint Vincent and the Grenadines", get_the_author_meta($meta_key, $user->ID ) )." >Saint Vincent and the Grenadines</option>
<option value='Samoa' ".selected( "Samoa", get_the_author_meta($meta_key, $user->ID ) )." >Samoa</option>
<option value='San Marino' ".selected( "San Marino", get_the_author_meta($meta_key, $user->ID ) )." >San Marino</option>
<option value='Sao Tome and Principe' ".selected( "Sao Tome and Principe'", get_the_author_meta($meta_key, $user->ID ) )." >Sao Tome and Principe</option>
<option value='Saudi Arabia' ".selected( "Saudi Arabia", get_the_author_meta($meta_key, $user->ID ) )." >Saudi Arabia</option>
<option value='Senegal' ".selected( "Senegal", get_the_author_meta($meta_key, $user->ID ) )." >Senegal</option>
<option value='Serbia' ".selected( "Serbia", get_the_author_meta($meta_key, $user->ID ) )." >Serbia</option>
<option value='Seychelles' ".selected( "Seychelles", get_the_author_meta($meta_key, $user->ID ) )." >Seychelles</option>
<option value='Sierra Leone' ".selected( "Sierra Leone", get_the_author_meta($meta_key, $user->ID ) )." >Sierra Leone</option>
<option value='Singapore' ".selected( "Singapore", get_the_author_meta($meta_key, $user->ID ) )." >Singapore</option>
<option value='Sint Maarten' ".selected( "Sint Maarten", get_the_author_meta($meta_key, $user->ID ) )." >Sint Maarten</option>
<option value='Slovakia' ".selected( "Slovakia", get_the_author_meta($meta_key, $user->ID ) )." >Slovakia</option>
<option value='Slovenia' ".selected( "Slovenia", get_the_author_meta($meta_key, $user->ID ) )." >Slovenia</option>
<option value='Solomon Islands' ".selected( "Solomon Islands", get_the_author_meta($meta_key, $user->ID ) )." >Solomon Islands</option>
<option value='Somalia' ".selected( "Somalia", get_the_author_meta($meta_key, $user->ID ) )." >Somalia</option>
<option value='South Africa' ".selected( "South Africa", get_the_author_meta($meta_key, $user->ID ) )." >South Africa</option>
<option value='Spain' ".selected( "Spain", get_the_author_meta($meta_key, $user->ID ) )." >Spain</option>
<option value='Sri Lanka' ".selected( "Sri Lanka", get_the_author_meta($meta_key, $user->ID ) )." >Sri Lanka</option>
<option value='Sudan' ".selected( "Sudan", get_the_author_meta($meta_key, $user->ID ) )." >Sudan</option>
<option value='Sudan, South' ".selected( "Sudan, South", get_the_author_meta($meta_key, $user->ID ) )." >Sudan, South</option>
<option value='Suriname' ".selected( "Suriname", get_the_author_meta($meta_key, $user->ID ) )." >Suriname</option>
<option value='Swaziland' ".selected( "Swaziland", get_the_author_meta($meta_key, $user->ID ) )." >Swaziland</option>
<option value='Sweden' ".selected( "Sweden", get_the_author_meta($meta_key, $user->ID ) )." >Sweden</option>
<option value='Switzerland' ".selected( "Switzerland", get_the_author_meta($meta_key, $user->ID ) )." >Switzerland</option>
<option value='Syria' ".selected( "Syria", get_the_author_meta($meta_key, $user->ID ) )." >Syria</option>
<option value='Taiwan' ".selected( "Taiwan", get_the_author_meta($meta_key, $user->ID ) )." >Taiwan</option>
<option value='Tajikistan' ".selected( "Tajikistan", get_the_author_meta($meta_key, $user->ID ) )." >Tajikistan</option>
<option value='Tanzania' ".selected( "Tanzania", get_the_author_meta($meta_key, $user->ID ) )." >Tanzania</option>
<option value='Thailand' ".selected( "Thailand", get_the_author_meta($meta_key, $user->ID ) )." >Thailand</option>
<option value='Togo' ".selected( "Togo", get_the_author_meta($meta_key, $user->ID ) )." >Togo</option>
<option value='Tonga' ".selected( "Tonga", get_the_author_meta($meta_key, $user->ID ) )." >Tonga</option>
<option value='Trinidad and Tobago' ".selected( "Trinidad and Tobago", get_the_author_meta($meta_key, $user->ID ) )." >Trinidad and Tobago</option>
<option value='Tunisia' ".selected( "Tunisia", get_the_author_meta($meta_key, $user->ID ) )." >Tunisia</option>
<option value='Turkey' ".selected( "Turkey", get_the_author_meta($meta_key, $user->ID ) )." >Turkey</option>
<option value='Turkmenistan' ".selected( "Turkmenistan", get_the_author_meta($meta_key, $user->ID ) )." >Turkmenistan</option>
<option value='Tuvalu' ".selected( "Tuvalu", get_the_author_meta($meta_key, $user->ID ) )." >Tuvalu</option>
<option value='Uganda' ".selected( "Uganda", get_the_author_meta($meta_key, $user->ID ) )." >Uganda</option>
<option value='Ukraine' ".selected( "Ukraine", get_the_author_meta($meta_key, $user->ID ) )." >Ukraine</option>
<option value='United Arab Emirates' ".selected( "United Arab Emirates", get_the_author_meta($meta_key, $user->ID ) )." >United Arab Emirates</option>
<option value='United Kingdom' ".selected( "United Kingdom", get_the_author_meta($meta_key, $user->ID ) )." >United Kingdom</option>
<option value='United States' ".selected( "United States", get_the_author_meta($meta_key, $user->ID ) )." >United States</option>
<option value='Uruguay' ".selected( "Uruguay", get_the_author_meta($meta_key, $user->ID ) )." >Uruguay</option>
<option value='Uzbekistan' ".selected( "Uzbekistan", get_the_author_meta($meta_key, $user->ID ) )." >Uzbekistan</option>
<option value='Vanuatu' ".selected( "Vanuatu", get_the_author_meta($meta_key, $user->ID ) )." >Vanuatu</option>
<option value='Vatican City' ".selected( "Vatican City", get_the_author_meta($meta_key, $user->ID ) )." >Vatican City</option>
<option value='Venezuela' ".selected( "Venezuela", get_the_author_meta($meta_key, $user->ID ) )." >Venezuela</option>
<option value='Vietnam' ".selected( "Vietnam", get_the_author_meta($meta_key, $user->ID ) )." >Vietnam</option>
<option value='Virgin Islands, British' ".selected( "Virgin Islands, British", get_the_author_meta($meta_key, $user->ID ) )." >Virgin Islands, British</option>
<option value='Virgin Islands, U.S.' ".selected( "Virgin Islands, U.S.", get_the_author_meta($meta_key, $user->ID ) )." >Virgin Islands, U.S.</option>
<option value='Yemen' ".selected( "Yemen", get_the_author_meta($meta_key, $user->ID ) )." >Yemen</option>
<option value='Zambia' ".selected( "Zambia", get_the_author_meta($meta_key, $user->ID ) )." >Zambia</option>
<option value='Zimbabwe' ".selected( "Zimbabwe", get_the_author_meta($meta_key, $user->ID ) )." >Zimbabwe</option>";
 
	return $return_data_selection;
}
function gravity_billing_profile_fields( $user ) { ?>

  <h3>Gravity Billing Information</h3>
    <table class="form-table">
        <tr>
            <th><label for="gtv_company_name">Company Name</label></th>
            <td>
                <input type="text" name="gtv_company_name" id="gtv_company_name" value="<?php echo esc_attr( get_the_author_meta( 'gtv_company_name', $user->ID ) ); ?>" class="regular-text" /><br/>
                <span class="description">Please enter your Company Name.</span>
            </td>
        </tr>
		<tr>
            <th><label for="gtv_building_name">Building Name or Number</label></th>
            <td>
                <input type="text" name="gtv_building_name" id="gtv_building_name" value="<?php echo esc_attr( get_the_author_meta( 'gtv_building_name', $user->ID ) ); ?>" class="regular-text" /><br/>
                <span class="description">Please enter your Building Name or Number.</span>
            </td>
        </tr>
		<tr>
            <th><label for="gtv_addres_line_one">Address Line 1</label></th>
            <td>
                <input type="text" name="gtv_addres_line_one" id="gtv_addres_line_one" value="<?php echo esc_attr( get_the_author_meta( 'gtv_addres_line_one', $user->ID ) ); ?>" class="regular-text" /><br/>
                <span class="description">Please enter your Address Line 1.</span>
            </td>
        </tr>
		<tr>
            <th><label for="gtv_addres_line_two">Address Line 2</label></th>
            <td>
                <input type="text" name="gtv_addres_line_two" id="gtv_addres_line_two" value="<?php echo esc_attr( get_the_author_meta( 'gtv_addres_line_two', $user->ID ) ); ?>" class="regular-text" /><br/>
                <span class="description">Please enter your Address Line 2.</span>
            </td>
        </tr>
			
			<tr>
            <th><label for="gtv_city_state">City / State</label></th>
            <td>
                <input type="text" name="gtv_city_state" id="gtv_city_state" value="<?php echo esc_attr( get_the_author_meta( 'gtv_city_state', $user->ID ) ); ?>" class="regular-text" /><br/>
                <span class="description">Please enter your City / State.</span>
            </td>
        </tr>
		<tr>
            <th><label for="gtv_postcode_zipcode">Postcode / Zipcode</label></th>
            <td>
                <input type="text" name="gtv_postcode_zipcode" id="gtv_postcode_zipcode" value="<?php echo esc_attr( get_the_author_meta( 'gtv_postcode_zipcode', $user->ID ) ); ?>" class="regular-text" /><br/>
                <span class="description">Please enter your Postcode / Zipcode.</span>
            </td>
        </tr>
			<tr> <th scope="row"><label>Billing Country</label ></th>
			 <td>
			  <select name="gtv_billing_country">
				<?php printf(  $this->country_option($user)); ?>
				</select>
		<br />
			  <span class="description">Please enter your Billing Country.</span>
			 </td>
			</tr>
				
    </table>
<?php
}


function save_gravity_extra_profile_fields( $user_id ) {

	
    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

	//Account Details 
    update_usermeta( $user_id, 'gtv_first_name', $_POST['gtv_first_name'] );
    update_usermeta( $user_id, 'gtv_last_name', $_POST['gtv_last_name'] );
    update_usermeta( $user_id, 'gtv_billing_country', $_POST['gtv_billing_country'] );
    update_usermeta( $user_id, 'phone_number', $_POST['phone_number'] );
    update_usermeta( $user_id, 'gtv_email', $_POST['gtv_email'] );
   
 
	//update_user_option( $user_id, 'email',  $_POST['gtv_email'],false);
    update_usermeta( $user_id, 'gtv_websites', $_POST['gtv_websites'] );
	//Billing Information
    update_usermeta( $user_id, 'gtv_company_name', $_POST['gtv_company_name'] );
    update_usermeta( $user_id, 'gtv_building_name', $_POST['gtv_building_name'] );
    update_usermeta( $user_id, 'gtv_addres_line_one', $_POST['gtv_addres_line_one'] );
    update_usermeta( $user_id, 'gtv_addres_line_two', $_POST['gtv_addres_line_two'] );
    update_usermeta( $user_id, 'gtv_city_state', $_POST['gtv_city_state'] );
    update_usermeta( $user_id, 'gtv_postcode_zipcode', $_POST['gtv_postcode_zipcode'] );
    update_usermeta( $user_id, 'gtv_coutry', $_POST['gtv_coutry'] );
	
	
		// Force update our username (user_login)


		
}

}

if( is_admin() )
    $my_settings_page = new MySettingsPage()
?>