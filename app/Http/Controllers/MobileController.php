<?php
/**
 * @author Hermo Developer <dev@hermo.my>
 * @link http://www.hermo.my
 * @copyright Copyright (c) Hermo Creative Sdn. Bhd.
 */

namespace App\Http\Controllers;

/**
 * Class MobileController
 * @package App\Http\Controllers
 */
class MobileController extends Controller
{
    const username = 'sl5310';
    const password = 'sl5310520';

    /*
    * Your phone number, including country code, i.e. +44123123123 in this case:
    */
    const phoneNumber = '60167785310';
    const url = 'https://bulksms.vsms.net/eapi/submission/send_sms/2/2.0';

    const MESSAGE_TAKEN = 'pill already taken';
    const MESSAGE_REMINDER = 'xxxxx';
    const MESSAGE_RESTOCK = 'less than 20 percentage';
    const MESSAGE_OOS = 'oos';

    /**
     * Send Message
     * @param $message
     */
    public function send($message)
    {
        $post_body = $this->unicode_sms(self::username, self::password, $message, self::phoneNumber );
        $result = $this->send_message( $post_body, self::url);
        if( $result['success'] ) {
            print_r( $this->formatted_server_response( $result ) );
        }
        else {
            print_r( $this->formatted_server_response( $result ) );
        }
    }

    /**
     * @param $result
     * @return string
     */
    protected function formatted_server_response( $result )
    {
        $this_result = "";

        if ($result['success']) {
            $this_result .= "Success: batch ID " .$result['api_batch_id']. "API message: ".$result['api_message']. "\nFull details " .$result['details'];
        }
        else {
            $this_result .= "Fatal error: HTTP status " .$result['http_status_code']. ", API status " .$result['api_status_code']. " API message " .$result['api_message']. " full details " .$result['details'];

            if ($result['transient_error']) {
                $this_result .=  "This is a transient error - you should retry it in a production environment";
            }
        }
        return $this_result;
    }

    protected function unicode_sms ( $username, $password, $message, $msisdn )
    {
        $post_fields = array (
            'username' => $username,
            'password' => $password,
            'message'  => $this->string_to_utf16_hex( $message ),
            'msisdn'   => $msisdn,
            'dca'      => '16bit'
        );
        return $this->make_post_body($post_fields);
    }

    protected function string_to_utf16_hex( $string )
    {
        return bin2hex(mb_convert_encoding($string, "UTF-16", "UTF-8"));
    }

    /**
     * @param $post_fields
     * @return string
     */
    protected function make_post_body($post_fields)
    {
        $stop_dup_id = $this->make_stop_dup_id();
        if ($stop_dup_id > 0) {
            $post_fields['stop_dup_id'] = $this->make_stop_dup_id();
        }
        $post_body = '';
        foreach( $post_fields as $key => $value ) {
            $post_body .= urlencode( $key ).'='.urlencode( $value ).'&';
        }
        $post_body = rtrim( $post_body,'&' );

        return $post_body;
    }

    /**
     * @return int
     */
    protected function make_stop_dup_id()
    {
        return 0;
    }

    protected function send_message( $post_body, $url )
    {
        /*
        * Do not supply $post_fields directly as an argument to CURLOPT_POSTFIELDS,
        * despite what the PHP documentation suggests: cUrl will turn it into in a
        * multipart formpost, which is not supported:
        */

        $ch = curl_init( );
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_body );
        // Allowing cUrl funtions 20 second to execute
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 20 );
        // Waiting 20 seconds while trying to connect
        curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 20 );

        $response_string = curl_exec( $ch );
        $curl_info = curl_getinfo( $ch );

        $sms_result = array();
        $sms_result['success'] = 0;
        $sms_result['details'] = '';
        $sms_result['transient_error'] = 0;
        $sms_result['http_status_code'] = $curl_info['http_code'];
        $sms_result['api_status_code'] = '';
        $sms_result['api_message'] = '';
        $sms_result['api_batch_id'] = '';

        if ( $response_string == FALSE ) {
            $sms_result['details'] .= "cURL error: " . curl_error( $ch ) . "\n";
        } elseif ( $curl_info[ 'http_code' ] != 200 ) {
            $sms_result['transient_error'] = 1;
            $sms_result['details'] .= "Error: non-200 HTTP status code: " . $curl_info[ 'http_code' ] . "\n";
        }
        else {
            $sms_result['details'] .= "Response from server: $response_string\n";
            $api_result = explode( '|', $response_string );
            $status_code = $api_result[0];
            $sms_result['api_status_code'] = $status_code;
            $sms_result['api_message'] = $api_result[1];
            if ( count( $api_result ) != 3 ) {
                $sms_result['details'] .= "Error: could not parse valid return data from server.\n" . count( $api_result );
            } else {
                if ($status_code == '0') {
                    $sms_result['success'] = 1;
                    $sms_result['api_batch_id'] = $api_result[2];
                    $sms_result['details'] .= "Message sent - batch ID $api_result[2]\n";
                }
                else if ($status_code == '1') {
                    # Success: scheduled for later sending.
                    $sms_result['success'] = 1;
                    $sms_result['api_batch_id'] = $api_result[2];
                }
                else {
                    $sms_result['details'] .= "Error sending: status code [$api_result[0]] description [$api_result[1]]\n";
                }
            }
        }
        curl_close( $ch );

        return $sms_result;
    }
}