<?php

class SEJOLICENSE {

    /**
     * URL Request
     * @since   1.0.0
     * @var     string
     */
    protected static $url      = '';

    /**
     * License key
     * @since   1.0.0
     * @var     string
     */
    protected static $license  = '';

    /**
     * User sejoli membership email address
     * @since   1.0.0
     * @var     string
     */
    protected static $email    = '';

    /**
     * User sejoli membership password
     * @since   1.0.0
     * @var     string
     */
    protected static $password = '';

    /**
     * String to be registered or checked
     * @since   1.0.0
     * @var     string
     */
    protected static $string   = '';

    /**
     * Set respond
     * @since   1.0.0
     * @var     array
     */
    protected static $respond;

    /**
     * Construction
     */
    public function __construct() {

    }

    /**
     * Set URL target
     * @param  string $url
     */
    static public function set_url($url) {

        self::$url = esc_url_raw($url);

        return new static;
    }

    /**
     * Set license
     * @param  string license
     */
    static public function set_license($license) {

        self::$license = sanitize_text_field($license);

        return new static;
    }

    /**
     * Set user email address
     * @param  string $url
     */
    static public function set_email($email) {

        self::$email = sanitize_email($email);

        return new static;
    }

    /**
     * Set license
     * @param  string $license
     */
    static public function set_password($password) {

        self::$password = sanitize_text_field($password);

        return new static;
    }

    /**
     * Set string
     * @param  string $string
     */
    static public function set_string($string) {

        self::$string = sanitize_text_field($string);

        return new static;
    }

    /**
     * Register license to sejoli membership
     * @since   1.0.0
     */
    static public function register() {

        $post_data = array(
            'user_email' => self::$email,
            'user_pass'  => self::$password,
            'license'    => self::$license,
            'string'     => self::$string
        );

        $request_url = self::$url;
        $response    = wp_remote_post($request_url, array(
                         'timeout' => 120,
                         'body'    => $post_data
                       ));

        if(is_wp_error($response)) :

            return array(
                'valid' => false,
                'messages'  => $response->get_error_messages()
            );

		else :
			$json_result   = json_decode(wp_remote_retrieve_body($response), true);
			$response_code = intval(wp_remote_retrieve_response_code($response));

			if(200 === $response_code) :

                return array(
                    'valid' => true,
                    'messages'  => $json_result['messages']
                );

            else :
                return array(
                    'valid'    => false,
                    'messages' => array(
                        sprintf( __('Error response code : %s. Tidak bisa menghubungi server lisensi', 'sejoli'), $response_code )
                    )
                );

            endif;

        endif;

    }

    /**
     * Check license to sejoli membership
     * @since   1.0.0
     */
    static public function check() {

        $post_data = array(
            'license'    => self::$license,
            'string'     => self::$string
        );

        $request_url   = add_query_arg($post_data, self::$url);
		$response      = wp_remote_get($request_url);
		$json_result   = json_decode(wp_remote_retrieve_body($response), true);
		$response_code = (int) wp_remote_retrieve_response_code($response);

		if(200 === $response_code && isset($json_result['valid'])) :

            if(false !== boolval($json_result['valid'])) :

                return array(
                    'valid'    => true,
                    'messages' => array(
                        __('Lisensi valid', 'sejoli')
                    )
                );

            else :

                return array(
                    'valid'    => false,
                    'messages' => array( $json_result['message'] )
                );

            endif;

		else :

            return array(
                'valid' => false,
                'messages' => array($response['response']['message'])
            );

		endif;

    }
}
