<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PatientController extends CI_Controller {

    public $twilioAccountSid = '';
    public $twilioApiKey = '';
    public $twilioApiSecret = '';
    public $outgoingApplicationSid = '';
    public $serviceSid = '';
    public $PatientAndriodPushCredentialSid = '';
    public $PatientiOSPushCredentialSid = '';
    public $app_state = '';

    public function __construct() {
        parent::__construct();
        $this->response = new stdClass();
        ini_set("display_errors", 0);
        error_reporting(0);
        $this->SetTimezone();
        $this->load->model('PatientModel');
        require_once $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/vendor/autoload.php';
        $this->twilioAccountSid = 'ACf1f5263f780f17cf70fcbf8356031c2e';
        $this->twilioApiKey = 'SK622195c2c3d543e0d05f873e02b210ef';
        $this->twilioApiSecret = 'ScNJPArn0hXCSSTUbpVICD6BGV87kmKg';

        $this->serviceSid = 'IS17041c3738a5bf5a8934d27a0f588d3f';
        $this->outgoingApplicationSid = 'AP524f6078754db4a98262aaa6e64ea7ea';
        $this->PatientAndriodPushCredentialSid = 'CRde827132be34f20bf75d73d3660cc9a9';
        $this->PatientiOSPushCredentialSid = 'CR5a2a69972940f6540c979b00aff5d8d0';
        $this->app_state = $this->GetProjectSettings();
        //$this->app_state = '';


        if ($_POST['lang'] == 'en')
            $this->lang->load('message', 'english');
        else if ($_POST['lang'] == 'ru')
            $this->lang->load('message', 'russian');
        else
            $this->lang->load('message', 'english');
    }

    public function GetProjectSettings() {
        $project_settings = $this->Custom->get_where('project_settings', array('id' => 1));
        return $project_settings[0]->app_state;
    }

    public function UpdateProjectSettings() {
        $app_state_val = $_POST['app_state'];
        $update_status = $this->Custom->update_where('project_settings', array('app_state' => $app_state_val), array('id' => 1));
        if ($update_status) {
            $this->response->success = 200;
            $this->response->message = "Project app state updated successfully.";
            $this->response->data = $app_state_val;
            die(json_encode($this->response));
        } else {
            $this->response->success = 202;
            $this->response->message = "Something went wrong.";
            die(json_encode($this->response));
        }
    }

    public function index() {
        echo $this->lang->line('welcome_message');
    }

    public function Test() {
        date_default_timezone_set("Asia/Kolkata");
        $x = strtotime(date('H:i', strtotime("13:00")));
        echo $x;
        echo "<br>";
        echo date('H:i', $x);
    }

    public function SetTimezone() {
        $user_id = ($this->input->post('user_id', TRUE)) ? $this->input->post('user_id', TRUE) : '';
        $timezone = "UTC";
        if (!empty($user_id)) {
            $users_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'patient'));
            if (!empty($users_record)) {
                if ($users_record[0]->timezone != '') {
                    $timezone = $users_record[0]->timezone;
                }
            }
        }
        date_default_timezone_set($timezone);
    }

    public function SendMail($email, $subject, $content) {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/vendor/phpmailer/phpmailer/src/OAuth.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/vendor/phpmailer/phpmailer/src/SMTP.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/vendor/phpmailer/phpmailer/src/POP3.php';
        require_once $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/vendor/phpmailer/phpmailer/src/Exception.php';
        //$email = 'sfs.kirti17@gmail.com';
        //$name = 'Kirti';
        //$subject = 'Test Subject';
        //$content = '<html><body><h1>This is testing email.</h1></body></html>';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        //$mail->SMTPDebug = 3;
        $mail->isSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = "email-smtp.us-east-1.amazonaws.com";
        $mail->SMTPAuth = true;
        $mail->Username = "AKIAJ3SG7AN5IGHHC7EA";
        $mail->Password = "AhdaUtVInWgg34wAcxJ+mfZ7gwUTnek6MXIyMHHIXGwU";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;
        $mail->From = "zumcare@gmail.com";
        $mail->FromName = "Zumcare";
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $content;
        $mail->send();
    }

    public function GetLanguage() {
        $language_record = $this->Custom->get_where('language');
        if ($language_record) {
            $this->response->success = 200;
            $this->response->message = $this->lang->line('language_list');
            $this->response->data = $language_record;
            die(json_encode($this->response));
        } else {
            $this->response->success = 205;
            $this->response->message = $this->lang->line('language_list_error');
            die(json_encode($this->response));
        }
    }

    public function GetFeedCategory() {
        $feed_category_record = $this->Custom->get_where('feed_category');
        if ($feed_category_record) {
            if ($_POST['lang'] == 'en') {
                foreach ($feed_category_record as $row) {
                    $ret_data[] = array(
                        'id' => $row->id,
                        'category_name' => $row->category_name_en,
                        'created_at' => $row->created_at
                    );
                }
            } else if ($_POST['lang'] == 'ru') {
                foreach ($feed_category_record as $row) {
                    $ret_data[] = array(
                        'id' => $row->id,
                        'category_name' => $row->category_name_ru,
                        'created_at' => $row->created_at
                    );
                }
            } else {
                foreach ($feed_category_record as $row) {
                    $ret_data[] = array(
                        'id' => $row->id,
                        'category_name' => $row->category_name_en,
                        'created_at' => $row->created_at
                    );
                }
            }
            $this->response->success = 200;
            $this->response->message = $this->lang->line('feed_category_list');
            $this->response->data = $ret_data;
            die(json_encode($this->response));
        } else {
            $this->response->success = 205;
            $this->response->message = $this->lang->line('feed_list_error');
            die(json_encode($this->response));
        }
    }

    public function GetSubSpecility() {
        $specility_id = (isset($_POST['specility_id']) && !empty($_POST['specility_id'])) ? $_POST['specility_id'] : "";
        if (!empty($specility_id)) {
            $specility_record = $this->Custom->get_where('specility', array('parent_id' => $specility_id));
            if ($specility_record) {
                $this->response->success = 200;
                $this->response->message = 'Sub specility List.';
                $this->response->data = $specility_record;
                die(json_encode($this->response));
            } else {
                $this->response->success = 205;
                $this->response->message = 'There is no record for sub speciality.';
                die(json_encode($this->response));
            }
        } else {
            $specility_record = $this->Custom->get_where('specility', array('parent_id !=' => 0));
            if ($specility_record) {
                if ($_POST['lang'] == 'en') {
                    foreach ($specility_record as $row) {
                        $ret_data[] = array(
                            'id' => $row->id,
                            'specility_name' => $row->specility_name,
                            'created_at' => $row->created_at
                        );
                    }
                } else if ($_POST['lang'] == 'ru') {
                    foreach ($specility_record as $row) {
                        $ret_data[] = array(
                            'id' => $row->id,
                            'specility_name' => $row->specility_name_ru,
                            'created_at' => $row->created_at
                        );
                    }
                } else {
                    foreach ($specility_record as $row) {
                        $ret_data[] = array(
                            'id' => $row->id,
                            'specility_name' => $row->specility_name,
                            'created_at' => $row->created_at
                        );
                    }
                }

                $this->response->success = 200;
                $this->response->message = 'Sub specility List.';
                $this->response->data = $ret_data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 205;
                $this->response->message = 'There is no record for sub speciality.';
                die(json_encode($this->response));
            }
        }
    }

    public function GetSpecility() {
        $specility_record = $this->Custom->get_where('specility', array('parent_id' => 0));
        if ($specility_record) {
            $specility_data = array();
            $result = $this->PatientModel->SearchDoctors();
            if ($result) {
                $specility_ids = array();
                foreach ($specility_record as $row) {
                    $doctor_data = array();
                    $speciality = explode(',', $row->id);
                    foreach ($result as $key => $val) {
                        if ($val->activate_status == 1) {
                            $specialityArr = "";
                            $specialityArr = explode(',', $val->speciality);
                            if (array_intersect($specialityArr, $speciality)) {
                                if (!in_array($row->id, $specility_ids))
                                    $specility_ids[] = $row->id;
                            }
                        }
                    }
                }
                if (!empty($specility_ids)) {
                    foreach ($specility_ids as $val) {
                        $specility_rec = $this->Custom->get_where('specility', array('parent_id' => 0, 'id' => $val));
                        if ($_POST['lang'] == 'en') {
                            $specility_data[] = array(
                                'id' => $specility_rec[0]->id,
                                'specility_name' => $specility_rec[0]->specility_name,
                                'created_at' => $specility_rec[0]->created_at
                            );
                        } else if ($_POST['lang'] == 'ru') {
                            $specility_data[] = array(
                                'id' => $specility_rec[0]->id,
                                'specility_name' => $specility_rec[0]->specility_name_ru,
                                'created_at' => $specility_rec[0]->created_at
                            );
                        } else {
                            $specility_data[] = array(
                                'id' => $specility_rec[0]->id,
                                'specility_name' => $specility_rec[0]->specility_name,
                                'created_at' => $specility_rec[0]->created_at
                            );
                        }
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('speciality_list');
                    $this->response->data = $specility_data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('speciality_list_error');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 205;
                $this->response->message = $this->lang->line('speciality_list_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 205;
            $this->response->message = 'There is no record for speciality.';
            die(json_encode($this->response));
        }
    }

    public function GetDiseases() {
        $diseases_record = $this->Custom->get_where('diseases');
        if ($diseases_record) {
            $this->response->success = 200;
            $this->response->message = $this->lang->line('diseases_list');
            $this->response->data = $diseases_record;
            die(json_encode($this->response));
        } else {
            $this->response->success = 205;
            $this->response->message = $this->lang->line('diseases_list_error');
            die(json_encode($this->response));
        }
    }

    public function GetCities() {
        $country_code = (isset($_POST['country_code']) && !empty($_POST['country_code'])) ? $_POST['country_code'] : "";
        if (!empty($country_code)) {
            $city_record = $this->Custom->GetCitiesForCountry($country_code);
            if (!empty($city_record)) {
                $this->response->success = 200;
                $this->response->message = $this->lang->line('city_list');
                $this->response->data = $city_record;
                die(json_encode($this->response));
            } else {
                $this->response->success = 205;
                $this->response->message = $this->lang->line('city_list_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('country_code_error');
            die(json_encode($this->response));
        }
    }

    public function Login() {
        $login_type = ($this->input->post('login_type', TRUE)) ? $this->input->post('login_type', TRUE) : 'manual';
        $comet_chat_id = (isset($_POST['comet_chat_id']) && !empty($_POST['comet_chat_id'])) ? $_POST['comet_chat_id'] : "";
        $name = (isset($_POST['name']) && !empty($_POST['name'])) ? $_POST['name'] : "";
        $email = ($this->input->post('email', TRUE)) ? $this->input->post('email', TRUE) : '';
        $country_code = ($this->input->post('country_code', TRUE)) ? $this->input->post('country_code', TRUE) : '';
        $phone_number = ($this->input->post('phone_number', TRUE)) ? $this->input->post('phone_number', TRUE) : '';
        $latitude = ($this->input->post('latitude', TRUE)) ? $this->input->post('latitude', TRUE) : '';
        $longitude = ($this->input->post('longitude', TRUE)) ? $this->input->post('longitude', TRUE) : '';
        $device_token = ($this->input->post('device_token', TRUE)) ? $this->input->post('device_token', TRUE) : '';
        $voip_device_token = ($this->input->post('voip_device_token', TRUE)) ? $this->input->post('voip_device_token', TRUE) : '';
        $device_type = ($this->input->post('device_type', TRUE)) ? $this->input->post('device_type', TRUE) : '';
        $timezone = ($this->input->post('timezone', TRUE)) ? $this->input->post('timezone', TRUE) : '';
        if ($timezone != '')
            date_default_timezone_set($timezone);
        $created_at = strtotime(date("Y-m-d H:i:s"));
        if (isset($login_type) && !empty($login_type)) {
            switch ($login_type):
                case 'manual':
                    if (!empty($name) && !empty($email) && !empty($phone_number) && !empty($country_code)) {
                        $user_data = $this->Custom->get_where('users', array('phone_number' => $phone_number, 'role' => 'patient'));
                        if ($user_data) {
                            /* if ($user_data[0]->status != 1 && $user_data[0]->number_verified != 1) {
                              $this->response->success = 204;
                              $this->response->message = 'Your email is not verified.';
                              die(json_encode($this->response));
                              } */
                            if ($email == 'sfs.gurwinder17@gmail.com')
                                $otp = '123456';
                            else
                                $otp = GenerateOTP(6);

                            $user_id = $user_data[0]->id;
                            if ($user_data[0]->user_uid == "")
                                $user_uid = $user_id . rand(1111, 9999);
                            else
                                $user_uid = $user_data[0]->user_uid;
                            $update_arr = array(
                                'user_uid' => $user_uid,
                                'comet_chat_id' => $comet_chat_id,
                                'name' => $name,
                                'email' => $email,
                                'country_code' => $country_code,
                                'phone_number' => $phone_number,
                                'role' => 'patient',
                                'device_type' => $device_type,
                                'device_token' => $device_token,
                                'voip_device_token' => $voip_device_token,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'verification_otp' => $otp,
                                'authentication_token' => '',
                                'number_verified' => 0,
                                'status' => 1,
                                'activate_status' => 1,
                                'timezone' => $timezone,
                                'created_at' => $created_at
                            );
                            $this->Custom->update_where('users', $update_arr, array('id' => $user_id));
                            //send mail
                            $mail_data = array('otp' => $otp, 'Hello' => $this->lang->line('Hello'), 'verification_content' => $this->lang->line('verification_otp'), 'Thanks' => $this->lang->line('Thanks'), 'team' => $this->lang->line('team'));
                            $content = $this->load->view('mail/confirmation_mail', $mail_data, TRUE);
                            $subject = $subject = $this->lang->line('confirm_subject');
                            $this->SendMail($email, $subject, $content);

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('user_register');
                            $this->response->data = array('user_id' => $user_id, 'phone_number' => $phone_number);
                            die(json_encode($this->response));
                        } else {
                            if ($email == 'sfs.gurwinder17@gmail.com')
                                $otp = '123456';
                            else
                                $otp = GenerateOTP(6);
                            $insert_arr = array(
                                'comet_chat_id' => $comet_chat_id,
                                'name' => $name,
                                'email' => $email,
                                'country_code' => $country_code,
                                'phone_number' => $phone_number,
                                'role' => 'patient',
                                'device_type' => $device_type,
                                'device_token' => $device_token,
                                'voip_device_token' => $voip_device_token,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'verification_otp' => $otp,
                                'authentication_token' => '',
                                'number_verified' => 0,
                                'status' => 1,
                                'activate_status' => 1,
                                'timezone' => $timezone,
                                'created_at' => $created_at
                            );
                            $insert_id = $this->Custom->insert_data('users', $insert_arr);
                            if ($insert_id) {
                                //update user data
                                $user_uid = $insert_id . rand(1111, 9999);
                                $this->Custom->update_where('users', array('user_uid' => $user_uid), array('id' => $insert_id));
                                //update user data
                                $user_uid = $insert_id . rand(1111, 9999);
                                $this->Custom->update_where('users', array('user_uid' => $user_uid), array('id' => $insert_id));
                                $this->Custom->insert_data('patient_profile', array('user_id' => $insert_id, 'created_at' => $created_at));
                                $this->Custom->insert_data('patient_medical_lifestyle', array('user_id' => $insert_id, 'created_at' => $created_at));
                                $this->Custom->insert_data('patient_member', array('user_id' => $insert_id, 'name' => $name, 'relationship' => 'self', 'created_at' => $created_at));
//send mail
                                $mail_data = array('otp' => $otp, 'Hello' => $this->lang->line('Hello'), 'verification_content' => $this->lang->line('verification_otp'), 'Thanks' => $this->lang->line('Thanks'), 'team' => $this->lang->line('team'));
                                $content = $this->load->view('mail/confirmation_mail', $mail_data, TRUE);
                                $subject = $this->lang->line('verification_subject');
                                $this->SendMail($email, $subject, $content);

                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('user_register');
                                $this->response->data = array('user_id' => $insert_id, 'phone_number' => $phone_number);
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 202;
                                $this->response->message = $this->lang->line('went_wrong');
                                die(json_encode($this->response));
                            }
                        }
                    } else {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                    break;
            endswitch;
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('login_type');
            die(json_encode($this->response));
        }
    }

    public function VerifyOTP() {
        $user_id = ($this->input->post('user_id', TRUE)) ? $this->input->post('user_id', TRUE) : '';
        $phone_number = ($this->input->post('phone_number', TRUE)) ? $this->input->post('phone_number', TRUE) : '';
        $otp = ($this->input->post('otp', TRUE)) ? $this->input->post('otp', TRUE) : '';
        if (!empty($user_id) && !empty($phone_number) && !empty($otp)) {
            $user_data = $this->Custom->get_where('users', array('id' => $user_id, 'phone_number' => $phone_number, 'role' => 'patient', 'number_verified' => 0));
            if ($user_data) {
                if ($otp != $user_data[0]->verification_otp) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('otp_error');
                    die(json_encode($this->response));
                }
                $auth_token = GenerateRandomNumber(11);
                $update_arr = array(
                    'verification_otp' => '',
                    'authentication_token' => $auth_token,
                    'number_verified' => 1
                );
                $this->Custom->update_where('users', $update_arr, array('id' => $user_id));
//get data
                $patient_data = $this->PatientModel->GetPatientProfile($user_id);
                $patient_data[0]->profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : '';
                $patient_data[0]->allergies = ($patient_data[0]->allergies) ? explode(',', $patient_data[0]->allergies) : array();
                $patient_data[0]->current_medication = ($patient_data[0]->current_medication) ? explode(',', $patient_data[0]->current_medication) : array();
                $patient_data[0]->past_medication = ($patient_data[0]->past_medication) ? explode(',', $patient_data[0]->past_medication) : array();
                $patient_data[0]->diseases = ($patient_data[0]->diseases) ? explode(',', $patient_data[0]->diseases) : array();
                $patient_data[0]->surgeries = ($patient_data[0]->surgeries) ? explode(',', $patient_data[0]->surgeries) : array();
                $patient_data[0]->injuries = ($patient_data[0]->injuries) ? explode(',', $patient_data[0]->injuries) : array();
                $patient_data[0]->specialNeeds = ($patient_data[0]->specialNeeds) ? explode(',', $patient_data[0]->specialNeeds) : array();
                $patient_data[0]->bloodTransfusion = ($patient_data[0]->bloodTransfusion) ? explode(',', $patient_data[0]->bloodTransfusion) : array();

                $this->response->success = 200;
                $this->response->message = $this->lang->line('account_verified');
                $this->response->data = $patient_data[0];
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_phone_no_errror');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function Logout() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (isset($user_id) && !empty($user_id)) {
//check authentication
            $headers = apache_request_headers();
            $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
            if (empty($auth_record)) {
                $this->response->success = 404;
                $this->response->message = $this->lang->line('authentication_error');
                die(json_encode($this->response));
            }
            $user_record = $this->Custom->get_data('users', 'id', $user_id);
            if (isset($user_record) && !empty($user_record)) {
                $update_res = $this->Custom->update("users", array('device_type' => '', 'device_token' => '', 'authentication_token' => ''), 'id', $user_id);
                if ($update_res) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('logout');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetProfile() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_data = $this->PatientModel->GetPatientProfile($user_id);
                if ($patient_data) {
                    $patient_data[0]->profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                    $patient_data[0]->allergies = ($patient_data[0]->allergies) ? explode(',', $patient_data[0]->allergies) : array();
                    $patient_data[0]->current_medication = ($patient_data[0]->current_medication) ? explode(',', $patient_data[0]->current_medication) : array();
                    $patient_data[0]->past_medication = ($patient_data[0]->past_medication) ? explode(',', $patient_data[0]->past_medication) : array();
                    $patient_data[0]->diseases = ($patient_data[0]->diseases) ? explode(',', $patient_data[0]->diseases) : array();
                    $patient_data[0]->surgeries = ($patient_data[0]->surgeries) ? explode(',', $patient_data[0]->surgeries) : array();
                    $patient_data[0]->injuries = ($patient_data[0]->injuries) ? explode(',', $patient_data[0]->injuries) : array();
                    $patient_data[0]->specialNeeds = ($patient_data[0]->specialNeeds) ? explode(',', $patient_data[0]->specialNeeds) : array();
                    $patient_data[0]->bloodTransfusion = ($patient_data[0]->bloodTransfusion) ? explode(',', $patient_data[0]->bloodTransfusion) : array();

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('user_details');
                    $this->response->data = $patient_data[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('userid_error');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function UpdateProfile() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $update_type = ($this->input->post('update_type')) ? $this->input->post('update_type') : '';

        if (!empty($user_id) && !empty($update_type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                unset($_POST['lang']);
                switch ($update_type):
                    case 'personal':
                        $user_profile_record = $this->Custom->get_where('patient_profile', array('user_id' => $user_id));
                        if (isset($user_profile_record) && !empty($user_profile_record)) {
//upload profile image
                            if (!empty($_FILES['profile_image']['name'])) {
                                $name = $_FILES['profile_image']['name'];
                                $ext = end((explode(".", $name)));
                                $new_name = time() . '.' . $ext;
                                $config['file_name'] = $new_name;
                                $config['upload_path'] = PATIENT_PROFILE_PATH . '/';
                                $config['allowed_types'] = '*';
                                $config['overwrite'] = TRUE;
                                $config['remove_spaces'] = TRUE;

                                if (!is_dir($config['upload_path']))
                                    die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                                $this->load->library('upload', $config);
                                $this->upload->initialize($config);

                                if (!$this->upload->do_upload('profile_image')) {
                                    $this->response->success = 203;
                                    $this->response->message = $this->lang->line('image_not_upload');
                                    die(json_encode($this->response));
                                } else {
                                    if ($user_profile_record[0]->profile_image != "")
                                        unlink(PATIENT_PROFILE_PATH . '/' . $user_profile_record[0]->profile_image);
                                    $_POST['profile_image'] = $new_name;
                                }
                            }
                            unset($_POST['update_type']);

                            if (isset($_POST['name']) && !empty($_POST['name'])) {
                                $this->Custom->update_where('users', array('name' => $_POST['name']), array('id' => $user_id));
                            } else {
                                $this->Custom->update_where('patient_profile', $_POST, array('user_id' => $user_id));
                            }
                            if (isset($_POST['comet_chat_id']) && !empty($_POST['comet_chat_id'])) {
                                $this->Custom->update_where('users', array('comet_chat_id' => $_POST['comet_chat_id']), array('id' => $user_id));
                            } else {
                                $this->Custom->update_where('patient_profile', $_POST, array('user_id' => $user_id));
                            }
                            if (isset($_POST['gender']) && !empty($_POST['gender']))
                                $this->Custom->update_where('patient_member', array('gender' => $_POST['gender']), array('user_id' => $user_id, 'relationship' => 'self'));
                            if (isset($_POST['dob']) && !empty($_POST['dob']))
                                $this->Custom->update_where('patient_member', array('dob' => $_POST['dob']), array('user_id' => $user_id, 'relationship' => 'self'));
                            if (isset($_POST['height']) && !empty($_POST['height']))
                                $this->Custom->update_where('patient_member', array('height' => $_POST['height']), array('user_id' => $user_id, 'relationship' => 'self'));
                            if (isset($_POST['weight']) && !empty($_POST['weight']))
                                $this->Custom->update_where('patient_member', array('weight' => $_POST['weight']), array('user_id' => $user_id, 'relationship' => 'self'));
//get data
                            $patient_data = $this->PatientModel->GetPatientProfile($user_id);
                            $patient_data[0]->profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                            $patient_data[0]->allergies = ($patient_data[0]->allergies) ? explode(',', $patient_data[0]->allergies) : array();
                            $patient_data[0]->current_medication = ($patient_data[0]->current_medication) ? explode(',', $patient_data[0]->current_medication) : array();
                            $patient_data[0]->past_medication = ($patient_data[0]->past_medication) ? explode(',', $patient_data[0]->past_medication) : array();
                            $patient_data[0]->diseases = ($patient_data[0]->diseases) ? explode(',', $patient_data[0]->diseases) : array();
                            $patient_data[0]->surgeries = ($patient_data[0]->surgeries) ? explode(',', $patient_data[0]->surgeries) : array();
                            $patient_data[0]->injuries = ($patient_data[0]->injuries) ? explode(',', $patient_data[0]->injuries) : array();
                            $patient_data[0]->specialNeeds = ($patient_data[0]->specialNeeds) ? explode(',', $patient_data[0]->specialNeeds) : array();
                            $patient_data[0]->bloodTransfusion = ($patient_data[0]->bloodTransfusion) ? explode(',', $patient_data[0]->bloodTransfusion) : array();

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('profile_updated');
                            $this->response->data = $patient_data[0];
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('userid_error');
                            die(json_encode($this->response));
                        }

                        break;

                    case 'medical':
                        $user_profile_record = $this->Custom->get_where('patient_medical_lifestyle', array('user_id' => $user_id));
                        if (isset($user_profile_record) && !empty($user_profile_record)) {
                            unset($_POST['update_type']);
                            $this->Custom->update_where('patient_medical_lifestyle', $_POST, array('user_id' => $user_id));

//get data
                            $patient_data = $this->PatientModel->GetPatientProfile($user_id);
                            $patient_data[0]->profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                            $patient_data[0]->allergies = ($patient_data[0]->allergies) ? explode(',', $patient_data[0]->allergies) : array();
                            $patient_data[0]->current_medication = ($patient_data[0]->current_medication) ? explode(',', $patient_data[0]->current_medication) : array();
                            $patient_data[0]->past_medication = ($patient_data[0]->past_medication) ? explode(',', $patient_data[0]->past_medication) : array();
                            $patient_data[0]->diseases = ($patient_data[0]->diseases) ? explode(',', $patient_data[0]->diseases) : array();
                            $patient_data[0]->surgeries = ($patient_data[0]->surgeries) ? explode(',', $patient_data[0]->surgeries) : array();
                            $patient_data[0]->injuries = ($patient_data[0]->injuries) ? explode(',', $patient_data[0]->injuries) : array();
                            $patient_data[0]->specialNeeds = ($patient_data[0]->specialNeeds) ? explode(',', $patient_data[0]->specialNeeds) : array();
                            $patient_data[0]->bloodTransfusion = ($patient_data[0]->bloodTransfusion) ? explode(',', $patient_data[0]->bloodTransfusion) : array();

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('profile_updated');
                            $this->response->data = $patient_data[0];
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('userid_error');
                            die(json_encode($this->response));
                        }
                        break;
                endswitch;
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DoctorDetails() {
        $this->load->model('DoctorModel');
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        if (!empty($doctor_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $where = array('users.id' => $doctor_id, 'users.role' => 'doctor', 'activate_status' => 1);
                $doctor_data = $this->PatientModel->SearchDoctors($where);
                if (!empty($doctor_data)) {
                    if ($doctor_data[0]->speciality != '') {
                        $speciality = explode(',', $doctor_data[0]->speciality);
                        foreach ($speciality as $spe) {
                            $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                            if ($specility_record) {
                                if ($_POST['lang'] == 'en') {
                                    $speciality_data[] = array(
                                        'specility_id' => $spe,
                                        'specility_name' => $specility_record[0]->specility_name
                                    );
                                } else if ($_POST['lang'] == 'ru') {
                                    $speciality_data[] = array(
                                        'specility_id' => $spe,
                                        'specility_name' => $specility_record[0]->specility_name_ru
                                    );
                                } else {
                                    $speciality_data[] = array(
                                        'specility_id' => $spe,
                                        'specility_name' => $specility_record[0]->specility_name
                                    );
                                }
                            } else {
                                $speciality_data[] = array(
                                    'specility_id' => $spe,
                                    'specility_name' => ""
                                );
                            }
                        }
                        $doctor_data[0]->speciality = $speciality_data;
                    } else {
                        $doctor_data[0]->speciality = array();
                    }
                    if ($doctor_data[0]->diseases != '') {
                        $diseases = explode(',', $doctor_data[0]->diseases);
                        foreach ($diseases as $des) {
                            $diseases_record = $this->Custom->get_where('diseases', array('id' => $des));
                            if ($diseases_record) {
                                $diseases_data[] = array(
                                    'diseases_id' => $des,
                                    'diseases_name' => $diseases_record[0]->diseases_name
                                );
                            } else {
                                $diseases_data[] = array(
                                    'diseases_id' => $des,
                                    'diseases_name' => ""
                                );
                            }
                        }
                        $doctor_data[0]->diseases = $diseases_data;
                    } else {
                        $doctor_data[0]->diseases = array();
                    }
                    if ($doctor_data[0]->treatment != '') {
                        $treatment = explode(',', $doctor_data[0]->treatment);
                        foreach ($treatment as $tre) {
                            $treatment_record = $this->Custom->get_where('treatment', array('id' => $tre));
                            if ($treatment_record) {
                                $treatment_data[] = array(
                                    'treatment_id' => $tre,
                                    'treatment_name' => $treatment_record[0]->name
                                );
                            } else {
                                $treatment_data[] = array(
                                    'treatment_id' => $tre,
                                    'treatment_name' => ""
                                );
                            }
                        }
                        $doctor_data[0]->treatment = $treatment_data;
                    } else {
                        $doctor_data[0]->treatment = array();
                    }
                    if ($doctor_data[0]->language != '') {
                        $language = explode(',', $doctor_data[0]->language);
                        foreach ($language as $lan) {
                            $language_record = $this->Custom->get_where('language', array('id' => $lan));
                            if ($language_record) {
                                $language_data[] = array(
                                    'language_id' => $lan,
                                    'language_name' => $language_record[0]->name
                                );
                            } else {
                                $language_data[] = array(
                                    'language_id' => $lan,
                                    'language_name' => ""
                                );
                            }
                        }
                        $doctor_data[0]->language = $language_data;
                    } else {
                        $doctor_data[0]->language = array();
                    }
                    $education_details = GetDetails('doctor_education', array('user_id' => $doctor_id));
                    $consultation_details = GetDetails('consultation_settings', array('user_id' => $doctor_id));
                    $clinic_hours_details = $this->DoctorModel->GetClinicHours($doctor_id);
                    $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                    $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
                    $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
                    $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
                    $doctor_data[0]->signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";
//$doctor_data[0]->education_details = ($education_details) ? $education_details : array();
                    $doctor_data[0]->consultation_details = ($consultation_details) ? $consultation_details[0] : new stdClass();
                    $clinic_hours_data1 = array();
                    if ($clinic_hours_details) {
                        foreach ($clinic_hours_details as $row) {
                            $day_id[] = $row->day_id;
                        }
                        $day_ids = array_unique($day_id);
                        foreach ($day_ids as $day) {
                            $clinic_data = array();
                            $work_status = 0;
                            $clinic_hours_data = $this->DoctorModel->GetClinicHours($doctor_id);
                            foreach ($clinic_hours_data as $CH) {
                                if ($day == $CH->day_id) {
                                    $clinic_data[] = array(
                                        "id" => $CH->id,
                                        "user_id" => $CH->user_id,
                                        "day_id" => $CH->day_id,
                                        "start_hour" => $CH->start_hour,
                                        "end_hour" => $CH->end_hour,
                                        "working_status" => $CH->working_status
                                    );
                                }
                            }
                            $clinic_hours_data1[] = array(
                                'day_id' => $day,
                                'clinic_data' => $clinic_data
                            );
                        }
                    }

                    $doctor_data[0]->clinic_hours_details = ($clinic_hours_data1) ? $clinic_hours_data1 : array();
//calculate rating
                    $doctor_data[0]->rating = AverageRating($doctor_id, 'doctor');
                    $doctor_data[0]->rating_count = TotalRatingCount($doctor_id, 'doctor');
//get reviews
                    $review_record = $this->PatientModel->GetReviews($doctor_id, 'doctor', 2);
                    if ($review_record) {
                        foreach ($review_record as $RR) {
                            if (!empty($RR->sender_profile_image)) {
                                $RR->sender_profile_image = base_url() . PATIENT_PROFILE_URL . '/' . $RR->sender_profile_image;
                            }
                            $reviews[] = $RR;
                        }
                    }
                    $doctor_data[0]->reviews = (isset($reviews) && !empty($reviews)) ? $reviews : array();
                    $doctor_data[0]->additional_info = array(
                        'education_details' => ($education_details) ? $education_details : array(),
                    );
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('doctor_details');
                    ;
                    $this->response->data = $doctor_data[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    ;
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function ReviewAndRating() {
        $sender_id = ($this->input->post('sender_id')) ? $this->input->post('sender_id') : '';
        $receiver_id = ($this->input->post('receiver_id')) ? $this->input->post('receiver_id') : '';
        $rating = ($this->input->post('rating')) ? $this->input->post('rating') : 0;
        $review = ($this->input->post('review')) ? $this->input->post('review') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        if (!empty($sender_id) && !empty($receiver_id) && !empty($review) && !empty($appointment_id)) {
            $sender_record = $this->Custom->get_where('users', array('id' => $sender_id, 'status' => 1, 'role' => 'patient'));
            if (isset($sender_record) && !empty($sender_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $sender_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($sender_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($sender_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $receiver_record = $this->Custom->get_where('users', array('id' => $receiver_id, 'role' => 'doctor'));
                if (isset($receiver_record) && !empty($receiver_record)) {
                    $appointment = $this->Custom->get_where('appointment', array('id' => $appointment_id));
                    if (empty($appointment)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('invalid_appointment');
                        die(json_encode($this->response));
                    }
                    $insert_arr = array(
                        'type' => 'doctor',
                        'sender_id' => $sender_id,
                        'receiver_id' => $receiver_id,
                        'rating' => $rating,
                        'review' => $review,
                        'appointment_id' => $appointment_id,
                        'created_at' => strtotime(date("Y-m-d H:i:s"))
                    );
                    $insert_id = $this->Custom->insert_data('rating_and_reviews', $insert_arr);
                    if ($insert_id) {
                        $update_status = $this->Custom->update_where('appointment', array('status' => 7), array('id' => $appointment_id));
                        //send notification
                        define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
                        $notification_settings = $this->Custom->get_where('notification_settings', array('user_id' => $receiver_id));
                        $show_notification = 1;
                        if ($notification_settings[0]->review_rating != 1)
                            $show_notification = 0;

                        $message = $this->lang->line('review_msg');
                        if ($show_notification == 1) {
                            if ($receiver_record[0]->device_type == "android") {
                                if (!empty($receiver_record[0]->device_token)) {
                                    $registatoin_ids = array($receiver_record[0]->device_token);
                                    $message_data = array("message" => $message, 'notification_type' => 'rating', 'id' => $insert_id, 'patient_id' => $user_id);

                                    $url = 'https://fcm.googleapis.com/fcm/send';
                                    $fields = array(
                                        'registration_ids' => $registatoin_ids,
                                        'data' => $message_data,
                                    );
                                    $headers = array(
                                        'Authorization: key=' . API_KEY,
                                        'Content-Type: application/json'
                                    );

                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, $url);
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                                    $result = curl_exec($ch);
                                    $res_array = json_decode($result);
                                }
                            } else {
                                $app_state = $this->app_state;
                                //$app_state = "";
                                $deviceToken = $receiver_record[0]->device_token;
                                $body['aps'] = array(
                                    'alert' => array(
                                        'content-available' => 1,
                                        'body' => $message,
                                    ),
                                    'badge' => 1,
                                    'notification_type' => 'rating',
                                    'id' => $insert_id,
                                    'patient_id' => $user_id,
                                    'sound' => 'default',
                                );

                                $passphrase = '123456789';
                                $ctx = stream_context_create();
                                if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                    stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                                } else {
                                    stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                                }
                                stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                                if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                    $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                                } else {
                                    $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                                }
                                $payload = json_encode($body);
                                $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                                $result = fwrite($fp, $msg, strlen($msg));
                                fclose($fp);
                            }
                        }

                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('reviews_added');
                        die(json_encode($this->response));
                    } else {
                        $this->response->success = 202;
                        $this->response->message = $this->lang->line('went_wrong');
                        die(json_encode($this->response));
                    }
                } else {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('receiver_id_error');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('sender_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DoctorFilters() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $filter_type = ($this->input->post('filter_type')) ? $this->input->post('filter_type') : '';
        $speciality = ($this->input->post('speciality')) ? $this->input->post('speciality') : '';
        $diseases = ($this->input->post('diseases')) ? $this->input->post('diseases') : '';
        $keyword = ($this->input->post('keyword')) ? $this->input->post('keyword') : '';
        $availablity_filter = ($this->input->post('availablity_filter')) ? $this->input->post('availablity_filter') : '';
        $availablity = ($this->input->post('availablity')) ? $this->input->post('availablity') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $gender_ru = ($this->input->post('gender_ru')) ? $this->input->post('gender_ru') : '';
        $treatment = ($this->input->post('treatment')) ? $this->input->post('treatment') : '';
        $locality = ($this->input->post('locality')) ? $this->input->post('locality') : '';
        $locality_lat = ($this->input->post('locality_lat')) ? $this->input->post('locality_lat') : '';
        $locality_lng = ($this->input->post('locality_lng')) ? $this->input->post('locality_lng') : '';
        $language = ($this->input->post('language')) ? $this->input->post('language') : '';
        $rating_filter = ($this->input->post('rating_filter')) ? $this->input->post('rating_filter') : '';
        $price_type = ($this->input->post('price_type')) ? $this->input->post('price_type') : '';
        $price_filter = ($this->input->post('price_filter')) ? $this->input->post('price_filter') : '';
        $min_exp = ($this->input->post('min_exp')) ? $this->input->post('min_exp') : '5';
        $max_exp = ($this->input->post('max_exp')) ? $this->input->post('max_exp') : '5';
        if (!empty($filter_type) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if ($filter_type == 'category') {
                    $filter_details = $this->Custom->get_where('filter_details', array('user_id' => $user_id));
                    if (!empty($filter_details)) {
                        $this->Custom->update('filter_details', $_POST, 'id', $filter_details[0]->id);
                    } else {
                        $this->Custom->insert_data('filter_details', $_POST);
                    }
                }
                if ($filter_type == 'all') {
                    $filter_details = $this->Custom->get_where('filter_details', array('user_id' => $user_id));
                    if (!empty($filter_details)) {
                        $updateArr = array(
                            'filter_type' => '',
                            'lang' => '',
                            'locality' => '',
                            'locality_lat' => '',
                            'locality_lng' => '',
                            'language' => '',
                            'availablity_filter' => '',
                            'availablity' => '',
                            'gender' => '',
                            'gender_ru' => '',
                            'price_type' => '',
                            'price_filter' => '',
                            'rating_filter' => '',
                            'min_exp' => '',
                            'max_exp' => '',
                        );
                        $this->Custom->update('filter_details', $updateArr, 'id', $filter_details[0]->id);
                    }
                }
                switch ($filter_type):
                    case 'all':
                        $result = $this->PatientModel->SearchDoctors();
                        if ($result) {
                            foreach ($result as $val) {
                                if ($val->activate_status == 1 && $val->approve_status == 1) {
                                    $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                    $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                    $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                    $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                    $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                    $val->rating = AverageRating($val->user_id, 'doctor');
                                    $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                    $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));
                                    if ($val->speciality != '') {
                                        $speciality_data = array();
                                        $speciality = explode(',', $val->speciality);
                                        foreach ($speciality as $spe) {
                                            $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                            if ($specility_record) {
                                                if ($_POST['lang'] == 'en') {
                                                    $speciality_data[] = array(
                                                        'id' => $specility_record[0]->id,
                                                        'specility_name' => $specility_record[0]->specility_name
                                                    );
                                                } else if ($_POST['lang'] == 'ru') {
                                                    $speciality_data[] = array(
                                                        'id' => $specility_record[0]->id,
                                                        'specility_name' => $specility_record[0]->specility_name_ru
                                                    );
                                                } else {
                                                    $speciality_data[] = array(
                                                        'id' => $specility_record[0]->id,
                                                        'specility_name' => $specility_record[0]->specility_name
                                                    );
                                                }
                                            } else {
                                                $speciality_data[] = array(
                                                    'specility_id' => $spe,
                                                    'specility_name' => ""
                                                );
                                            }
                                        }
                                        $val->speciality = $speciality_data;
                                    } else {
                                        $val->speciality = array();
                                    }

                                    $doctor_data[] = array(
                                        'user_id' => $val->user_id,
                                        'role' => $val->role,
                                        'title' => $val->title,
                                        'name' => $val->name,
                                        'email' => $val->email,
                                        'country_code' => $val->country_code,
                                        'phone_number' => $val->phone_number,
                                        'latitude' => $val->latitude,
                                        'longitude' => $val->longitude,
                                        'status' => $val->status,
                                        'activate_status' => $val->activate_status,
                                        'created_at' => $val->created_at,
                                        'dob' => $val->dob,
                                        'gender' => $val->gender,
                                        'profile_image' => $val->profile_image,
                                        'experience' => $val->experience,
                                        'city' => $val->city,
                                        'consulation_fee' => $val->consulation_fee,
                                        'clinic_name' => $val->clinic_name,
                                        'clinic_phone_number' => $val->clinic_phone_number,
                                        'clinic_address' => $val->clinic_address,
                                        'clinic_city' => $val->clinic_city,
                                        'clinic_locality' => $val->clinic_locality,
                                        'clinic_pincode' => $val->clinic_pincode,
                                        'clinic_state' => $val->clinic_state,
                                        'clinic_lat' => $val->clinic_lat,
                                        'clinic_lng' => $val->clinic_lng,
                                        'availability' => $val->availability,
                                        'medical_registration_proof' => $val->medical_registration_proof,
                                        'photo_id_proof' => $val->photo_id_proof,
                                        'degree_proof' => $val->degree_proof,
                                        'signature' => $val->signature,
                                        'rating' => $val->rating,
                                        'rating_count' => $val->rating_count,
                                        'feature_status' => $val->feature_status,
                                        'feature_date' => $val->feature_date,
                                        'consultation_details' => ($consultation_details) ? $consultation_details[0] : new stdClass(),
                                        'speciality' => $val->speciality
                                    );

                                    /* End Here */
                                }
                            }
                            $filterData = array();
                            $filterData1 = array();
                            if ($doctor_data) {
                                foreach ($doctor_data as $DD) {
                                    if ($DD['feature_status'] == 1) {
                                        $feature_date[] = $DD['feature_date'];
                                        $filterData[] = $DD;
                                    } else {
                                        $filterData1[] = $DD;
                                    }
                                }
                                if (!empty($filterData)) {
                                    array_multisort($feature_date, SORT_DESC, $filterData);
                                    $return_data = array_merge($filterData, $filterData1);
                                } else {
                                    $return_data = $filterData1;
                                }
                            } else {
                                $return_data = array();
                            }
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('doctor_details');
                            $this->response->data = $return_data;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'other':
                        if (!empty($keyword)) {
                            $where = "users.name LIKE '%$keyword%' OR doctor_profile.clinic_name LIKE '%$keyword%'";
                            $result = $this->PatientModel->SearchDoctors($where);
                            if ($result) {
                                foreach ($result as $val) {
                                    if ($val->activate_status == 1 && $val->approve_status == 1) {
                                        $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                        $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                        $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                        $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                        $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                        $val->rating = AverageRating($val->user_id, 'doctor');
                                        $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                        $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));
                                        if ($val->speciality != '') {
                                            $speciality_data = array();
                                            $speciality = explode(',', $val->speciality);
                                            foreach ($speciality as $spe) {
                                                $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                                if ($specility_record) {
                                                    if ($_POST['lang'] == 'en') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    } else if ($_POST['lang'] == 'ru') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name_ru
                                                        );
                                                    } else {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    }
                                                } else {
                                                    $speciality_data[] = array(
                                                        'specility_id' => $spe,
                                                        'specility_name' => ""
                                                    );
                                                }
                                            }
                                            $val->speciality = $speciality_data;
                                        } else {
                                            $val->speciality = array();
                                        }
                                        $doctor_data[] = array(
                                            'user_id' => $val->user_id,
                                            'role' => $val->role,
                                            'title' => $val->title,
                                            'name' => $val->name,
                                            'email' => $val->email,
                                            'country_code' => $val->country_code,
                                            'phone_number' => $val->phone_number,
                                            'latitude' => $val->latitude,
                                            'longitude' => $val->longitude,
                                            'status' => $val->status,
                                            'activate_status' => $val->activate_status,
                                            'created_at' => $val->created_at,
                                            'dob' => $val->dob,
                                            'gender' => $val->gender,
                                            'profile_image' => $val->profile_image,
                                            'experience' => $val->experience,
                                            'city' => $val->city,
                                            'consulation_fee' => $val->consulation_fee,
                                            'clinic_name' => $val->clinic_name,
                                            'clinic_phone_number' => $val->clinic_phone_number,
                                            'clinic_address' => $val->clinic_address,
                                            'clinic_city' => $val->clinic_city,
                                            'clinic_locality' => $val->clinic_locality,
                                            'clinic_pincode' => $val->clinic_pincode,
                                            'clinic_state' => $val->clinic_state,
                                            'clinic_lat' => $val->clinic_lat,
                                            'clinic_lng' => $val->clinic_lng,
                                            'availability' => $val->availability,
                                            'medical_registration_proof' => $val->medical_registration_proof,
                                            'photo_id_proof' => $val->photo_id_proof,
                                            'degree_proof' => $val->degree_proof,
                                            'signature' => $val->signature,
                                            'rating' => $val->rating,
                                            'feature_status' => $val->feature_status,
                                            'feature_date' => $val->feature_date,
                                            'rating_count' => $val->rating_count,
                                            'consultation_details' => ($consultation_details) ? $consultation_details[0] : new stdClass(),
                                            'speciality' => $val->speciality

                                                /* End Here */
                                        );
                                    }
                                }
                                $filterData = array();
                                $filterData1 = array();
                                if ($doctor_data) {
                                    foreach ($doctor_data as $DD) {
                                        if ($DD['feature_status'] == 1) {
                                            $feature_date[] = $DD['feature_date'];
                                            $filterData[] = $DD;
                                        } else {
                                            $filterData1[] = $DD;
                                        }
                                    }
                                    if (!empty($filterData)) {
                                        array_multisort($feature_date, SORT_DESC, $filterData);
                                        $return_data = array_merge($filterData, $filterData1);
                                    } else {
                                        $return_data = $filterData1;
                                    }
                                } else {
                                    $return_data = array();
                                }
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('doctor_details');
                                $this->response->data = $return_data;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record');
                                die(json_encode($this->response));
                            }
                        } else {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'category':
                        if (!empty($gender) || !empty($gender_ru) || !empty($availablity) || (!empty($locality_lat) && !empty($locality_lng)) || !empty($language) || !empty($rating_filter) || !empty($price_type) || !empty($max_exp) || !empty($min_exp)) {
                            $where = "";
                            if (!empty($gender)) {
                                if ($gender != 'Any')
                                    $where .= "doctor_profile.gender = '$gender' OR doctor_profile.gender = '$gender_ru' AND ";
                            }

                            if ($min_exp == 5 && $max_exp == 5)
                                $where .= "doctor_profile.experience <= '5'";
                            else {
                                if (!empty($min_exp) && !empty($max_exp) && $max_exp != 5) {
                                    $where .= "doctor_profile.experience >= '$min_exp' AND doctor_profile.experience <= '$max_exp'";
                                } else {
                                    if (!empty($min_exp) && !empty($max_exp) && $max_exp == 5)
                                        $where .= "doctor_profile.experience >= '$min_exp'";
                                }
                            }
                            $result = $this->PatientModel->SearchDoctors($where);
                            if (!empty($language)) {
                                $language = explode(',', $language);
                            }
                            if ($result) {
                                foreach ($result as $key => $val) {
                                    $consultation_details = array();
                                    $speciality = array();
                                    if ($val->activate_status == 1 && $val->approve_status == 1) {
                                        $languageArr = "";
                                        $languageArr = explode(',', $val->language);
                                        if (!empty($language)) {
                                            if (count($languageArr) > count($language)) {
                                                if (array_intersect($languageArr, $language)) {
                                                    $clinic_hours_details = $this->Custom->get_where('clinic_hours', array('user_id' => $val->user_id));
                                                    $clinic_days = array();
                                                    foreach ($clinic_hours_details as $CH) {
                                                        if ($CH->working_status == 1) {
                                                            $clinic_days[] = $CH->day_id;
                                                        }
                                                    }
                                                    $val->clinic_days = $clinic_days;
                                                    $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                                    $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                                    $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                                    $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                                    $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                                    $val->rating = AverageRating($val->user_id, 'doctor');
                                                    $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                                    $doctor_data[] = $val;
                                                    $rating[] = $val->rating;
                                                    $clinic_working_days[] = $val->clinic_days;
                                                    $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));
                                                    $val->consultation_details = ($consultation_details) ? $consultation_details[0] : new stdClass();
                                                    //12 june 2108

                                                    if ($val->speciality != '') {
                                                        $speciality_data = array();
                                                        $speciality = explode(',', $val->speciality);
                                                        foreach ($speciality as $spe) {
                                                            $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                                            if ($specility_record) {
                                                                if ($_POST['lang'] == 'en') {
                                                                    $speciality_data[] = array(
                                                                        'id' => $specility_record[0]->id,
                                                                        'specility_name' => $specility_record[0]->specility_name
                                                                    );
                                                                } else if ($_POST['lang'] == 'ru') {
                                                                    $speciality_data[] = array(
                                                                        'id' => $specility_record[0]->id,
                                                                        'specility_name' => $specility_record[0]->specility_name_ru
                                                                    );
                                                                } else {
                                                                    $speciality_data[] = array(
                                                                        'id' => $specility_record[0]->id,
                                                                        'specility_name' => $specility_record[0]->specility_name
                                                                    );
                                                                }
                                                            } else {
                                                                $speciality_data[] = array(
                                                                    'specility_id' => $spe,
                                                                    'specility_name' => ""
                                                                );
                                                            }
                                                        }
                                                        $val->speciality = $speciality_data;
                                                    } else {
                                                        $val->speciality = array();
                                                    }
                                                    // End Here
                                                }
                                            } else {
                                                if (array_intersect($language, $languageArr)) {
                                                    $clinic_hours_details = $this->Custom->get_where('clinic_hours', array('user_id' => $val->user_id));
                                                    $clinic_days = array();
                                                    foreach ($clinic_hours_details as $CH) {
                                                        if ($CH->working_status == 1) {
                                                            $clinic_days[] = $CH->day_id;
                                                        }
                                                    }
                                                    $val->clinic_days = $clinic_days;
                                                    $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                                    $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                                    $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                                    $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                                    $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                                    $val->rating = AverageRating($val->user_id, 'doctor');
                                                    $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                                    $doctor_data[] = $val;
                                                    $rating[] = $val->rating;
                                                    $clinic_working_days[] = $val->clinic_days;
                                                    $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));
                                                    $val->consultation_details = ($consultation_details) ? $consultation_details[0] : new stdClass();

                                                    // 12 june 
                                                    if ($val->speciality != '') {
                                                        $speciality_data = array();
                                                        $speciality = explode(',', $val->speciality);
                                                        foreach ($speciality as $spe) {
                                                            $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                                            if ($specility_record) {
                                                                if ($_POST['lang'] == 'en') {
                                                                    $speciality_data[] = array(
                                                                        'id' => $specility_record[0]->id,
                                                                        'specility_name' => $specility_record[0]->specility_name
                                                                    );
                                                                } else if ($_POST['lang'] == 'ru') {
                                                                    $speciality_data[] = array(
                                                                        'id' => $specility_record[0]->id,
                                                                        'specility_name' => $specility_record[0]->specility_name_ru
                                                                    );
                                                                } else {
                                                                    $speciality_data[] = array(
                                                                        'id' => $specility_record[0]->id,
                                                                        'specility_name' => $specility_record[0]->specility_name
                                                                    );
                                                                }
                                                            } else {
                                                                $speciality_data[] = array(
                                                                    'specility_id' => $spe,
                                                                    'specility_name' => ""
                                                                );
                                                            }
                                                        }
                                                        $val->speciality = $speciality_data;
                                                    } else {
                                                        $val->speciality = array();
                                                    }
                                                    // End Here
                                                }
                                            }
                                        } else {
                                            $clinic_hours_details = $this->Custom->get_where('clinic_hours', array('user_id' => $val->user_id));
                                            $clinic_days = array();
                                            foreach ($clinic_hours_details as $CH) {
                                                if ($CH->working_status == 1) {
                                                    $clinic_days[] = $CH->day_id;
                                                }
                                            }
                                            $val->clinic_days = $clinic_days;
                                            $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                            $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                            $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                            $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                            $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
                                            if ($val->speciality != '') {
                                                $speciality_data = array();
                                                $speciality = explode(',', $val->speciality);
                                                foreach ($speciality as $spe) {
                                                    $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                                    if ($specility_record) {
                                                        if ($_POST['lang'] == 'en') {
                                                            $speciality_data[] = array(
                                                                'id' => $specility_record[0]->id,
                                                                'specility_name' => $specility_record[0]->specility_name
                                                            );
                                                        } else if ($_POST['lang'] == 'ru') {
                                                            $speciality_data[] = array(
                                                                'id' => $specility_record[0]->id,
                                                                'specility_name' => $specility_record[0]->specility_name_ru
                                                            );
                                                        } else {
                                                            $speciality_data[] = array(
                                                                'id' => $specility_record[0]->id,
                                                                'specility_name' => $specility_record[0]->specility_name
                                                            );
                                                        }
                                                    } else {
                                                        $speciality_data[] = array(
                                                            'specility_id' => $spe,
                                                            'specility_name' => ""
                                                        );
                                                    }
                                                }
                                                $val->speciality = $speciality_data;
                                            } else {
                                                $val->speciality = array();
                                            }
                                            //calculate rating
                                            $val->rating = AverageRating($val->user_id, 'doctor');
                                            $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                            $doctor_data[] = $val;
                                            $rating[] = $val->rating;
                                            $clinic_working_days[] = $val->clinic_days;
                                            $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));
                                            $val->consultation_details = ($consultation_details) ? $consultation_details[0] : new stdClass();
                                        }
                                    }
                                }
                                if ($doctor_data) {
                                    $prices = "";
                                    $actual_data = array();
                                    if (!empty($locality_lat) && !empty($locality_lng)) {
                                        foreach ($doctor_data as $row) {
                                            $distance = 0;
                                            $distance = distance($locality_lat, $locality_lng, $row->clinic_lat, $row->clinic_lng, 'K');

                                            if ($distance)
                                                $distance = round($distance);

                                            if ($distance <= 30) {
                                                $doctor_data1[] = $row;
                                            }
                                        }
                                    } else {
                                        $doctor_data1 = $doctor_data;
                                    }

                                    if (!empty($price_type)) {
                                        foreach ($doctor_data1 as $doc) {
                                            if ($price_type == 'online') {
                                                if ($doc->consultation_details->online_consult_status == 1) {
                                                    $actual_data[] = $doc;
                                                    $doc->prices = $doc->consultation_details->online_consult_charge;
                                                    $prices[] = $doc->consultation_details->online_consult_charge;
                                                }
                                            }
                                            if ($price_type == 'offline') {
                                                if ($doc->consultation_details->offline_consult_status == 1) {
                                                    $actual_data[] = $doc;
                                                    $doc->prices = $doc->consultation_details->offline_consult_charge;
                                                    $prices[] = $doc->consultation_details->offline_consult_charge;
                                                }
                                            }
                                            if ($price_type == 'invite') {
                                                if ($doc->consultation_details->invite_consult_status == 1) {
                                                    $actual_data[] = $doc;
                                                    $doc->prices = $doc->consultation_details->invite_consult_charge;
                                                    $prices[] = $doc->consultation_details->invite_consult_charge;
                                                }
                                            }
                                            if ($price_type == 'enquiry') {
                                                if ($doc->consultation_details->enquiry_consult_status == 1) {
                                                    $actual_data[] = $doc;
                                                    $doc->prices = $doc->consultation_details->enquiry_consult_charge;
                                                    $prices[] = $doc->consultation_details->enquiry_consult_charge;
                                                }
                                            }
                                        }

                                        if (!empty($price_filter)) {
                                            array_multisort($prices, SORT_DESC, $actual_data);
                                        } else {
                                            array_multisort($prices, SORT_ASC, $actual_data);
                                        }
                                    } else {
                                        $actual_data = $doctor_data1;
                                    }

                                    if (!empty($availablity)) {
                                        if (empty($availablity_filter)) {
                                            $this->response->success = 201;
                                            $this->response->message = $this->lang->line('required_field');
                                            die(json_encode($this->response));
                                        }
                                        $rating = "";
                                        $availablity = explode(',', $availablity);
                                        foreach ($actual_data as $DD) {
                                            if (!empty($DD->clinic_days)) {
                                                if (array_intersect($availablity, $DD->clinic_days)) {
                                                    $rating[] = $DD->rating;
                                                    $actual_data1[] = $DD;
                                                }
                                            }
                                        }
                                    } else {
                                        $actual_data1 = $actual_data;
                                    }

                                    if (!empty($rating_filter)) {
                                        array_multisort($rating, SORT_DESC, $actual_data1);
                                    } else {
                                        array_multisort($rating, SORT_ASC, $actual_data1);
                                    }
                                } else {
                                    $actual_data1 = array();
                                }
                                if ($actual_data1) {
                                    foreach ($actual_data1 as $val) {
                                        $search_doctor_data[] = array(
                                            'user_id' => $val->user_id,
                                            'role' => $val->role,
                                            'title' => $val->title,
                                            'name' => $val->name,
                                            'email' => $val->email,
                                            'country_code' => $val->country_code,
                                            'phone_number' => $val->phone_number,
                                            'latitude' => $val->latitude,
                                            'longitude' => $val->longitude,
                                            'status' => $val->status,
                                            'activate_status' => $val->activate_status,
                                            'created_at' => $val->created_at,
                                            'dob' => $val->dob,
                                            'gender' => $val->gender,
                                            'profile_image' => $val->profile_image,
                                            'experience' => $val->experience,
                                            'city' => $val->city,
                                            'consulation_fee' => $val->consulation_fee,
                                            'clinic_name' => $val->clinic_name,
                                            'clinic_phone_number' => $val->clinic_phone_number,
                                            'clinic_address' => $val->clinic_address,
                                            'clinic_city' => $val->clinic_city,
                                            'clinic_locality' => $val->clinic_locality,
                                            'clinic_pincode' => $val->clinic_pincode,
                                            'clinic_state' => $val->clinic_state,
                                            'clinic_lat' => $val->clinic_lat,
                                            'clinic_lng' => $val->clinic_lng,
                                            'availability' => $val->availability,
                                            'medical_registration_proof' => $val->medical_registration_proof,
                                            'photo_id_proof' => $val->photo_id_proof,
                                            'degree_proof' => $val->degree_proof,
                                            'signature' => $val->signature,
                                            'rating' => $val->rating,
                                            'rating_count' => $val->rating_count,
                                            'feature_status' => $val->feature_status,
                                            'feature_date' => $val->feature_date,
                                            'consultation_details' => $val->consultation_details,
                                            'speciality' => $val->speciality
                                        );
                                    }
                                } else {
                                    $search_doctor_data = array();
                                }
                                $filterData = array();
                                $filterData1 = array();
                                if ($doctor_data) {
                                    foreach ($search_doctor_data as $DD) {
                                        if ($DD['feature_status'] == 1) {
                                            $feature_date[] = $DD['feature_date'];
                                            $filterData[] = $DD;
                                        } else {
                                            $filterData1[] = $DD;
                                        }
                                    }
                                    if (!empty($filterData)) {
                                        array_multisort($feature_date, SORT_DESC, $filterData);
                                        $return_data = array_merge($filterData, $filterData1);
                                    } else {
                                        $return_data = $filterData1;
                                    }
                                } else {
                                    $return_data = array();
                                }
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('doctor_details');
                                $this->response->data = $return_data;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record');
                                die(json_encode($this->response));
                            }
                        } else {
                            $result = $this->PatientModel->SearchDoctors();
                            if ($result) {
                                foreach ($result as $val) {
                                    if ($val->activate_status == 1 && $val->approve_status == 1) {
                                        $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                        $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                        $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                        $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                        $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                        $val->rating = AverageRating($val->user_id, 'doctor');
                                        $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                        $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));
                                        $doctor_data[] = array(
                                            'user_id' => $val->user_id,
                                            'role' => $val->role,
                                            'title' => $val->title,
                                            'name' => $val->name,
                                            'email' => $val->email,
                                            'country_code' => $val->country_code,
                                            'phone_number' => $val->phone_number,
                                            'latitude' => $val->latitude,
                                            'longitude' => $val->longitude,
                                            'status' => $val->status,
                                            'activate_status' => $val->activate_status,
                                            'created_at' => $val->created_at,
                                            'dob' => $val->dob,
                                            'gender' => $val->gender,
                                            'profile_image' => $val->profile_image,
                                            'experience' => $val->experience,
                                            'city' => $val->city,
                                            'consulation_fee' => $val->consulation_fee,
                                            'clinic_name' => $val->clinic_name,
                                            'clinic_phone_number' => $val->clinic_phone_number,
                                            'clinic_address' => $val->clinic_address,
                                            'clinic_city' => $val->clinic_city,
                                            'clinic_locality' => $val->clinic_locality,
                                            'clinic_pincode' => $val->clinic_pincode,
                                            'clinic_state' => $val->clinic_state,
                                            'clinic_lat' => $val->clinic_lat,
                                            'clinic_lng' => $val->clinic_lng,
                                            'availability' => $val->availability,
                                            'medical_registration_proof' => $val->medical_registration_proof,
                                            'photo_id_proof' => $val->photo_id_proof,
                                            'degree_proof' => $val->degree_proof,
                                            'signature' => $val->signature,
                                            'rating' => $val->rating,
                                            'rating_count' => $val->rating_count,
                                            'feature_status' => $val->feature_status,
                                            'feature_date' => $val->feature_date,
                                            'consultation_details' => ($consultation_details) ? $consultation_details[0] : new stdClass()
                                        );
                                    }
                                }
                                $filterData = array();
                                $filterData1 = array();
                                if ($doctor_data) {
                                    foreach ($doctor_data as $DD) {
                                        if ($DD['feature_status'] == 1) {
                                            $feature_date[] = $DD['feature_date'];
                                            $filterData[] = $DD;
                                        } else {
                                            $filterData1[] = $DD;
                                        }
                                    }
                                    if (!empty($filterData)) {
                                        array_multisort($feature_date, SORT_DESC, $filterData);
                                        $return_data = array_merge($filterData, $filterData1);
                                    } else {
                                        $return_data = $filterData1;
                                    }
                                } else {
                                    $return_data = array();
                                }
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('doctor_details');
                                $this->response->data = $return_data;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record');
                                die(json_encode($this->response));
                            }
                        }
                        break;
                    case 'treatment':
                        if (empty($treatment)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $result = $this->PatientModel->SearchDoctors();
                        $doctor_data = array();
                        if ($result) {
                            $treatment = explode(',', $treatment);
                            foreach ($result as $val) {
                                if ($val->activate_status == 1 && $val->approve_status == 1) {
                                    $treatmentArr = "";
                                    $treatmentArr = explode(',', $val->treatment);
                                    if (array_intersect($treatmentArr, $treatment)) {
                                        $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                        $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                        $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                        $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                        $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                        $val->rating = AverageRating($val->user_id, 'doctor');
                                        $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                        $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));

                                        /**
                                         * 12 june 2018 
                                         */
                                        if ($val->speciality != '') {
                                            $speciality_data = array();
                                            $speciality = explode(',', $val->speciality);
                                            foreach ($speciality as $spe) {
                                                $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                                if ($specility_record) {
                                                    if ($_POST['lang'] == 'en') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    } else if ($_POST['lang'] == 'ru') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name_ru
                                                        );
                                                    } else {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    }
                                                } else {
                                                    $speciality_data[] = array(
                                                        'specility_id' => $spe,
                                                        'specility_name' => ""
                                                    );
                                                }
                                            }
                                            $val->speciality = $speciality_data;
                                        } else {
                                            $val->speciality = array();
                                        }

                                        $doctor_data[] = array(
                                            'user_id' => $val->user_id,
                                            'role' => $val->role,
                                            'title' => $val->title,
                                            'name' => $val->name,
                                            'email' => $val->email,
                                            'country_code' => $val->country_code,
                                            'phone_number' => $val->phone_number,
                                            'latitude' => $val->latitude,
                                            'longitude' => $val->longitude,
                                            'status' => $val->status,
                                            'activate_status' => $val->activate_status,
                                            'created_at' => $val->created_at,
                                            'dob' => $val->dob,
                                            'gender' => $val->gender,
                                            'profile_image' => $val->profile_image,
                                            'experience' => $val->experience,
                                            'city' => $val->city,
                                            'consulation_fee' => $val->consulation_fee,
                                            'clinic_name' => $val->clinic_name,
                                            'clinic_phone_number' => $val->clinic_phone_number,
                                            'clinic_address' => $val->clinic_address,
                                            'clinic_city' => $val->clinic_city,
                                            'clinic_locality' => $val->clinic_locality,
                                            'clinic_pincode' => $val->clinic_pincode,
                                            'clinic_state' => $val->clinic_state,
                                            'clinic_lat' => $val->clinic_lat,
                                            'clinic_lng' => $val->clinic_lng,
                                            'availability' => $val->availability,
                                            'medical_registration_proof' => $val->medical_registration_proof,
                                            'photo_id_proof' => $val->photo_id_proof,
                                            'degree_proof' => $val->degree_proof,
                                            'signature' => $val->signature,
                                            'rating' => $val->rating,
                                            'rating_count' => $val->rating_count,
                                            'feature_status' => $val->feature_status,
                                            'feature_date' => $val->feature_date,
                                            'consultation_details' => ($consultation_details) ? $consultation_details[0] : new stdClass(),
                                            'speciality' => $val->speciality
                                        );

                                        /* End Here */
                                    }
                                }
                            }
                            $filterData = array();
                            $filterData1 = array();
                            if ($doctor_data) {
                                foreach ($doctor_data as $DD) {
                                    if ($DD['feature_status'] == 1) {
                                        $feature_date[] = $DD['feature_date'];
                                        $filterData[] = $DD;
                                    } else {
                                        $filterData1[] = $DD;
                                    }
                                }
                                if (!empty($filterData)) {
                                    array_multisort($feature_date, SORT_DESC, $filterData);
                                    $return_data = array_merge($filterData, $filterData1);
                                } else {
                                    $return_data = $filterData1;
                                }
                            } else {
                                $return_data = array();
                            }
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('doctor_details');
                            $this->response->data = $return_data;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'speciality':
                        if (empty($speciality)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $doctor_data = array();
                        $result = $this->PatientModel->SearchDoctors();
                        if ($result) {
                            $speciality = explode(',', $speciality);
                            foreach ($result as $key => $val) {
                                if ($val->activate_status == 1 && $val->approve_status == 1) {
                                    $specialityArr = "";
                                    $specialityArr = explode(',', $val->speciality);
                                    if (array_intersect($specialityArr, $speciality)) {
                                        $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                        $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                        $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                        $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                        $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                        $val->rating = AverageRating($val->user_id, 'doctor');
                                        $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                        $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));

                                        /**
                                         * 12 June 2018 
                                         */
                                        if ($val->speciality != '') {
                                            $speciality_data = array();
                                            $speciality1 = explode(',', $val->speciality);
                                            foreach ($speciality1 as $spe) {
                                                $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                                if ($specility_record) {
                                                    if ($_POST['lang'] == 'en') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    } else if ($_POST['lang'] == 'ru') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name_ru
                                                        );
                                                    } else {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    }
                                                } else {
                                                    $speciality_data[] = array(
                                                        'specility_id' => $spe,
                                                        'specility_name' => ""
                                                    );
                                                }
                                            }
                                            $val->speciality = $speciality_data;
                                        } else {
                                            $val->speciality = array();
                                        }

                                        $doctor_data[] = array(
                                            'user_id' => $val->user_id,
                                            'role' => $val->role,
                                            'title' => $val->title,
                                            'name' => $val->name,
                                            'email' => $val->email,
                                            'country_code' => $val->country_code,
                                            'phone_number' => $val->phone_number,
                                            'latitude' => $val->latitude,
                                            'longitude' => $val->longitude,
                                            'status' => $val->status,
                                            'activate_status' => $val->activate_status,
                                            'created_at' => $val->created_at,
                                            'dob' => $val->dob,
                                            'gender' => $val->gender,
                                            'profile_image' => $val->profile_image,
                                            'experience' => $val->experience,
                                            'city' => $val->city,
                                            'consulation_fee' => $val->consulation_fee,
                                            'clinic_name' => $val->clinic_name,
                                            'clinic_phone_number' => $val->clinic_phone_number,
                                            'clinic_address' => $val->clinic_address,
                                            'clinic_city' => $val->clinic_city,
                                            'clinic_locality' => $val->clinic_locality,
                                            'clinic_pincode' => $val->clinic_pincode,
                                            'clinic_state' => $val->clinic_state,
                                            'clinic_lat' => $val->clinic_lat,
                                            'clinic_lng' => $val->clinic_lng,
                                            'availability' => $val->availability,
                                            'medical_registration_proof' => $val->medical_registration_proof,
                                            'photo_id_proof' => $val->photo_id_proof,
                                            'degree_proof' => $val->degree_proof,
                                            'signature' => $val->signature,
                                            'rating' => $val->rating,
                                            'rating_count' => $val->rating_count,
                                            'feature_status' => $val->feature_status,
                                            'feature_date' => $val->feature_date,
                                            'consultation_details' => ($consultation_details) ? $consultation_details[0] : new stdClass(),
                                            'speciality' => $val->speciality
                                        );
                                    }
                                }
                            }
                        }

                        if ($doctor_data) {
                            $filterData = array();
                            $filterData1 = array();
                            foreach ($doctor_data as $DD) {
                                if ($DD['feature_status'] == 1) {
                                    $feature_date[] = $DD['feature_date'];
                                    $filterData[] = $DD;
                                } else {
                                    $filterData1[] = $DD;
                                }
                            }
                            if (!empty($filterData)) {
                                array_multisort($feature_date, SORT_DESC, $filterData);
                                $return_data = array_merge($filterData, $filterData1);
                            } else {
                                $return_data = $filterData1;
                            }

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('doctor_details');
                            $this->response->data = $return_data;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'diseases':
                        if (empty($diseases)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $result = $this->PatientModel->SearchDoctors();
                        $doctor_data = array();
                        if ($result) {
                            $diseases = explode(',', $diseases);
                            foreach ($result as $key => $val) {
                                if ($val->activate_status == 1 && $val->approve_status == 1) {
                                    $diseasesArr = "";
                                    $diseasesArr = explode(',', $val->diseases);
                                    if (array_intersect($diseasesArr, $diseases)) {
                                        $val->profile_image = ($val->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $val->profile_image : "";
                                        $val->medical_registration_proof = ($val->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->medical_registration_proof : "";
                                        $val->degree_proof = ($val->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->degree_proof : "";
                                        $val->photo_id_proof = ($val->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->photo_id_proof : "";
                                        $val->signature = ($val->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $val->signature : "";
//calculate rating
                                        $val->rating = AverageRating($val->user_id, 'doctor');
                                        $val->rating_count = TotalRatingCount($val->user_id, 'doctor');
                                        $consultation_details = GetDetails('consultation_settings', array('user_id' => $val->user_id));

                                        /**
                                         * 12 june 2018 
                                         */
                                        if ($val->speciality != '') {
                                            $speciality_data = array();
                                            $speciality = explode(',', $val->speciality);
                                            foreach ($speciality as $spe) {
                                                $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                                if ($specility_record) {
                                                    if ($_POST['lang'] == 'en') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    } else if ($_POST['lang'] == 'ru') {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name_ru
                                                        );
                                                    } else {
                                                        $speciality_data[] = array(
                                                            'id' => $specility_record[0]->id,
                                                            'specility_name' => $specility_record[0]->specility_name
                                                        );
                                                    }
                                                } else {
                                                    $speciality_data[] = array(
                                                        'specility_id' => $spe,
                                                        'specility_name' => ""
                                                    );
                                                }
                                            }
                                            $val->speciality = $speciality_data;
                                        } else {
                                            $val->speciality = array();
                                        }


                                        $doctor_data[] = array(
                                            'user_id' => $val->user_id,
                                            'role' => $val->role,
                                            'title' => $val->title,
                                            'name' => $val->name,
                                            'email' => $val->email,
                                            'country_code' => $val->country_code,
                                            'phone_number' => $val->phone_number,
                                            'latitude' => $val->latitude,
                                            'longitude' => $val->longitude,
                                            'status' => $val->status,
                                            'activate_status' => $val->activate_status,
                                            'created_at' => $val->created_at,
                                            'dob' => $val->dob,
                                            'gender' => $val->gender,
                                            'profile_image' => $val->profile_image,
                                            'experience' => $val->experience,
                                            'city' => $val->city,
                                            'consulation_fee' => $val->consulation_fee,
                                            'clinic_name' => $val->clinic_name,
                                            'clinic_phone_number' => $val->clinic_phone_number,
                                            'clinic_address' => $val->clinic_address,
                                            'clinic_city' => $val->clinic_city,
                                            'clinic_locality' => $val->clinic_locality,
                                            'clinic_pincode' => $val->clinic_pincode,
                                            'clinic_state' => $val->clinic_state,
                                            'clinic_lat' => $val->clinic_lat,
                                            'clinic_lng' => $val->clinic_lng,
                                            'availability' => $val->availability,
                                            'medical_registration_proof' => $val->medical_registration_proof,
                                            'photo_id_proof' => $val->photo_id_proof,
                                            'degree_proof' => $val->degree_proof,
                                            'signature' => $val->signature,
                                            'rating' => $val->rating,
                                            'rating_count' => $val->rating_count,
                                            'feature_status' => $val->feature_status,
                                            'feature_date' => $val->feature_date,
                                            'consultation_details' => ($consultation_details) ? $consultation_details[0] : new stdClass(),
                                            'speciality' => $val->speciality
                                        );
                                    }
                                }
                            }
                        }
                        if ($doctor_data) {
                            $filterData = array();
                            $filterData1 = array();
                            foreach ($doctor_data as $DD) {
                                if ($DD['feature_status'] == 1) {
                                    $feature_date[] = $DD['feature_date'];
                                    $filterData[] = $DD;
                                } else {
                                    $filterData1[] = $DD;
                                }
                            }
                            if (!empty($filterData)) {
                                array_multisort($feature_date, SORT_DESC, $filterData);
                                $return_data = array_merge($filterData, $filterData1);
                            } else {
                                $return_data = $filterData1;
                            }

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('doctor_details');
                            $this->response->data = $return_data;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;
                endswitch;
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetFilterDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $filter_details = $this->Custom->get_where('filter_details', array('user_id' => $user_id));
                if (!empty($filter_details)) {
                    $this->response->success = 200;
                    $this->response->message = 'Filter Details.';
                    $this->response->data = $filter_details;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetTimingSlots() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $day_id = ($this->input->post('day_id')) ? $this->input->post('day_id') : '';
        $date = ($this->input->post('date')) ? $this->input->post('date') : '';
        if (!empty($user_id) && !empty($doctor_id) && !empty($day_id) && !empty($date)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                if (empty($doctor_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('doctor_id_error');
                    die(json_encode($this->response));
                }
                $clinic_hours_details = $this->Custom->get_where('clinic_hours', array('user_id' => $doctor_id, 'day_id' => $day_id, 'working_status' => 1));
                $booking_frequency = 30;
                $user_timezone = date_default_timezone_get();
                if (!empty($clinic_hours_details)) {
                    foreach ($clinic_hours_details as $row) {
                        //$start = ConvertTimezone($row->start_hour, $user_timezone, 'H:i');
                        //$end = ConvertTimezone($row->end_hour, $user_timezone, 'H:i');
                        //print_r(date('H:i', $row->start_hour));
                        //$start = date('H:i', strtotime($row->start_hour));
                        //$end = date('H:i', strtotime($row->end_hour));
                        $start = $row->start_hour;
                        $end = $row->end_hour;
                        for ($i = $start; $i <= $end; $i = $i + $booking_frequency * 60) {
                            $slots[] = date("H:i", $i);
                        }
                    }
                    foreach ($slots as $i => $start) {
                        $finish_time = strtotime($start) + $booking_frequency * 60;
                        $book_slot_time[] = array(
                            'start' => $start,
                            'finish' => date("H:i", $finish_time),
                            'status' => 0
                        );
                    }

                    //$booking_record = $this->Custom->get_where('appointment', array('appointment_date' => $date, 'doctor_id' => $doctor_id));
                    $booking_record = $this->Custom->query("select * from appointment where appointment_date = '$date' && doctor_id = $doctor_id && (status = 0 || status = 1 || status = 4 || status = 5 || status = 6)");
                    if (!empty($booking_record)) {
                        /* foreach ($booking_record as $book) {
                          $book_start_slot[] = $book->start_time;
                          foreach ($book_slot_time as $BST) {
                          if ($BST['start'] == $book->start_time) {
                          $BST['status'] = 1;
                          }
                          $new_slot_time[] = $BST;
                          }
                          } */
                        foreach ($booking_record as $book) {
                            $book_start_slot[] = date("H:i", $book->start_time);
                        }
                        foreach ($book_slot_time as $BST) {
                            if (in_array($BST['start'], $book_start_slot)) {
                                $BST['status'] = 1;
                            }
                            $new_slot_time[] = $BST;
                        }
                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('time_slot_details');
                        $this->response->data = ($new_slot_time) ? $new_slot_time : array();
                        die(json_encode($this->response));
                    } else {
                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('time_slot_details');
                        $this->response->data = ($book_slot_time) ? $book_slot_time : array();
                        die(json_encode($this->response));
                    }
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function BookAppointment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $appointment_date = ($this->input->post('appointment_date')) ? $this->input->post('appointment_date') : '';
        $start_time = ($this->input->post('start_time')) ? $this->input->post('start_time') : '';
        $end_time = ($this->input->post('end_time')) ? $this->input->post('end_time') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $mobile_number = ($this->input->post('mobile_number')) ? $this->input->post('mobile_number') : '';
        $email = ($this->input->post('email')) ? $this->input->post('email') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $description = ($this->input->post('description')) ? $this->input->post('description') : '';
        $user_type = ($this->input->post('user_type')) ? $this->input->post('user_type') : '';
        $member_id = ($this->input->post('member_id')) ? $this->input->post('member_id') : '';
        $appointment_type = ($this->input->post('appointment_type')) ? $this->input->post('appointment_type') : 0;
        $images = (isset($_FILES['image']) && !empty($_FILES['image'])) ? $_FILES['image'] : '';
        $annoymous_status = ($this->input->post('annoymous_status')) ? $this->input->post('annoymous_status') : 0;
        $images_arr = "";
        if (!empty($user_id) && !empty($doctor_id) && !empty($appointment_date)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                if (empty($doctor_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('doctor_id_error');
                    die(json_encode($this->response));
                }
                $appointment = $this->Custom->query("select * from appointment where doctor_id = $doctor_id AND start_time = '$time' AND (status = 0 OR status = 1 OR status = 4 OR status = 5 OR status = 6)");
                if (!empty($appointment)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('slot_booked');
                    die(json_encode($this->response));
                }
                $consultation_settings = $this->Custom->get_where('consultation_settings', array('user_id' => $doctor_id));
                if ($appointment_type == 1 || $appointment_type == 2 || $appointment_type == 3) {
                    $consult_time = $consultation_settings[0]->online_consult_time;
                    $end_time = strtotime('+' . $consult_time . ' minutes', $start_time);
                    $amount = $consultation_settings[0]->online_consult_charge;
                }
                if ($appointment_type == 0) {
                    $amount = $consultation_settings[0]->offline_consult_charge;
                }
                if ($appointment_type == 4) {
                    $amount = $consultation_settings[0]->invite_consult_charge;
                }
                $appointment_doc = "";
                if (!empty($images)) {
                    $files = $images;
                    unset($_FILES);
                    $config = array(
                        'upload_path' => PATIENT_MEDICAL_DOCUMENT_PATH . '/',
                        'allowed_types' => '*',
                        'overwrite' => TRUE,
                        'encrypt_name' => FALSE,
                        'remove_spaces' => FALSE
                    );

                    $this->load->library('upload', $config);
                    foreach ($files['name'] as $key => $image) {
                        $_FILES['image']['name'] = $files['name'][$key];
                        $_FILES['image']['type'] = $files['type'][$key];
                        $_FILES['image']['tmp_name'] = $files['tmp_name'][$key];
                        $_FILES['image']['error'] = $files['error'][$key];
                        $_FILES['image']['size'] = $files['size'][$key];

                        $ext = end((explode(".", $image)));
                        $new_name = rand() . time() . '.' . $ext;

                        $config['file_name'] = $new_name;

                        $this->upload->initialize($config);

                        $this->upload->do_upload('image');
                        $images_arr[] = $new_name;
                    }
                }
                if (!empty($images_arr))
                    $appointment_doc = implode(',', $images_arr);
                else
                    $appointment_doc = "";
                if ($user_type == 'patient_member') {
                    $patient_member = $this->Custom->get_where('patient_member', array('id' => $member_id));
                    if (empty($patient_member)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('member_id_error');
                        die(json_encode($this->response));
                    }
                    $insertArr = array(
                        'appointment_uid' => '',
                        'user_id' => $user_id,
                        'user_type' => $user_type,
                        'member_id' => $member_id,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $appointment_date,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'name' => $patient_member[0]->name,
                        'mobile_number' => '',
                        'email' => '',
                        'gender' => $patient_member[0]->gender,
                        'description' => $description,
                        'address' => '',
                        'appointment_doc' => $appointment_doc,
                        'appointment_type' => $appointment_type,
                        'annoymous_status' => $annoymous_status,
                        'doctor_accepted_date' => '',
                        'amount' => $amount,
                        'created_at' => strtotime(date("Y-m-d H:i:s")),
                    );
                } else {
                    $insertArr = array(
                        'appointment_uid' => '',
                        'user_id' => $user_id,
                        'user_type' => $user_type,
                        'member_id' => 0,
                        'doctor_id' => $doctor_id,
                        'appointment_date' => $appointment_date,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'name' => $name,
                        'mobile_number' => $mobile_number,
                        'email' => $email,
                        'gender' => $gender,
                        'description' => $description,
                        'address' => '',
                        'appointment_doc' => $appointment_doc,
                        'appointment_type' => $appointment_type,
                        'annoymous_status' => $annoymous_status,
                        'doctor_accepted_date' => '',
                        'amount' => $amount,
                        'created_at' => strtotime(date("Y-m-d H:i:s")),
                    );
                }
                $insert_id = $this->Custom->insert_data('appointment', $insertArr);

                if ($insert_id) {
//update chat room name
                    $chat_room_name = 'ZumCare-' . $doctor_id . '-' . $user_id . '-' . time();
                    $this->Custom->update_where('appointment', array('chat_room_name' => $chat_room_name), array('id' => $insert_id));

                    $doctor_profile_record = $this->Custom->get_where('doctor_profile', array('user_id' => $doctor_id));
                    $doctor_name = $doctor_record[0]->title . " " . $doctor_record[0]->name;
                    $datetime = date('M d', strtotime($appointment_date)) . ", " . date('H:i A', $start_time);

                    $username = $user_record[0]->name;
//send mail
                    $mail_data = array('Hello' => $this->lang->line('Hello'), 'username' => $username, 'appointment_req_with' => $this->lang->line('appointment_req_with'), 'at' => $this->lang->line('at'), 'for' => $this->lang->line('for'), 'forward_for_confirmation' => $this->lang->line('forward_for_confirmation'), 'notify_for_app_confirmation' => $this->lang->line('notify_for_app_confirmation'), 'call_at_clinic' => $this->lang->line('call_at_clinic'), 'clinic_details' => $this->lang->line('clinic_details'), 'Thanks' => $this->lang->line('Thanks'), 'team' => $this->lang->line('team'), 'doctor_name' => $doctor_name, 'doctor_profile_record' => $doctor_profile_record, 'datetime' => $datetime);
                    $content = $this->load->view('mail/book_appointment', $mail_data, TRUE);
                    $subject = $this->lang->line('appointment_booked_subject') . $doctor_name;
                    $this->SendMail($user_record[0]->email, $subject, $content);

                    $this->DoctorNotification('appointment', $user_id, $doctor_id, 'patient', $insert_id, $appointment_type, 0, '');

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('appointment_booked');
                    $this->response->data = $insert_id;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetDoctorFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $feed_category_id = ($this->input->post('feed_category_id')) ? $this->input->post('feed_category_id') : '';
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case 'doctor':
                        if (empty($doctor_id)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_doctor_id');
                            die(json_encode($this->response));
                        }
                        $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                        if (empty($doctor_record)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('doctor_id_error');
                            die(json_encode($this->response));
                        }
                        $feed_record = $this->PatientModel->GetFeeds($doctor_id, 'by_user');
                        if (!empty($feed_record)) {
                            foreach ($feed_record as $value) {
                                //like dislike status
                                $feed_status_record = $this->Custom->get_where('like_dislike_doctor_feed', array('feed_id' => $value->id, 'user_id' => $user_id));
                                if ($feed_status_record)
                                    $value->feed_like_dislike_status = $feed_status_record[0]->status;
                                else
                                    $value->feed_like_dislike_status = "0";
                                //save feed status
                                $saved_patient_feed_record = $this->Custom->get_where('saved_patient_feed', array('feed_id' => $value->id, 'user_id' => $user_id));
                                if (!empty($saved_patient_feed_record)) {
                                    $value->save_feed_status = "1";
                                    $value->saved_feed_id = $saved_patient_feed_record[0]->id;
                                } else {
                                    $value->save_feed_status = "0";
                                    $value->saved_feed_id = "";
                                }
                                $value->image = ($value->image) ? base_url() . DOCTOR_FEED_URL . '/' . $value->image : "";
                                $value->profile_image = ($value->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $value->profile_image : "";
                                $value->timestamp = $value->created_at;
                                $value->like_count = GetTotalCount('like_dislike_doctor_feed', array('feed_id' => $value->id, 'status' => 1));
                                $value->comment_count = GetTotalCount('comment_doctor_feed', array('feed_id' => $value->id));
                                if ($_POST['lang'] == 'en') {
                                    $value->category_name = $value->category_name_en;
                                } else if ($_POST['lang'] == 'ru') {
                                    $value->category_name = $value->category_name_ru;
                                } else {
                                    $value->category_name = $value->category_name_en;
                                }

                                //12 June 2018
                                if ($value->speciality != '') {
                                    $speciality_data = array();
                                    $speciality = explode(',', $value->speciality);
                                    foreach ($speciality as $spe) {
                                        $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                        if ($specility_record) {
                                            if ($_POST['lang'] == 'en') {
                                                $speciality_data[] = array(
                                                    'id' => $specility_record[0]->id,
                                                    'specility_name' => $specility_record[0]->specility_name
                                                );
                                            } else if ($_POST['lang'] == 'ru') {
                                                $speciality_data[] = array(
                                                    'id' => $specility_record[0]->id,
                                                    'specility_name' => $specility_record[0]->specility_name_ru
                                                );
                                            } else {
                                                $speciality_data[] = array(
                                                    'id' => $specility_record[0]->id,
                                                    'specility_name' => $specility_record[0]->specility_name
                                                );
                                            }
                                        } else {
                                            $speciality_data[] = array(
                                                'specility_id' => $spe,
                                                'specility_name' => ""
                                            );
                                        }
                                    }
                                    $value->speciality = $speciality_data;
                                } else {
                                    $value->speciality = array();
                                }

                                //End here
                                unset($value->category_name_ru);
                                unset($value->category_name_en);
                                $feed_details[] = $value;
                            }
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('feed_data');
                            $this->response->data = $feed_details;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_for_feed_data');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'all':
                        $feed_record = $this->PatientModel->GetFeeds();
                        //print_r($feed_record); die;
                        if (!empty($feed_record)) {
                            foreach ($feed_record as $value) {
//like dislike status
                                $feed_status_record = $this->Custom->get_where('like_dislike_doctor_feed', array('feed_id' => $value->id, 'user_id' => $user_id));
                                if ($feed_status_record)
                                    $value->feed_like_dislike_status = $feed_status_record[0]->status;
                                else
                                    $value->feed_like_dislike_status = "0";
//save feed status
                                $saved_patient_feed_record = $this->Custom->get_where('saved_patient_feed', array('feed_id' => $value->id, 'user_id' => $user_id));
                                if (!empty($saved_patient_feed_record)) {
                                    $value->save_feed_status = "1";
                                    $value->saved_feed_id = $saved_patient_feed_record[0]->id;
                                } else {
                                    $value->save_feed_status = "0";
                                    $value->saved_feed_id = "";
                                }
                                //12 June 2018
                                if ($value->speciality != '') {
                                    $speciality_data = array();
                                    $speciality = explode(',', $value->speciality);
                                    foreach ($speciality as $spe) {
                                        $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                        if ($specility_record) {
                                            if ($_POST['lang'] == 'en') {
                                                $speciality_data[] = array(
                                                    'id' => $specility_record[0]->id,
                                                    'specility_name' => $specility_record[0]->specility_name
                                                );
                                            } else if ($_POST['lang'] == 'ru') {
                                                $speciality_data[] = array(
                                                    'id' => $specility_record[0]->id,
                                                    'specility_name' => $specility_record[0]->specility_name_ru
                                                );
                                            } else {
                                                $speciality_data[] = array(
                                                    'id' => $specility_record[0]->id,
                                                    'specility_name' => $specility_record[0]->specility_name
                                                );
                                            }
                                        } else {
                                            $speciality_data[] = array(
                                                'specility_id' => $spe,
                                                'specility_name' => ""
                                            );
                                        }
                                    }
                                    $value->speciality = $speciality_data;
                                } else {
                                    $value->speciality = array();
                                }

                                //End here 
                                $value->image = ($value->image) ? base_url() . DOCTOR_FEED_URL . '/' . $value->image : "";
                                $value->profile_image = ($value->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $value->profile_image : "";
                                $value->timestamp = $value->created_at;
                                $value->like_count = GetTotalCount('like_dislike_doctor_feed', array('feed_id' => $value->id, 'status' => 1));
                                $value->comment_count = GetTotalCount('comment_doctor_feed', array('feed_id' => $value->id));
                                if ($_POST['lang'] == 'en') {
                                    $value->category_name = $value->category_name_en;
                                } else if ($_POST['lang'] == 'ru') {
                                    $value->category_name = $value->category_name_ru;
                                } else {
                                    $value->category_name = $value->category_name_en;
                                }
                                unset($value->category_name_ru);
                                unset($value->category_name_en);
                                $feed_details[] = $value;
                            }
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('feed_data');
                            $this->response->data = $feed_details;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_for_feed_data');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'feed_category':
                        if (empty($feed_category_id)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_feed_id');
                            die(json_encode($this->response));
                        }
                        $feed_category = $this->Custom->get_where('feed_category', array('id' => $feed_category_id));
                        if (empty($feed_category)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('invalid_feed_id');
                            die(json_encode($this->response));
                        }
                        if (!empty($doctor_id)) {
                            $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                            if (empty($doctor_record)) {
                                $this->response->success = 203;
                                $this->response->message = $this->lang->line('doctor_id_error');
                                die(json_encode($this->response));
                            }
                            $feed_record = $this->PatientModel->GetFeeds($doctor_id, 'by_user');
                        } else {
                            $feed_record = $this->PatientModel->GetFeeds();
                        }
                        if (!empty($feed_record)) {
                            foreach ($feed_record as $value) {
                                if ($value->feed_category_id == $feed_category_id) {
//like dislike status
                                    $feed_status_record = $this->Custom->get_where('like_dislike_doctor_feed', array('feed_id' => $value->id, 'user_id' => $user_id));
                                    if ($feed_status_record)
                                        $value->feed_like_dislike_status = $feed_status_record[0]->status;
                                    else
                                        $value->feed_like_dislike_status = "0";
//save feed status
                                    $saved_patient_feed_record = $this->Custom->get_where('saved_patient_feed', array('feed_id' => $value->id, 'user_id' => $user_id));
                                    if (!empty($saved_patient_feed_record)) {
                                        $value->save_feed_status = "1";
                                        $value->saved_feed_id = $saved_patient_feed_record[0]->id;
                                    } else {
                                        $value->save_feed_status = "0";
                                        $value->saved_feed_id = "";
                                    }
                                    $value->image = ($value->image) ? base_url() . DOCTOR_FEED_URL . '/' . $value->image : "";
                                    $value->profile_image = ($value->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $value->profile_image : "";
                                    $value->timestamp = $value->created_at;
                                    $value->like_count = GetTotalCount('like_dislike_doctor_feed', array('feed_id' => $value->id, 'status' => 1));
                                    $value->comment_count = GetTotalCount('comment_doctor_feed', array('feed_id' => $value->id));
                                    if ($_POST['lang'] == 'en') {
                                        $value->category_name = $value->category_name_en;
                                    } else if ($_POST['lang'] == 'ru') {
                                        $value->category_name = $value->category_name_ru;
                                    } else {
                                        $value->category_name = $value->category_name_en;
                                    }

                                    //12 June 2018
                                    if ($value->speciality != '') {
                                        $speciality_data = array();
                                        $speciality = explode(',', $value->speciality);
                                        foreach ($speciality as $spe) {
                                            $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                            if ($specility_record) {
                                                if ($_POST['lang'] == 'en') {
                                                    $speciality_data[] = array(
                                                        'id' => $specility_record[0]->id,
                                                        'specility_name' => $specility_record[0]->specility_name
                                                    );
                                                } else if ($_POST['lang'] == 'ru') {
                                                    $speciality_data[] = array(
                                                        'id' => $specility_record[0]->id,
                                                        'specility_name' => $specility_record[0]->specility_name_ru
                                                    );
                                                } else {
                                                    $speciality_data[] = array(
                                                        'id' => $specility_record[0]->id,
                                                        'specility_name' => $specility_record[0]->specility_name
                                                    );
                                                }
                                            } else {
                                                $speciality_data[] = array(
                                                    'specility_id' => $spe,
                                                    'specility_name' => ""
                                                );
                                            }
                                        }
                                        $value->speciality = $speciality_data;
                                    } else {
                                        $value->speciality = array();
                                    }

                                    //End here
                                    unset($value->category_name_ru);
                                    unset($value->category_name_en);
                                    $feed_details[] = $value;
                                }
                            }
                            if ($feed_details) {
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('feed_data');
                                $this->response->data = $feed_details;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record_for_feed_data');
                                die(json_encode($this->response));
                            }
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_for_feed_data');
                            die(json_encode($this->response));
                        }
                        break;
                endswitch;
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function LikeDislikeDoctorFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        $status = ($this->input->post('status')) ? $this->input->post('status') : '';
        if (isset($_POST['lang'])) {
            unset($_POST['lang']);
        }
        if (!empty($user_id) && !empty($feed_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_feed = $this->Custom->get_where('doctor_feed', array('id' => $feed_id));
                if (empty($doctor_feed)) {
                    $this->response->success = 203;
                    $this->response->message = "Feed id is not valid";
                    die(json_encode($this->response));
                }
                $feed_record = $this->Custom->get_where('like_dislike_doctor_feed', array('user_id' => $user_id, 'feed_id' => $feed_id));
                if (!empty($feed_record)) {
                    $this->Custom->update_where('like_dislike_doctor_feed', array('status' => $status), array('id' => $feed_record[0]->id));

                    if ($status == 0)
                        $message = $this->lang->line('feed_dislike');
                    if ($status == 1) {
                        $message = $this->lang->line('feed_like');
                    }
                    $this->response->success = 200;
                    $this->response->message = $message;
                    die(json_encode($this->response));
                } else {
                    $insert_id = $this->Custom->insert_data('like_dislike_doctor_feed', $_POST);
                    if ($insert_id) {
//send notification
                        if ($status == 1) {
                            $this->DoctorNotification('feed', $user_id, $doctor_feed[0]->user_id, 'patient', $feed_id, 'feed_like', '', '');
                        }
                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('feed_like');
                        die(json_encode($this->response));
                    } else {
                        $this->response->success = 202;
                        $this->response->message = $this->lang->line('went_wrong');
                        die(json_encode($this->response));
                    }
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AddInAppropriate() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $reason = ($this->input->post('reason')) ? $this->input->post('reason') : '';
        $feedback = ($this->input->post('feedback')) ? $this->input->post('feedback') : '';
        $_POST['created_at'] = strtotime(date("Y-m-d H:i:s"));
        if (!empty($user_id) && !empty($doctor_id) && !empty($reason)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                if (empty($doctor_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('doctor_id_error');
                    die(json_encode($this->response));
                }
                unset($_POST['lang']);
                $insert_id = $this->Custom->insert_data('in_appropriate', $_POST);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('in_appropriate_added');
                    //print_r($this->response); die("Hello");
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function PatientMember() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $member_id = ($this->input->post('member_id')) ? $this->input->post('member_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $dob = ($this->input->post('dob')) ? $this->input->post('dob') : '';
        $height = ($this->input->post('height')) ? $this->input->post('height') : '';
        $weight = ($this->input->post('weight')) ? $this->input->post('weight') : '';
        $city = ($this->input->post('city')) ? $this->input->post('city') : '';
        $locality = ($this->input->post('locality')) ? $this->input->post('locality') : '';
        $language = ($this->input->post('language')) ? $this->input->post('language') : '';
        $phone_number = ($this->input->post('phone_number')) ? $this->input->post('phone_number') : '';
        $relationship = ($this->input->post('relationship')) ? $this->input->post('relationship') : '';
        $_POST['created_at'] = strtotime(date("Y-m-d H:i:s"));
        unset($_POST['lang']);
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case 'get':
                        $member_record = $this->Custom->get_where('patient_member', array('user_id' => $user_id, 'status' => 1));
                        if ($member_record) {
                            foreach ($member_record as $row) {
                                $member_language = "";
                                if ($row->language != '') {
                                    foreach ($row->language as $lang) {
                                        $language_record = $this->Custom->get_where('language', array('id' => $lang));
                                        $member_language[] = array(
                                            'language_id' => $lang,
                                            'language' => ($language_record) ? $language_record[0]->name : ""
                                        );
                                    }
                                }
                                $row->member_language = ($member_language) ? $member_language : array();
                                $additional_record = $this->Custom->get_where('member_additional_info', array('user_id' => $user_id, 'member_id' => $row->id));
                                if ($additional_record) {
                                    foreach ($additional_record as $info) {
                                        $add_infoArr[] = array(
                                            'add_info_id' => $info->id,
                                            'question' => $info->question,
                                            'title' => $info->title,
                                            'current_status' => $info->current_status,
                                            'notes' => $info->notes,
                                        );
                                    }
                                } else {
                                    $add_infoArr = array();
                                }
                                $row->additional_info = $add_infoArr;
                                $member_data[] = $row;
                            }
                        } else {
                            $member_data = array();
                        }
                        if (!empty($member_data)) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('member_list');
                            $this->response->data = $member_data;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_for_member');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'add':
                        if (empty($name) && empty($relationship)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        unset($_POST['type']);
                        $insert_id = $this->Custom->insert_data('patient_member', $_POST);
                        if ($insert_id) {
                            $member_record = $this->Custom->get_where('patient_member', array('id' => $insert_id));
                            $member_language = "";
                            if ($member_record[0]->language != '') {
                                foreach ($member_record[0]->language as $lang) {
                                    $language_record = $this->Custom->get_where('language', array('id' => $lang));
                                    $member_language[] = array(
                                        'language_id' => $lang,
                                        'language' => ($language_record) ? $language_record[0]->name : ""
                                    );
                                }
                            }
                            $member_record[0]->member_language = ($member_language) ? $member_language : array();

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('member_added');
                            $this->response->data = $member_record[0];
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'update':
                        $updateMemberArr = array(
                            'name' => $name,
                            'relationship' => $relationship,
                            'gender' => $gender,
                            'dob' => $dob,
                            'height' => $height,
                            'weight' => $weight,
                            'city' => $city,
                            'locality' => $locality,
                            'language' => $language,
                            'phone_number' => $phone_number
                        );
                        $this->Custom->update_where('patient_member', $updateMemberArr, array('id' => $member_id));

                        $member_record = $this->Custom->get_where('patient_member', array('id' => $member_id));
                        $member_language = "";
                        if ($member_record[0]->language != '') {
                            foreach ($member_record[0]->language as $lang) {
                                $language_record = $this->Custom->get_where('language', array('id' => $lang));
                                $member_language[] = array(
                                    'language_id' => $lang,
                                    'language' => ($language_record) ? $language_record[0]->name : ""
                                );
                            }
                        }
                        $member_record[0]->member_language = ($member_language) ? $member_language : array();

                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('member_updated');
                        $this->response->data = $member_record[0];
                        die(json_encode($this->response));
                        break;
                endswitch;
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AskQuestion() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $member_id = ($this->input->post('member_id')) ? $this->input->post('member_id') : '';
        $issue = ($this->input->post('issue')) ? $this->input->post('issue') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $dob = ($this->input->post('dob')) ? $this->input->post('dob') : '';
        $height = ($this->input->post('height')) ? $this->input->post('height') : '';
        $weight = ($this->input->post('weight')) ? $this->input->post('weight') : '';
        $city = ($this->input->post('city')) ? $this->input->post('city') : '';
        $locality = ($this->input->post('locality')) ? $this->input->post('locality') : '';
        $question1 = ($this->input->post('question1')) ? $this->input->post('question1') : '';
        $question2 = ($this->input->post('question2')) ? $this->input->post('question2') : '';
        $question3 = ($this->input->post('question3')) ? $this->input->post('question3') : '';
        $treatment_type = ($this->input->post('treatment_type')) ? $this->input->post('treatment_type') : '';
        if (!empty($user_id) && !empty($member_id) && !empty($issue) && !empty($gender) && !empty($dob) && !empty($city) && !empty($locality)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if (!empty($doctor_id)) {
                    $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                    if (empty($doctor_record)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('doctor_id_error');
                        die(json_encode($this->response));
                    }
                }
                $member_record = $this->Custom->get_where('patient_member', array('id' => $member_id, 'user_id' => $user_id, 'status' => 1));
                if (empty($member_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('member_id_error');
                    die(json_encode($this->response));
                }
                $quesArr = array(
                    'user_id' => $user_id,
                    'doctor_id' => $doctor_id,
                    'member_id' => $member_id,
                    'issue' => $issue,
                    'diagnose_condition' => $question1,
                    'medication' => $question2,
                    'allergies' => $question3,
                    'treatment_type' => $treatment_type,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('questions', $quesArr);
                if ($insert_id) {
                    $updateMemberArr = array(
                        'gender' => $gender,
                        'dob' => $dob,
                        'height' => $height,
                        'weight' => $weight,
                        'city' => $city,
                        'locality' => $locality
                    );
                    $this->Custom->update_where('patient_member', $updateMemberArr, array('id' => $member_id));

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('question_added');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function MemberAdditionalInfo() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $member_id = ($this->input->post('member_id')) ? $this->input->post('member_id') : '';
        $question = ($this->input->post('question')) ? $this->input->post('question') : '';
        $title = ($this->input->post('title')) ? $this->input->post('title') : '';
        $current_status = ($this->input->post('current_status')) ? $this->input->post('current_status') : '';
        $notes = ($this->input->post('notes')) ? $this->input->post('notes') : '';
        $_POST['created_at'] = strtotime(date("Y-m-d H:i:s"));
        unset($_POST['lang']);
        if (!empty($user_id) && !empty($member_id) && !empty($question) && !empty($title)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $member_record = $this->Custom->get_where('patient_member', array('id' => $member_id));
                if (empty($member_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('member_id_error');
                    die(json_encode($this->response));
                }
                $add_info_record = $this->Custom->get_where('member_additional_info', array('member_id' => $member_id, 'question' => $question, 'title' => $title));
                if (!empty($add_info_record)) {
                    $updateArr = array(
                        'current_status' => $current_status,
                        'notes' => $notes
                    );
                    $this->Custom->update_where('member_additional_info', $updateArr, array('id' => $add_info_record[0]->id));

                    $member_add_record = $this->Custom->get_where('member_additional_info', array('id' => $add_info_record[0]->id));
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('additional_info_updated');
                    $this->response->data = $member_add_record[0];
                    die(json_encode($this->response));
                } else {
                    $insert_id = $this->Custom->insert_data('member_additional_info', $_POST);
                    if ($insert_id) {
                        $member_add_record = $this->Custom->get_where('member_additional_info', array('id' => $insert_id));
                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('additional_info_added');
                        $this->response->data = $member_add_record[0];
                        die(json_encode($this->response));
                    } else {
                        $this->response->success = 202;
                        $this->response->message = $this->lang->line('went_wrong');
                        die(json_encode($this->response));
                    }
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DeleteMemberAdditionalInfo() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $member_id = ($this->input->post('member_id')) ? $this->input->post('member_id') : '';
        $add_info_id = ($this->input->post('add_info_id')) ? $this->input->post('add_info_id') : '';
        if (!empty($user_id) && !empty($member_id) && !empty($add_info_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $member_record = $this->Custom->get_where('patient_member', array('id' => $member_id));
                if (empty($member_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('member_id_error');
                    die(json_encode($this->response));
                }
                $add_info_record = $this->Custom->get_where('member_additional_info', array('member_id' => $member_id, 'id' => $add_info_id));
                if (empty($add_info_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_additional_info');
                    die(json_encode($this->response));
                }
                $del_status = $this->Custom->delete_where('member_additional_info', array('id' => $add_info_id));
                if ($del_status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('additional_info_deleted');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetAppointment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $today_date = date('Y-m-d');
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $current_time = date('H:i:s');
                $appointment_record = $this->Custom->query("select * from appointment where user_id='$user_id'");
                if ($appointment_record) {
                    foreach ($appointment_record as $value) {
                        if ($value->status == 1 && $value->doctor_accepted_date != '') {
                            $doctor_accepted_time = date('H:i:s', $value->doctor_accepted_date);
                            $doctor_accepted_date = date('Y-m-d', $value->doctor_accepted_date);
                            $payment_time = date('H:i:s', strtotime('+10 minutes', strtotime($doctor_accepted_time)));
                            if (strtotime($doctor_accepted_date) < strtotime(date('Y-m-d'))) {
                                $this->Custom->update_where('appointment', array('status' => 8), array('id' => $value->id));
                            } else {
                                if (strtotime($payment_time) < strtotime($current_time)) {
                                    $this->Custom->update_where('appointment', array('status' => 8), array('id' => $value->id));
                                }
                            }
                        }
                        if ($value->appointment_date == $today_date && ($value->status == 0 || $value->status == 1 || $value->status == 4)) {
                            if ($value->status == 4) {
                                if ($value->end_time < strtotime($current_time)) {
                                    $this->Custom->update_where('appointment', array('status' => 5, 'status_updated_by' => 'automatic'), array('id' => $value->id));
                                }
                            } else {
                                if ($value->end_time < strtotime($current_time)) {
                                    $this->Custom->update_where('appointment', array('status' => 9), array('id' => $value->id));
                                }
                            }
                        }
                        if ($value->appointment_date < $today_date && ($value->status == 0 || $value->status == 1 || $value->status == 4)) {
                            $this->Custom->update_where('appointment', array('status' => 9), array('id' => $value->id));
                        }
                    }

                    $appointment_rec = array();
                    if ($type == 'all')
                        $appointment_rec = $this->Custom->query("select * from appointment where user_id='$user_id' order by start_time ASC");
                    if ($type == 'online')
                        $appointment_rec = $this->Custom->query("select * from appointment where user_id='$user_id' AND (status = 0 OR status = 1 OR status = 4) AND (appointment_type = 1 OR appointment_type = 2 OR appointment_type = 3) order by start_time ASC");
                    if ($type == 'offline')
                        $appointment_rec = $this->Custom->query("select * from appointment where user_id='$user_id' AND (status = 0 OR status = 1 OR status = 4) AND appointment_type = 0 order by start_time ASC");
                    if ($type == 'invite')
                        $appointment_rec = $this->Custom->query("select * from appointment where user_id='$user_id' AND (status = 0 OR status = 1 OR status = 4) AND appointment_type = 4 order by start_time ASC");
                    if ($type == 'completed')
                        $appointment_rec = $this->Custom->query("select * from appointment where user_id='$user_id' AND (status = 2 || status = 3 || status = 5 || status = 6 || status = 7 || status = 8  || status = 9)  order by id DESC");

                    if ($appointment_rec) {
                        foreach ($appointment_rec as $value) {
                            $AppintmentDoc = array();
                            if ($value->appointment_doc != '') {
                                $appointment_doc = explode(',', $value->appointment_doc);
                                foreach ($appointment_doc as $doc) {
                                    $AppintmentDoc[] = base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $doc;
                                }
                            }
                            //get relationship
                            $value->member_relationship = "";
                            if ($value->user_type == "patient_member") {
                                $patient_member = $this->Custom->get_where('patient_member', array('id' => $value->member_id));
                                $value->member_relationship = ($patient_member) ? $patient_member[0]->relationship : "";
                                $value->member_relationship = ($patient_member) ? $patient_member[0]->relationship : "";
                            }
                            $value->AppintmentDoc = $AppintmentDoc;
                            $where = "users.id = '$value->doctor_id'";
                            $doctor_record = $this->PatientModel->SearchDoctors($where);
                            if ($doctor_record) {
                                $value->title = $doctor_record[0]->title;
                                $value->doctor_name = $doctor_record[0]->name;
                                $value->doctor_gender = $doctor_record[0]->gender;
                                $value->doctor_phone_number = $doctor_record[0]->phone_number;
                                $value->profile_image = ($doctor_record[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_record[0]->profile_image : "";
                                $value->clinic_name = $doctor_record[0]->clinic_name;
                                $value->clinic_phone_number = $doctor_record[0]->clinic_phone_number;
                                $value->clinic_address = $doctor_record[0]->clinic_address;
                                $value->clinic_city = $doctor_record[0]->clinic_city;
                                $value->clinic_locality = $doctor_record[0]->clinic_locality;
                                $value->clinic_pincode = $doctor_record[0]->clinic_pincode;
                                $value->clinic_state = $doctor_record[0]->clinic_state;
                                $value->clinic_lat = $doctor_record[0]->clinic_lat;
                                $value->clinic_lng = $doctor_record[0]->clinic_lng;
                                $consultation_settings = $this->Custom->query("select * from consultation_settings where user_id='$value->doctor_id' AND type='basic'");
                                if ($consultation_settings) {
                                    if ($value->appointment_type == 1 || $value->appointment_type == 2 || $value->appointment_type == 3) {
                                        $value->consult_status = $consultation_settings[0]->online_consult_status;
                                        $value->consult_charge = $consultation_settings[0]->online_consult_charge;
                                        $value->consult_time = $consultation_settings[0]->online_consult_time;
                                    }
                                    if ($value->appointment_type == 0) {
                                        $value->consult_status = $consultation_settings[0]->offline_consult_status;
                                        $value->consult_charge = $consultation_settings[0]->offline_consult_charge;
                                        $value->consult_time = $consultation_settings[0]->offline_consult_time;
                                    }
                                    if ($value->appointment_type == 4) {
                                        $value->consult_status = $consultation_settings[0]->invite_consult_status;
                                        $value->consult_charge = $consultation_settings[0]->invite_consult_charge;
                                        $value->consult_time = $consultation_settings[0]->invite_consult_time;
                                    }
                                } else {
                                    $value->consult_status = "";
                                    $value->consult_charge = "";
                                    $value->consult_time = "";
                                }
                                //get unread message count
                                $unread_message_count = 0;
                                $chat_message_rec = $this->Custom->query("select * from chat_message where appointment_id = '" . $value->id . "' AND receiver_id = '" . $user_id . "' AND message_status = 0");
                                if (!empty($chat_message_rec)) {
                                    $unread_message_count = count($chat_message_rec);
                                } else {
                                    $unread_message_count = 0;
                                }
                                $value->unread_message_count = $unread_message_count;
                            }
                        }
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('appointment_list');
                    $this->response->data = ($appointment_rec) ? $appointment_rec : array();
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record_appointment');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetAppointmentDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $today_date = date('Y-m-d');
        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->query("select * from appointment where user_id = $user_id AND id = $appointment_id");
                if (empty($appointment_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                $appointment_record = $this->Custom->query("select * from appointment where user_id = $user_id");
                if ($appointment_record) {
                    foreach ($appointment_record as $value) {
                        if ($value->status == 1 && $value->doctor_accepted_date != '') {
                            $doctor_accepted_time = date('H:i:s', $value->doctor_accepted_date);
                            $doctor_accepted_date = date('Y-m-d', $value->doctor_accepted_date);
                            $payment_time = date('H:i:s', strtotime('+10 minutes', strtotime($doctor_accepted_time)));
                            $current_time = date('H:i:s');
                            if (strtotime($doctor_accepted_date) < strtotime(date('Y-m-d'))) {
                                $this->Custom->update_where('appointment', array('status' => 8), array('id' => $value->id));
                            } else {
                                if (strtotime($payment_time) < strtotime($current_time)) {
                                    $this->Custom->update_where('appointment', array('status' => 8), array('id' => $value->id));
                                }
                            }
                        }
                        if ($value->appointment_date < $today_date && ($value->status == 0 || $value->status == 1 || $value->status == 4)) {
                            $this->Custom->update_where('appointment', array('status' => 9), array('id' => $value->id));
                        }
                    }
                }
                $where = "users.id = '" . $appointment_rec[0]->doctor_id . "'";
                $doctor_record = $this->PatientModel->SearchDoctors($where);
                if ($doctor_record) {
                    $appointment_rec[0]->title = $doctor_record[0]->title;
                    $appointment_rec[0]->doctor_name = $doctor_record[0]->name;
                    $appointment_rec[0]->doctor_gender = $doctor_record[0]->gender;
                    $appointment_rec[0]->doctor_phone_number = $doctor_record[0]->phone_number;
                    $appointment_rec[0]->profile_image = ($doctor_record[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_record[0]->profile_image : "";
                    $appointment_rec[0]->clinic_name = $doctor_record[0]->clinic_name;
                    $appointment_rec[0]->clinic_phone_number = $doctor_record[0]->clinic_phone_number;
                    $appointment_rec[0]->clinic_address = $doctor_record[0]->clinic_address;
                    $appointment_rec[0]->clinic_city = $doctor_record[0]->clinic_city;
                    $appointment_rec[0]->clinic_locality = $doctor_record[0]->clinic_locality;
                    $appointment_rec[0]->clinic_pincode = $doctor_record[0]->clinic_pincode;
                    $appointment_rec[0]->clinic_state = $doctor_record[0]->clinic_state;
                    $appointment_rec[0]->clinic_lat = $doctor_record[0]->clinic_lat;
                    $appointment_rec[0]->clinic_lng = $doctor_record[0]->clinic_lng;
                    $consultation_settings = $this->Custom->query("select * from consultation_settings where user_id='$value->doctor_id' AND type='basic'");
                    if ($consultation_settings) {
                        if ($appointment_rec[0]->appointment_type == 1 || $appointment_rec[0]->appointment_type == 2 || $appointment_rec[0]->appointment_type == 3) {
                            $appointment_rec[0]->consult_status = $consultation_settings[0]->online_consult_status;
                            $appointment_rec[0]->consult_charge = $consultation_settings[0]->online_consult_charge;
                            $appointment_rec[0]->consult_time = $consultation_settings[0]->online_consult_time;
                        }
                        if ($appointment_rec[0]->appointment_type == 0) {
                            $appointment_rec[0]->consult_status = $consultation_settings[0]->offline_consult_status;
                            $appointment_rec[0]->consult_charge = $consultation_settings[0]->offline_consult_charge;
                            $appointment_rec[0]->consult_time = $consultation_settings[0]->offline_consult_time;
                        }
                        if ($appointment_rec[0]->appointment_type == 4) {
                            $appointment_rec[0]->consult_status = $consultation_settings[0]->invite_consult_status;
                            $appointment_rec[0]->consult_charge = $consultation_settings[0]->invite_consult_charge;
                            $appointment_rec[0]->consult_time = $consultation_settings[0]->invite_consult_time;
                        }
                    } else {
                        $appointment_rec[0]->consult_status = "";
                        $appointment_rec[0]->consult_charge = "";
                        $appointment_rec[0]->consult_time = "";
                    }
                }
                $AppintmentDoc = array();
                if ($appointment_rec[0]->appointment_doc != '') {
                    $appointment_doc = explode(',', $appointment_rec[0]->appointment_doc);
                    foreach ($appointment_doc as $doc) {
                        $AppintmentDoc[] = base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $doc;
                    }
                }
                $appointment_rec[0]->AppintmentDoc = $AppintmentDoc;
                //get relationship
                $appointment_rec[0]->member_relationship = "";
                if ($appointment_rec[0]->user_type == "patient_member") {
                    $patient_member = $this->Custom->get_where('patient_member', array('id' => $appointment_rec[0]->member_id));
                    $appointment_rec[0]->member_relationship = ($patient_member) ? $patient_member[0]->relationship : "";
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('appointment_details');
                $this->response->data = $appointment_rec[0];
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetQuestions() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                //$questions_record = $this->Custom->get_where('questions', array('user_id' => $user_id));
                $questions_record = $this->Custom->query("select * from questions where user_id = $user_id ORDER BY id DESC");
                if ($questions_record) {
                    foreach ($questions_record as $row) {
                        $answers_record = $this->Custom->get_where('answers', array('question_id' => $row->id));
                        if ($answers_record) {
                            foreach ($answers_record as $ans) {
                                $where = array('users.id' => $ans->doctor_id, 'users.role' => 'doctor');
                                $doctor_data = $this->PatientModel->SearchDoctors($where);
                                if (!empty($doctor_data)) {
                                    if ($doctor_data[0]->speciality != '' || $doctor_data[0]->speciality != 0) {
                                        $specility_record = $this->Custom->get_where('specility', array('id' => $doctor_data[0]->speciality));
                                        if ($specility_record) {
                                            if ($_POST['lang'] == 'en') {
                                                $speciality_name = $specility_record[0]->specility_name;
                                            } else if ($_POST['lang'] == 'ru') {
                                                $speciality_name = $specility_record[0]->specility_name_ru;
                                            } else {
                                                $speciality_name = $specility_record[0]->specility_name;
                                            }
                                        } else
                                            $speciality_name = "";
                                    }else {
                                        $speciality_name = "";
                                    }
                                    $title = $doctor_data[0]->title;
                                    $name = $doctor_data[0]->name;
                                    $profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                                }

                                $anser_data[] = array(
                                    'doctor_id' => $ans->doctor_id,
                                    'reply' => $ans->reply,
                                    'created_at' => $ans->created_at,
                                    'speciality_name' => $speciality_name,
                                    'name' => $name,
                                    'title' => $title,
                                    'profile' => $profile_image
                                );
                            }
                        } else {
                            $anser_data = array();
                        }
                        $row->answers = $anser_data;
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('question_list');
                    $this->response->data = $questions_record;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('question_no_record');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function SendTreatmentEnquiry() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $uid = ($this->input->post('uid')) ? $this->input->post('uid') : '';
        $promo_id = ($this->input->post('promo_id')) ? $this->input->post('promo_id') : 0;
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $treatment_id = ($this->input->post('treatment_id')) ? $this->input->post('treatment_id') : '';
        $duration = ($this->input->post('duration')) ? $this->input->post('duration') : '';
        $query_details = ($this->input->post('query_details')) ? $this->input->post('query_details') : '';
        $discount_amt = ($this->input->post('discount_amt')) ? $this->input->post('discount_amt') : 0;

        if (!empty($user_id) && !empty($doctor_id) && !empty($duration) && !empty($query_details)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                if (empty($doctor_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('doctor_id_error');
                    die(json_encode($this->response));
                }
                if (!empty($treatment_id)) {
                    $treatment_record = $this->Custom->get_where('treatment', array('id' => $treatment_id));
                    if (empty($treatment_record)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('invalid_treatment');
                        die(json_encode($this->response));
                    }
                }
                $consultation_settings = $this->Custom->get_where('consultation_settings', array('user_id' => $doctor_id));
                $total_amount = $consultation_settings[0]->enquiry_consult_charge;
                $amount = $total_amount - $discount_amt;
                $insertArr = array(
                    'uid' => $uid,
                    'doctor_id' => $doctor_id,
                    'treatment_id' => $treatment_id,
                    'patient_id' => $user_id,
                    'duration' => $duration,
                    'query_details' => $query_details,
                    'total_amount' => $total_amount,
                    'amount' => $amount,
                    'discount_amt' => $discount_amt,
                    'promo_id' => $promo_id,
                    'promo_uses_date' => strtotime(date("Y-m-d H:i:s")),
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('treatment_enquiry', $insertArr);
                if ($insert_id) {
                    //update transaction table
                    $this->Custom->update_where('transaction', array('user_id' => $user_id, 'doctor_id' => $doctor_id), array('uid' => $uid));
                    //send notification to doctor
                    $this->DoctorNotification('enquiry', $user_id, $doctor_id, 'patient', $insert_id, '', '', '');

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('send_treatment');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetTreatment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treatment_record = $this->Custom->get_where('treatment');
                if (empty($treatment_record)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('treatment_list');
                $this->response->data = $treatment_record;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function HomeScreenData() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $timezone = ($this->input->post('timezone')) ? $this->input->post('timezone') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if (!empty($timezone))
                    $this->Custom->update_where('users', array('timezone' => $timezone), array('id' => $user_id));
                //specility data
                $app_home_screen_setting = $this->Custom->get_where('app_home_screen_setting');
                if ($app_home_screen_setting[0]->speciality != "") {
                    $SelectedSpeciality = explode(',', $app_home_screen_setting[0]->speciality);
                    foreach ($SelectedSpeciality as $SS) {
                        $specility_record = $this->Custom->query('select * from specility where parent_id = 0 AND id = "' . $SS . '"');
                        if ($_POST['lang'] == 'en') {
                            $specility_record1[] = array(
                                'id' => $specility_record[0]->id,
                                'specility_name' => $specility_record[0]->specility_name,
                                'image' => ($specility_record[0]->image) ? base_url() . SPECIALITY_URL . '/' . $specility_record[0]->image : ""
                            );
                        } else if ($_POST['lang'] == 'ru') {
                            $specility_record1[] = array(
                                'id' => $specility_record[0]->id,
                                'specility_name' => $specility_record[0]->specility_name_ru,
                                'image' => ($specility_record[0]->image) ? base_url() . SPECIALITY_URL . '/' . $specility_record[0]->image : ""
                            );
                        } else {
                            $specility_record1[] = array(
                                'id' => $specility_record[0]->id,
                                'specility_name' => $specility_record[0]->specility_name,
                                'image' => ($specility_record[0]->image) ? base_url() . SPECIALITY_URL . '/' . $specility_record[0]->image : ""
                            );
                        }
                    }
                }

                $specility = ($specility_record1) ? $specility_record1 : array();
                //diseases data
                $diseases_record = $this->Custom->query('select * from diseases LIMIT 0,4');
                $diseases = ($diseases_record) ? $diseases_record : array();
                //doctor data
                $app_home_screen_setting = $this->Custom->get_where('app_home_screen_setting');
                if ($app_home_screen_setting[0]->doctors != "") {
                    $SelectedDoctors = explode(',', $app_home_screen_setting[0]->doctors);
                    foreach ($SelectedDoctors as $SD) {
                        $where = "users.id = $SD";
                        $result = $this->PatientModel->SearchDoctors($where);
                        $result[0]->profile_image = ($result[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $result[0]->profile_image : "";
                        if ($result[0]->speciality != '') {
                            $speciality_data = array();
                            $speciality = explode(',', $result[0]->speciality);
                            foreach ($speciality as $spe) {
                                $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                if ($specility_record) {
                                    if ($_POST['lang'] == 'en') {
                                        $speciality_data[] = array(
                                            'id' => $specility_record[0]->id,
                                            'specility_name' => $specility_record[0]->specility_name
                                        );
                                    } else if ($_POST['lang'] == 'ru') {
                                        $speciality_data[] = array(
                                            'id' => $specility_record[0]->id,
                                            'specility_name' => $specility_record[0]->specility_name_ru
                                        );
                                    } else {
                                        $speciality_data[] = array(
                                            'id' => $specility_record[0]->id,
                                            'specility_name' => $specility_record[0]->specility_name
                                        );
                                    }
                                } else {
                                    $speciality_data[] = array(
                                        'specility_id' => $spe,
                                        'specility_name' => ""
                                    );
                                }
                            }
                            $result[0]->speciality = $speciality_data;
                        } else {
                            $result[0]->speciality = array();
                        }

                        $doctor_data[] = array(
                            'user_id' => $result[0]->user_id,
                            'profile_image' => $result[0]->profile_image,
                            'name' => $result[0]->name,
                            'speciality' => $result[0]->speciality
                        );
                    }
                }
                //banners data
                $bannersData = array();
                $app_banners = $this->Custom->get_where('app_banners');
                if ($app_banners) {
                    $appBannersArr = array_slice($app_banners, 0, 5);
                    foreach ($appBannersArr as $banner) {
                        $bannersData[] = (object) array(
                                    'banner_image' => ($banner->banner_image) ? base_url() . APP_BANNER_URL . $banner->banner_image : "",
                                    'banner_url' => $banner->banner_url
                        );
                    }
                }

                $data = (object) array(
                            'specility' => $specility,
                            'diseases' => $diseases,
                            'doctors' => ($doctor_data) ? $doctor_data : array(),
                            'banners' => $bannersData
                );
                $this->response->success = 200;
                $this->response->message = $this->lang->line('home_details');
                $this->response->data = $data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetTreatmentEnquiry() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treatment_enquiry_record = $this->Custom->query("select * from treatment_enquiry where patient_id = $user_id order by id DESC");
                if (empty($treatment_enquiry_record)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }
                /* foreach ($treatment_enquiry_record as $row) {
                  $treatments = $this->Custom->get_where('treatment', array('id' => $row->treatment_id));
                  $row->treatment_name = ($treatments) ? $treatments[0]->name : "";
                  $where = array('users.id' => $row->doctor_id, 'users.role' => 'doctor');
                  $doctor_data = $this->PatientModel->SearchDoctors($where);
                  $title = $doctor_data[0]->title;
                  $name = $doctor_data[0]->name;
                  $profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                  $row->title = $title;
                  $row->name = $name;
                  $row->profile_image = $profile_image;
                  } */

                foreach ($treatment_enquiry_record as $row) {
                    $treatments = $this->Custom->get_where('treatment', array('id' => $row->treatment_id));
                    $treatment_name = ($treatments) ? $treatments[0]->name : "";

                    $conversaton_details = $this->Custom->query("select enquiry_conversations.id as conversation_id,enquiry_messages.* from enquiry_conversations INNER JOIN enquiry_messages ON enquiry_conversations.id = enquiry_messages.conversation_id where enquiry_conversations.enquiry_id = $row->id ORDER BY enquiry_messages.id DESC");
                    if (!empty($conversaton_details)) {
                        if ($conversaton_details[0]->sender_type == 'patient') {
                            $where = array('users.id' => $conversaton_details[0]->receiver_id, 'users.role' => 'doctor');
                            $doctor_data = $this->PatientModel->SearchDoctors($where);
                            if ($doctor_data) {
                                $doctor_id = $conversaton_details[0]->receiver_id;
                                $title = $doctor_data[0]->title;
                                $name = $doctor_data[0]->name;
                                $profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                            } else {
                                $doctor_id = $conversaton_details[0]->receiver_id;
                                $title = "";
                                $name = "";
                                $profile_image = "";
                            }
                        }
                        if ($conversaton_details[0]->sender_type == 'doctor') {
                            $where = array('users.id' => $conversaton_details[0]->sender_id, 'users.role' => 'doctor');
                            $doctor_data = $this->PatientModel->SearchDoctors($where);
                            if ($doctor_data) {
                                $doctor_id = $conversaton_details[0]->sender_id;
                                $title = $doctor_data[0]->title;
                                $name = $doctor_data[0]->name;
                                $profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                            } else {
                                $doctor_id = $conversaton_details[0]->sender_id;
                                $title = "";
                                $name = "";
                                $profile_image = "";
                            }
                        }
                        if ($conversaton_details[0]->message_type != 'text')
                            $conversaton_details[0]->message = ($conversaton_details[0]->message) ? base_url() . ENQUIRY_MESSAGE_URL . '/' . $conversaton_details[0]->message : "";
                        $all_data[] = array(
                            'enquiry_id' => $row->id,
                            'sender_id' => $conversaton_details[0]->sender_id,
                            'receiver_id' => $conversaton_details[0]->receiver_id,
                            'sender_type' => $conversaton_details[0]->sender_type,
                            'message' => $conversaton_details[0]->message,
                            'message_type' => $conversaton_details[0]->message_type,
                            'sent_at' => $conversaton_details[0]->sent_at,
                            'timestamp' => $conversaton_details[0]->sent_at,
                            'treatment_name' => $treatment_name,
                            'title' => $title,
                            'name' => $name,
                            'doctor_id' => $doctor_id,
                            'profile_image' => $profile_image
                        );
                    } else {
                        $where = array('users.id' => $row->doctor_id, 'users.role' => 'doctor');
                        $doctor_data = $this->PatientModel->SearchDoctors($where);
                        $title = $doctor_data[0]->title;
                        $name = $doctor_data[0]->name;
                        $profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                        $all_data[] = array(
                            'enquiry_id' => $row->id,
                            'sender_id' => $row->patient_id,
                            'receiver_id' => $row->doctor_id,
                            'sender_type' => 'patient',
                            'message' => $row->query_details,
                            'message_type' => 'text',
                            'sent_at' => $row->created_at,
                            'timestamp' => $row->created_at,
                            'treatment_name' => $treatment_name,
                            'title' => $title,
                            'name' => $name,
                            'doctor_id' => $row->doctor_id,
                            'profile_image' => $profile_image
                        );
                    }
                }


                $this->response->success = 200;
                $this->response->message = $this->lang->line('treatment_enquiry_list');
                $this->response->data = $all_data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function SaveUnsaveFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        $status = ($this->input->post('status')) ? $this->input->post('status') : '';
        $saved_feed_id = ($this->input->post('saved_feed_id')) ? $this->input->post('saved_feed_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($status):
                    case 0:
                        if (empty($saved_feed_id)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $saved_patient_feed_record = $this->Custom->get_where('saved_patient_feed', array('id' => $saved_feed_id));
                        if (empty($saved_patient_feed_record)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('invalid_saved_feed_id');
                            die(json_encode($this->response));
                        }
                        $status = $this->Custom->delete_where('saved_patient_feed', array('id' => $saved_feed_id));
                        if ($status) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('feed_unsave');
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 1:
                        if (empty($feed_id)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $feed_record = $this->Custom->get_where('doctor_feed', array('id' => $feed_id));
                        if (empty($feed_record)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('invalid_feed');
                            die(json_encode($this->response));
                        }
                        $saved_patient_feed_record = $this->Custom->get_where('saved_patient_feed', array('user_id' => $user_id, 'feed_id' => $feed_id));
                        if (!empty($saved_patient_feed_record)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('feed_already_save');
                            die(json_encode($this->response));
                        }
                        $insertArr = array(
                            'user_id' => $user_id,
                            'feed_id' => $feed_id,
                            'created_at' => strtotime(date("Y-m-d H:i:s"))
                        );
                        $insert_id = $this->Custom->insert_data('saved_patient_feed', $insertArr);
                        if ($insert_id) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('feed_save');
                            $this->response->save_feed_id = $insert_id;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                endswitch;
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetSavedFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $saved_patient_feed_record = $this->Custom->get_where('saved_patient_feed', array('user_id' => $user_id));
                if (empty($saved_patient_feed_record)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }

                foreach ($saved_patient_feed_record as $row) {
                    $feed_record = $this->PatientModel->GetSavedFeeds(array('doctor_feed.id' => $row->feed_id));
                    if ($feed_record) {
                        //12 June 2018
                        if ($feed_record[0]->speciality != '') {
                            $speciality_data = array();
                            $speciality = explode(',', $feed_record[0]->speciality);
                            foreach ($speciality as $spe) {
                                $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                if ($specility_record) {
                                    if ($_POST['lang'] == 'en') {
                                        $speciality_data[] = array(
                                            'id' => $specility_record[0]->id,
                                            'specility_name' => $specility_record[0]->specility_name
                                        );
                                    } else if ($_POST['lang'] == 'ru') {
                                        $speciality_data[] = array(
                                            'id' => $specility_record[0]->id,
                                            'specility_name' => $specility_record[0]->specility_name_ru
                                        );
                                    } else {
                                        $speciality_data[] = array(
                                            'id' => $specility_record[0]->id,
                                            'specility_name' => $specility_record[0]->specility_name
                                        );
                                    }
                                } else {
                                    $speciality_data[] = array(
                                        'specility_id' => $spe,
                                        'specility_name' => ""
                                    );
                                }
                            }
                            $feed_record[0]->speciality = $speciality_data;
                        } else {
                            $feed_record[0]->speciality = array();
                        }

                        //End here
                        //like dislike status
                        $feed_status_record = $this->Custom->get_where('like_dislike_doctor_feed', array('feed_id' => $row->feed_id, 'user_id' => $user_id));
                        if ($feed_status_record)
                            $feed_record[0]->feed_like_dislike_status = $feed_status_record[0]->status;
                        else
                            $feed_record[0]->feed_like_dislike_status = "0";
                        //save feed status
                        $saved_patient_feed_record = $this->Custom->get_where('saved_patient_feed', array('feed_id' => $row->feed_id, 'user_id' => $user_id));
                        if (!empty($saved_patient_feed_record)) {
                            $feed_record[0]->save_feed_status = "1";
                            $feed_record[0]->saved_feed_id = $saved_patient_feed_record[0]->id;
                        } else {
                            $feed_record[0]->save_feed_status = "0";
                            $feed_record[0]->saved_feed_id = "";
                        }
                        $feed_record[0]->image = ($feed_record[0]->image) ? base_url() . DOCTOR_FEED_URL . '/' . $feed_record[0]->image : "";
                        $feed_record[0]->profile_image = ($feed_record[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $feed_record[0]->profile_image : "";
                        $feed_record[0]->timestamp = strtotime($feed_record[0]->created_at);
                        $feed_record[0]->like_count = GetTotalCount('like_dislike_doctor_feed', array('feed_id' => $feed_record[0]->id, 'status' => 1));
                        $feed_record[0]->comment_count = GetTotalCount('comment_doctor_feed', array('feed_id' => $feed_record[0]->id));
                        if ($_POST['lang'] == 'en') {
                            $feed_record[0]->category_name = $feed_record[0]->category_name_en;
                        } else if ($_POST['lang'] == 'ru') {
                            $feed_record[0]->category_name = $feed_record[0]->category_name_ru;
                        } else {
                            $feed_record[0]->category_name = $feed_record[0]->category_name_en;
                        }
                        $feed_record[0]->speciality = $feed_record[0]->speciality;
                        unset($feed_record[0]->category_name_ru);
                        unset($feed_record[0]->category_name_en);
                        $row->feed = $feed_record[0];
                        $feed_details[] = $row;
                    }
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('saved_feed_list');
                $this->response->data = $feed_details;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AddDocument() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $document = ($_FILES['document']) ? $_FILES['document'] : "";

        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if (!empty($_FILES['document']['name'])) {
                    $name = $_FILES['document']['name'];
                    $ext = end((explode(".", $name)));
                    $new_name = time() . '.' . $ext;
                    $config['file_name'] = $new_name;
                    $config['upload_path'] = PATIENT_MEDICAL_DOCUMENT_PATH . '/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $config['remove_spaces'] = TRUE;

                    if (!is_dir($config['upload_path']))
                        die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('document')) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('docu_not_upload');
                        die(json_encode($this->response));
                    } else {
                        $image_name = $new_name;
                    }
                    $insertArr = array(
                        'patient_id' => $user_id,
                        'image' => $image_name,
                        'type' => $type,
                        'created_at' => strtotime(date("Y-m-d H:i:s"))
                    );
                    $insert_id = $this->Custom->insert_data('patients_document', $insertArr);
                    if ($insert_id) {
                        $patients_document = $this->Custom->get_where('patients_document', array('id' => $insert_id));
                        $patients_document[0]->image = ($patients_document[0]->image) ? base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $patients_document[0]->image : "";
                        $data = (object) array(
                                    'doc_id' => $patients_document[0]->id,
                                    'user_id' => $patients_document[0]->patient_id,
                                    'document' => $patients_document[0]->image,
                                    'type' => $patients_document[0]->type,
                        );
                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('docu_added');
                        $this->response->data = $data;
                        die(json_encode($this->response));
                    } else {
                        $this->response->success = 202;
                        $this->response->message = $this->lang->line('went_wrong');
                        die(json_encode($this->response));
                    }
                } else {
                    $this->response->success = 201;
                    $this->response->message = $this->lang->line('required_field');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DeleteDocument() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $document_id = ($this->input->post('document_id')) ? $this->input->post('document_id') : '';

        if (!empty($user_id) && !empty($document_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $document_record = $this->Custom->get_where('patients_document', array('id' => $document_id, 'patient_id' => $user_id));
                if (empty($document_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_docu_id');
                    die(json_encode($this->response));
                }
                unlink(PATIENT_MEDICAL_DOCUMENT_PATH . '/' . $document_record[0]->image);
                $status = $this->Custom->delete_where('patients_document', array('id' => $document_id));
                if ($status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('docu_deleted');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetDocuments() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patients_document_record = $this->Custom->get_where('patients_document', array('patient_id' => $user_id));
                if (empty($patients_document_record)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }

                foreach ($patients_document_record as $row) {
                    $row->image = ($row->image) ? base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $row->image : "";
                    $data[] = array(
                        'doc_id' => $row->id,
                        'user_id' => $row->patient_id,
                        'document' => $row->image,
                        'type' => $row->type
                    );
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('docu_list');
                $this->response->data = $data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function SendMessage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $enquiry_id = ($this->input->post('enquiry_id')) ? $this->input->post('enquiry_id') : '';
        $message_type = ($this->input->post('message_type')) ? $this->input->post('message_type') : '';

        if (!empty($user_id) && !empty($doctor_id) && !empty($enquiry_id) && !empty($message_type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                if (empty($doctor_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('doctor_id_error');
                    die(json_encode($this->response));
                }
                $treatment_enquiry_record = $this->Custom->get_where('treatment_enquiry', array('id' => $enquiry_id, 'patient_id' => $user_id));
                if (empty($treatment_enquiry_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_enquiry');
                    die(json_encode($this->response));
                }
                $enquiry_conversations_record = $this->Custom->query('select * from enquiry_conversations where user_id = "' . $user_id . '" AND doctor_id = "' . $doctor_id . '" AND enquiry_id = "' . $enquiry_id . '"');
                if (empty($enquiry_conversations_record)) {
                    $insertArr = array(
                        'enquiry_id' => $enquiry_id,
                        'user_id' => $user_id,
                        'doctor_id' => $doctor_id,
                        'created_at' => strtotime(date("Y-m-d H:i:s"))
                    );
                    $conversation_id = $this->Custom->insert_data('enquiry_conversations', $insertArr);
                } else {
                    $conversation_id = $enquiry_conversations_record[0]->id;
                }
                if ($message_type == 'text') {
                    $message = ($this->input->post('message')) ? $this->input->post('message') : '';
                    if (empty($message)) {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                }
                if ($message_type == 'image') {
                    if (!empty($_FILES['message']['name'])) {
                        $name = $_FILES['message']['name'];
                        $ext = end((explode(".", $name)));
                        $new_name = time() . '.' . $ext;
                        $config['file_name'] = $new_name;
                        $config['upload_path'] = ENQUIRY_MESSAGE_PATH . '/';
                        $config['allowed_types'] = '*';
                        $config['overwrite'] = TRUE;
                        $config['remove_spaces'] = TRUE;

                        if (!is_dir($config['upload_path']))
                            die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('message')) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('file_not_upload');
                            die(json_encode($this->response));
                        } else {
                            $message = $new_name;
                        }
                    } else {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                }
                $insertMessageArr = array(
                    'conversation_id' => $conversation_id,
                    'sender_id' => $user_id,
                    'receiver_id' => $doctor_id,
                    'sender_type' => 'patient',
                    'message' => $message,
                    'message_type' => $message_type,
                    'sent_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('enquiry_messages', $insertMessageArr);
                if ($insert_id) {
                    $patient_profile = $this->Custom->get_where('patient_profile', array('user_id' => $user_id));
                    $doctor_profile = $this->Custom->get_where('doctor_profile', array('user_id' => $doctor_id));
                    if ($message_type == 'image') {
                        $message = ($message) ? base_url() . ENQUIRY_MESSAGE_URL . '/' . $message : "";
                    }
                    $response_data = array(
                        'enquiry_id' => $enquiry_id,
                        'sender_id' => $user_id,
                        'sender_name' => $user_record[0]->name,
                        'sender_profile_image' => ($patient_profile[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_profile[0]->profile_image : "",
                        'receiver_id' => $doctor_id,
                        'receiver_name' => $doctor_record[0]->title . " " . $doctor_record[0]->name,
                        'receiver_profile_image' => ($doctor_profile[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_profile[0]->profile_image : "",
                        'sender_type' => 'patient',
                        'message' => $message,
                        'message_type' => $message_type,
                        'sent_at' => strtotime(date("Y-m-d H:i:s")),
                        'timestamp' => strtotime(date("Y-m-d H:i:s"))
                    );
//send notification
                    define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
                    $receiver_data = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                    $patient_profile = $this->Custom->get_where('patient_profile', array('user_id' => $user_id));
                    $message = $this->lang->line('enquiry_new_message');
                    $sender_name = $user_record[0]->name;
                    $sender_profile_image = ($patient_profile[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_profile[0]->profile_image : "";
                    if (isset($receiver_data) && !empty($receiver_data)) {
                        if ($receiver_data[0]->device_type == "android") {
                            if (!empty($receiver_data[0]->device_token)) {
                                $registatoin_ids = array($receiver_data[0]->device_token);
                                $message_data = array("message" => $message, 'notification_type' => 'enquiry', 'id' => $enquiry_id, 'patient_id' => $user_id, 'patient_name' => $sender_name);

                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $fields = array(
                                    'registration_ids' => $registatoin_ids,
                                    'data' => $message_data,
                                );
                                $headers = array(
                                    'Authorization: key=' . API_KEY,
                                    'Content-Type: application/json'
                                );

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                                $result = curl_exec($ch);
                                $res_array = json_decode($result);
                            }
                        } else {
                            $app_state = $this->app_state;
                            //$app_state = "";
                            $deviceToken = $receiver_data[0]->device_token;
                            $body['aps'] = array(
                                'alert' => array(
//'title' => "You have a notification",
                                    'content-available' => 1,
                                    'body' => $message,
                                ),
                                'badge' => 1,
                                'notification_type' => 'enquiry',
                                'id' => $enquiry_id,
                                'patient_id' => $user_id,
                                'patient_name' => $sender_name,
                                'sound' => 'default',
                            );

                            $passphrase = '123456789';
                            $ctx = stream_context_create();
                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                            } else {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                            }
                            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                            } else {
                                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                            }
                            $payload = json_encode($body);
                            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                            $result = fwrite($fp, $msg, strlen($msg));
                            fclose($fp);
                        }
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('message_send');
                    $this->response->data = $response_data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function EnquiryDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $enquiry_id = ($this->input->post('enquiry_id')) ? $this->input->post('enquiry_id') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id) && !empty($enquiry_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }

                $treatment_enquiry = $this->Custom->get_where('treatment_enquiry', array('patient_id' => $user_id, 'id' => $enquiry_id));
                if (!empty($treatment_enquiry)) {
                    $this->load->model('DoctorModel');
                    $patient_data = $this->PatientModel->GetPatientProfile($treatment_enquiry[0]->patient_id);
                    if ($patient_data) {
                        $sender_name = $patient_data[0]->name;
                        $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                    } else {
                        $sender_name = "";
                        $sender_profile_image = "";
                    }
                    $where = array('users.id' => $treatment_enquiry[0]->doctor_id, 'users.role' => 'doctor');
                    $doctor_data = $this->PatientModel->SearchDoctors($where);
                    if ($doctor_data) {
                        $receiver_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                        $receiver_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                    } else {
                        $receiver_name = "";
                        $receiver_profile_image = "";
                    }
                    $all_data[] = array(
                        'enquiry_id' => $treatment_enquiry[0]->id,
                        'sender_id' => $treatment_enquiry[0]->patient_id,
                        'sender_name' => $sender_name,
                        'sender_profile_image' => $sender_profile_image,
                        'receiver_id' => $treatment_enquiry[0]->doctor_id,
                        'receiver_name' => $receiver_name,
                        'receiver_profile_image' => $receiver_profile_image,
                        'sender_type' => 'patient',
                        'message' => $treatment_enquiry[0]->query_details,
                        'message_type' => 'text',
                        'sent_at' => $treatment_enquiry[0]->created_at,
                        'timestamp' => $treatment_enquiry[0]->created_at
                    );
                    $conversaton_details = $this->Custom->query("select enquiry_conversations.id as conversation_id,enquiry_messages.* from enquiry_conversations INNER JOIN enquiry_messages ON enquiry_conversations.id = enquiry_messages.conversation_id where enquiry_conversations.enquiry_id = '" . $enquiry_id . "' ORDER BY enquiry_messages.id ASC");
                    if (!empty($conversaton_details)) {
                        foreach ($conversaton_details as $CD) {
                            if ($CD->sender_type == 'patient') {
                                $patient_data = $this->PatientModel->GetPatientProfile($CD->sender_id);
                                if ($patient_data) {
                                    $sender_name = $patient_data[0]->name;
                                    $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                                } else {
                                    $sender_name = "";
                                    $sender_profile_image = "";
                                }
                                $where = array('users.id' => $CD->receiver_id, 'users.role' => 'doctor');
                                $doctor_data = $this->PatientModel->SearchDoctors($where);
                                if ($doctor_data) {
                                    $receiver_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                                    $receiver_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                                } else {
                                    $receiver_name = "";
                                    $receiver_profile_image = "";
                                }
                            }
                            if ($CD->sender_type == 'doctor') {
                                $where = array('users.id' => $CD->sender_id, 'users.role' => 'doctor');
                                $doctor_data = $this->PatientModel->SearchDoctors($where);
                                if ($doctor_data) {
                                    $sender_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                                    $sender_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                                } else {
                                    $sender_name = "";
                                    $sender_profile_image = "";
                                }

                                $patient_data = $this->PatientModel->GetPatientProfile($CD->receiver_id);
                                if ($patient_data) {
                                    $receiver_name = $patient_data[0]->name;
                                    $receiver_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                                } else {
                                    $receiver_name = "";
                                    $receiver_profile_image = "";
                                }
                            }
                            if ($CD->message_type != 'text')
                                $CD->message = ($CD->message) ? base_url() . ENQUIRY_MESSAGE_URL . '/' . $CD->message : "";
                            $all_data[] = array(
                                'enquiry_id' => $treatment_enquiry[0]->id,
                                'sender_id' => $CD->sender_id,
                                'sender_name' => $sender_name,
                                'sender_profile_image' => $sender_profile_image,
                                'receiver_id' => $CD->receiver_id,
                                'receiver_name' => $receiver_name,
                                'receiver_profile_image' => $receiver_profile_image,
                                'sender_type' => $CD->sender_type,
                                'message' => $CD->message,
                                'message_type' => $CD->message_type,
                                'sent_at' => $CD->sent_at,
                                'timestamp' => $CD->sent_at
                            );
                        }
                    }

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('treatment_enquiry_details');
                    $this->response->data = $all_data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_enquiry');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DeleteEnquiry() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $enquiry_id = ($this->input->post('enquiry_id')) ? $this->input->post('enquiry_id') : '';

        if (!empty($user_id) && !empty($enquiry_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treatment_enquiry_record = $this->Custom->get_where('treatment_enquiry', array('id' => $enquiry_id, 'patient_id' => $user_id));
                if (empty($treatment_enquiry_record)) {
                    $this->response->success = 203;
                    $this->response->message = "Enquiry id is not valid.";
                    die(json_encode($this->response));
                }
                $status = $this->Custom->delete_where('treatment_enquiry', array('id' => $enquiry_id));
                if ($status) {
                    $enquiry_conversations_record = $this->Custom->get_where('enquiry_conversations', array('enquiry_id' => $enquiry_id));
                    if ($enquiry_conversations_record) {
//delete fom conversation
                        $conversation_id = $enquiry_conversations_record[0]->id;
                        $this->Custom->delete_where('enquiry_conversations', array('id' => $conversation_id));
//delete fom messages
                        $enquiry_messages_record = $this->Custom->get_where('enquiry_messages', array('conversation_id' => $conversation_id));
                        if ($enquiry_messages_record) {
                            foreach ($enquiry_messages_record as $EMR) {
                                if ($EMR->message_type != 'text') {
                                    unlink(ENQUIRY_MESSAGE_PATH . '/' . $EMR->message);
                                }
                                $this->Custom->delete_where('enquiry_messages', array('id' => $EMR->id));
                            }
                        }
                    }

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('enquiry_deleted');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function ReportIssue() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $issue = ($this->input->post('issue')) ? $this->input->post('issue') : '';
        $issue_details = ($this->input->post('issue_details')) ? $this->input->post('issue_details') : '';

        if (!empty($user_id) && !empty($issue)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'user_id' => $user_id,
                    'issue' => $issue,
                    'issue_details' => $issue_details,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('report_issue', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('issue_reported');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AppFeedback() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feedback = ($this->input->post('feedback')) ? $this->input->post('feedback') : '';

        if (!empty($user_id) && !empty($feedback)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'user_id' => $user_id,
                    'type' => 'patient',
                    'feedback' => $feedback,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('app_feedback', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('feedback_shared');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DeactiveAccount() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $update_status = $this->Custom->update_where('users', array('activate_status' => 0), array('id' => $user_id));
                if ($update_status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function InviteDoctor() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $date = ($this->input->post('date')) ? $this->input->post('date') : '';
        $time = ($this->input->post('time')) ? $this->input->post('time') : '';
        $description = ($this->input->post('description')) ? $this->input->post('description') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $mobile_number = ($this->input->post('mobile_number')) ? $this->input->post('mobile_number') : '';
        $email = ($this->input->post('email')) ? $this->input->post('email') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $address = ($this->input->post('address')) ? $this->input->post('address') : '';

        if (!empty($user_id) && !empty($doctor_id) && !empty($date) && !empty($time) && !empty($address) && !empty($name) && !empty($email) && !empty($mobile_number) && !empty($gender)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                if (empty($doctor_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('doctor_id_error');
                    die(json_encode($this->response));
                }
                if ($doctor_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('doctor_account_deactivate');
                    die(json_encode($this->response));
                }
                $appointment = $this->Custom->query("select * from appointment where doctor_id = $doctor_id AND start_time = '$time' AND (status = 0 OR status = 1 OR status = 4 OR status = 5 OR status = 6)");
                if (!empty($appointment)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('slot_booked');
                    die(json_encode($this->response));
                }
                $consultation_settings = $this->Custom->get_where('consultation_settings', array('user_id' => $doctor_id));
                $amount = $consultation_settings[0]->invite_consult_charge;
                $consult_time = $consultation_settings[0]->invite_consult_time;
                $end_time = strtotime('+' . $consult_time . ' minutes', $time);

                $insertArr = array(
                    'user_id	' => $user_id,
                    'doctor_id' => $doctor_id,
                    'appointment_date' => $date,
                    'start_time' => $time,
                    'end_time' => $end_time,
                    'name' => $name,
                    'email' => $email,
                    'mobile_number' => $mobile_number,
                    'gender' => $gender,
                    'description' => $description,
                    'address' => $address,
                    'appointment_type' => 4,
                    'annoymous_status' => 0,
                    'amount' => $amount,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('appointment', $insertArr);
                if ($insert_id) {
//update chat room name
                    $chat_room_name = 'ZumCare-' . $doctor_id . '-' . $user_id . '-' . time();
                    $this->Custom->update_where('appointment', array('chat_room_name' => $chat_room_name), array('id' => $insert_id));

//send mail
                    /* $mail_data = array('otp' => $otp);
                      $content = $this->load->view('mail/doctor_invitation_mail', $mail_data, TRUE);
                      $subject = 'Invitation - zumcare App';
                      $this->SendMail($email, $subject, $content); */

                    $this->DoctorNotification('appointment', $user_id, $doctor_id, 'patient', $insert_id, 4, 0, '');

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('doctor_invited');
                    $this->response->data = $insert_id;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function UpdateAppointmentStatus() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';

        if (!empty($user_id) && !empty($appointment_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_record = $this->Custom->get_where("appointment", array('user_id' => $user_id, 'id' => $appointment_id));
                if (empty($appointment_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case "cancel":
                        if ($appointment_record[0]->appointment_type != 0 && $appointment_record[0]->status == 4) {
                            $this->response->success = 203;
                            $this->response->message = "Your Appointment is paid. So can't cancel appointment";
                            die(json_encode($this->response));
                        }
                        $update_status = $this->Custom->update_where('appointment', array('status' => 3), array('id' => $appointment_id));
                        if ($update_status) {
                            $this->DoctorNotification('appointment', $user_id, $appointment_record[0]->doctor_id, 'patient', $insert_id, $appointment_record[0]->appointment_type, 3, '');

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('appointment_cancelled');
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case "complete":
                        $update_status = $this->Custom->update_where('appointment', array('status' => 7), array('id' => $appointment_id));
                        if ($update_status) {
                            $this->DoctorNotification('appointment', $user_id, $appointment_record[0]->doctor_id, 'patient', $insert_id, $appointment_record[0]->appointment_type, 7, '');

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('appointment_completed');
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;

                    case "satisfy":
                        $update_status = $this->Custom->update_where('appointment', array('status' => 5, 'chat_status' => 2), array('id' => $appointment_id));
                        if ($update_status) {
                            $this->DoctorNotification('appointment', $user_id, $appointment_record[0]->doctor_id, 'patient', $appointment_id, $appointment_record[0]->appointment_type, 5, '');

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('appointment_satisfy');
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                endswitch;
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DoctorNotification($notification_type, $sender_id, $receiver_id, $sender_type, $action_id, $type, $status, $token) {
        define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
        $created = strtotime(date("Y-m-d H:i:s"));
        $notification_settings = $this->Custom->get_where('notification_settings', array('user_id' => $receiver_id));
        $show_notification = 1;
        $sender_data = $this->Custom->get_where('users', array('id' => $sender_id));
        $receiver_data = $this->Custom->get_where('users', array('id' => $receiver_id));
        $patient_name = $sender_data[0]->name;
        $_POST['lang'] = (isset($_POST['lang']) && !empty($_POST['lang'])) ? $_POST['lang'] : '';
        $message_ru = '';

        if ($notification_type == 'appointment') {
            $appointment_data = $this->Custom->get_where('appointment', array('id' => $action_id));
            if ($status == 0) {
                $message = $this->CreateMessageForNotification('english', 'new_appointment'); //$this->lang->line('new_appointment');
                $message_ru = $this->CreateMessageForNotification('russian', 'new_appointment');
            }

            if ($status == 3) {
                $message = $this->CreateMessageForNotification('english', 'appointment_cancel_by'); // $this->lang->line('appointment_cancel_by') . " " . $sender_data[0]->name . ".";
                $message_ru = $this->CreateMessageForNotification('russian', 'appointment_cancel_by');
            }

            if ($status == 4) {
                if ($type == 2) {
                    $message = $this->CreateMessageForNotification('english', 'receive_video_call') . " " . $sender_data[0]->name . "."; //$this->lang->line('receive_video_call') . " " . $sender_data[0]->name . ".";
                    $message_ru = $this->CreateMessageForNotification('russian', 'receive_video_call') . " " . $sender_data[0]->name . ".";
                }
                if ($type == 1) {
                    $message = $this->CreateMessageForNotification('english', 'receive_audio_call') . " " . $sender_data[0]->name . "."; //$this->lang->line('receive_audio_call') . " " . $sender_data[0]->name . ".";
                    $message_ru = $this->CreateMessageForNotification('russian', 'receive_audio_call') . " " . $sender_data[0]->name . ".";
                }
                if ($type == 3) {
                    $message = "vdvd";
                    $message_ru = "vdvd";
                }
            }

            if ($status == 5) {
                $message = $sender_data[0]->name . " " . $this->CreateMessageForNotification('english', 'satisfy_with_consultation'); //$sender_data[0]->name . " " . $this->lang->line('satisfy_with_consultation');
                $message_ru = $sender_data[0]->name . " " . $this->CreateMessageForNotification('russian', 'satisfy_with_consultation');
            }
            if ($status == 7) {
                $message = $this->CreateMessageForNotification('english', 'appointment_marked_completed') . $sender_data[0]->name . "."; //$this->lang->line('appointment_marked_completed') . " " . $sender_data[0]->name . ".";
                $message_ru = $this->CreateMessageForNotification('russian', 'appointment_marked_completed') . $sender_data[0]->name . ".";
            }
            $room_name = ($appointment_data) ? $appointment_data[0]->chat_room_name : "";

            $send_msg = array("message" => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '' )) ? $message : $message_ru, 'notification_type' => $notification_type, 'type' => $type, 'patient_id' => $sender_id, 'patient_name' => $patient_name, 'room_name' => $room_name, 'id' => $action_id, 'token' => $token, 'status' => $status);


            if ($status == 0 && $notification_settings[0]->create_appointment != 1)
                $show_notification = 0;
            if ($status == 2 && $notification_settings[0]->reject_appointment != 1)
                $show_notification = 0;
            if ($status == 7 && $notification_settings[0]->payment_appointment != 1)
                $show_notification = 0;
        }

        if ($notification_type == 'enquiry') {
            //$message = $this->lang->line('got_enquiry') . " " . $patient_name . ".";
            $message = $this->CreateMessageForNotification('english', 'got_enquiry');
            $message_ru = $this->CreateMessageForNotification('russian', 'got_enquiry');
            $send_msg = array("message" => (isset($_POST['lang']) && !empty($_POST['lang']) && $_POST['lang'] == 'en') ? $message : $message_ru, 'notification_type' => $notification_type, 'id' => $action_id, 'patient_id' => $sender_id, 'patient_name' => $patient_name);

            if ($notification_settings[0]->treatment_enquiry != 1)
                $show_notification = 0;
        }

        if ($notification_type == 'feed') {
            if ($type == 'feed_comment') {
                // $message = $patient_name . " " . $this->lang->line('comment_on_feed');
                $message = $patient_name . " " . $this->CreateMessageForNotification('english', 'comment_on_feed');
                $message_ru = $patient_name . " " . $this->CreateMessageForNotification('russian', 'comment_on_feed');
                if ($notification_settings[0]->feed_comment != 1)
                    $show_notification = 0;
            }
            if ($type == 'feed_like') {
                // $message = $patient_name . " " . $this->lang->line('like_your_feed');
                $message = $patient_name . " " . $this->CreateMessageForNotification('english', 'like_your_feed');
                $message = $patient_name . " " . $this->CreateMessageForNotification('russian', 'like_your_feed');
                if ($notification_settings[0]->feed_like != 1)
                    $show_notification = 0;
            }
            $send_msg = array("message" => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '' )) ? $message : $message_ru, 'notification_type' => $notification_type, 'id' => $action_id, 'patient_id' => $sender_id, 'patient_name' => $patient_name);
        }

        if (isset($receiver_data) && !empty($receiver_data)) {
            if ($receiver_data[0]->device_type == "android") {
                if (!empty($receiver_data[0]->device_token)) {
                    if ($show_notification == 1) {
                        $registatoin_ids = array($receiver_data[0]->device_token);
                        $message_data = $send_msg;

                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $fields = array(
                            'registration_ids' => $registatoin_ids,
                            'data' => $message_data,
                        );
                        $headers = array(
                            'Authorization: key=' . API_KEY,
                            'Content-Type: application/json'
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                        $result = curl_exec($ch);
                        $res_array = json_decode($result);
                    }
                    if (($status != 4 && $notification_type == 'appointment') || $notification_type == 'enquiry' || $notification_type == 'feed') {
                        $insert_data = array(
                            'notification_type' => $notification_type,
                            'sender_id' => $sender_id,
                            'receiver_id' => $receiver_id,
                            'sender_type' => $sender_type,
                            'message' => $message,
                            'message_ru' => (isset($message_ru) && !empty($message_ru)) ? $message_ru : '',
                            'action_id' => $action_id,
                            'status' => $status,
                            'created_at' => $created
                        );

                        $insert_id = $this->Custom->insert_data('notifications', $insert_data);
                        // print_r($send_msg); die("Hello");
                    }
                }
            } else {
                $app_state = $this->app_state;
                // = "";

                $deviceToken = $receiver_data[0]->device_token;

                if ($show_notification == 1) {
                    if ($notification_type == 'appointment') {

                        if ($status == 4 && ($type == 1 || $type == 2 || $type == 3)) {
                            $deviceToken = $receiver_data[0]->voip_device_token;
                        } else {
                            $deviceToken = $receiver_data[0]->device_token;
                        }

                        $body['aps'] = array(
                            'alert' => array(
//'title' => "You have a notification",
                                'content-available' => 1,
                                'body' => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '' )) ? $message : $message_ru,
                            ),
                            'badge' => 1,
                            'notification_type' => $notification_type,
                            'status' => $status,
                            'type' => $type,
                            'id' => $action_id,
                            'patient_name' => $patient_name,
                            'patient_id' => $sender_id,
                            'room_name' => $room_name,
                            'token' => $token,
                            'sound' => 'default',
                        );

                        $passphrase = '123456789';

                        $ctx = stream_context_create();

                        if ($status == 4 && ($type == 1 || $type == 2 || $type == 3)) {
                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorVideo.pem');
                            } else {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/Audio_videoCertificates.pem');
                            }
                        } else {
                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                            } else {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                            }
                        }

                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        } else {
                            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        }

                        $payload = json_encode($body);

                        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                        $result = fwrite($fp, $msg, strlen($msg));

                        fclose($fp);
                    }
                    if ($notification_type == 'enquiry') {
                        $deviceToken = $receiver_data[0]->device_token;
                        $body['aps'] = array(
                            'alert' => array(
//'title' => "You have a notification",
                                'content-available' => 1,
                                'body' => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '' )) ? $message : $message_ru,
                            ),
                            'badge' => 1,
                            'id' => $action_id,
                            'patient_name' => $patient_name,
                            'patient_id' => $sender_id,
                            'sound' => 'default',
                            'notification_type' => $notification_type,
                        );

                        $passphrase = '123456789';

                        $ctx = stream_context_create();


                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                        }


                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        } else {
                            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        }

                        $payload = json_encode($body);

                        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                        $result = fwrite($fp, $msg, strlen($msg));
                        fclose($fp);
                    }

                    if ($notification_type == 'feed') {
                        $deviceToken = $receiver_data[0]->device_token;
                        $body['aps'] = array(
                            'alert' => array(
//'title' => "You have a notification",
                                'content-available' => 1,
                                'body' => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '' )) ? $message : $message_ru,
                            ),
                            'badge' => 1,
                            'id' => $action_id,
                            'patient_name' => $patient_name,
                            'patient_id' => $sender_id,
                            'sound' => 'default',
                            'notification_type' => $notification_type,
                        );

                        $passphrase = '123456789';

                        $ctx = stream_context_create();


                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                        }


                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        } else {
                            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        }

                        $payload = json_encode($body);

                        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                        $result = fwrite($fp, $msg, strlen($msg));

                        fclose($fp);
                    }
                }

                if (($status != 4 && $notification_type == 'appointment') || $notification_type == 'enquiry' || $notification_type == 'feed') {
                    $insert_data = array(
                        'notification_type' => $notification_type,
                        'sender_id' => $sender_id,
                        'receiver_id' => $receiver_id,
                        'sender_type' => $sender_type,
                        'message' => $message,
                        'message_ru' => (isset($message_ru) && !empty($message_ru)) ? $message_ru : '',
                        'action_id' => $action_id,
                        'status' => $status,
                        'created_at' => $created
                    );

                    $insert_id = $this->Custom->insert_data('notifications', $insert_data);
                }
            }
        }
    }

    public function PaymentNotification($notification_type, $sender_id, $receiver_id, $sender_type, $action_id) {
        define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
        $created = strtotime(date("Y-m-d H:i:s"));
        $notification_settings = $this->Custom->get_where('notification_settings', array('user_id' => $receiver_id));
        $show_notification = 1;
        $sender_data = $this->Custom->get_where('users', array('id' => $sender_id));
        $receiver_data = $this->Custom->get_where('users', array('id' => $receiver_id));

        $message = $this->CreateMessageForNotification('english', 'payment_done');
        $message_ru = $this->CreateMessageForNotification('russian', 'payment_done');

        $send_msg = array("message" => ($_POST['lang'] == 'en') ? $message : $message_ru, 'notification_type' => 'payment', 'patient_id' => $sender_id, 'id' => $action_id);
        if ($notification_settings[0]->payment_appointment != 1)
            $show_notification = 0;


        if ($receiver_data) {
            if ($receiver_data[0]->device_type == "android") {
                if (!empty($receiver_data[0]->device_token)) {
                    if ($show_notification == 1) {
                        $registatoin_ids = array($receiver_data[0]->device_token);
                        $message_data = $send_msg;

                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $fields = array(
                            'registration_ids' => $registatoin_ids,
                            'data' => $message_data,
                        );
                        $headers = array(
                            'Authorization: key=' . API_KEY,
                            'Content-Type: application/json'
                        );

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                        $result = curl_exec($ch);
                        $res_array = json_decode($result);
                    }
                    $insert_data = array(
                        'notification_type' => 'appointment',
                        'sender_id' => $sender_id,
                        'receiver_id' => $receiver_id,
                        'sender_type' => 'patient',
                        'message' => $message,
                        'message_ru' => (isset($message_ru) && !empty($message_ru)) ? $message_ru : '',
                        'action_id' => $action_id,
                        'status' => 4,
                        'created_at' => $created
                    );
                    $insert_id = $this->Custom->insert_data('notifications', $insert_data);
                }
            } else {
                $app_state = $this->app_state;
                // = "";
                $deviceToken = $receiver_data[0]->device_token;
                if ($show_notification == 1) {
                    $body['aps'] = array(
                        'alert' => array(
                            'content-available' => 1,
                            'body' => ($language == 'en') ? $message : $message_ru,
                        ),
                        'badge' => 1,
                        'notification_type' => 'appointment',
                        'id' => $action_id,
                        'patient_id' => $sender_id,
                        'sound' => 'default',
                    );
                    $passphrase = '123456789';
                    $ctx = stream_context_create();
                    if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                    } else {
                        stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                    }
                    stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                    if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                        $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    } else {
                        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                    }

                    $payload = json_encode($body);

                    $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

                    $result = fwrite($fp, $msg, strlen($msg));

                    fclose($fp);
                }
                $insert_data = array(
                    'notification_type' => 'appointment',
                    'sender_id' => $sender_id,
                    'receiver_id' => $receiver_id,
                    'sender_type' => 'patient',
                    'message' => $message,
                    'message_ru' => (isset($message_ru) && !empty($message_ru)) ? $message_ru : '',
                    'action_id' => $action_id,
                    'status' => 4,
                    'created_at' => $created
                );
                $insert_id = $this->Custom->insert_data('notifications', $insert_data);
            }
        }
    }

    public function GenerateSignature() {
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $amount = ($this->input->post('amount')) ? $this->input->post('amount') : '';
        $promo_id = ($this->input->post('promo_id')) ? $this->input->post('promo_id') : 0;
        $currency_id = ($this->input->post('currency_id')) ? $this->input->post('currency_id') : '';
        if (!empty($type) && !empty($amount) && !empty($currency_id)) {
            require $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/kkb.utils.php';
            $path1 = $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/config.txt';
            $order_id = substr(str_shuffle("123456789"), 0, 7);
            $content = process_request($order_id, $currency_id, $amount, $path1);

            if ($type == 'appointment') {
                if (empty($appointment_id)) {
                    $this->response->success = 201;
                    $this->response->message = $this->lang->line('required_field');
                    die(json_encode($this->response));
                }

                //update order id
                $this->Custom->update_where('appointment', array('appointment_uid' => $order_id, 'promo_id' => $promo_id, 'promo_uses_date' => strtotime(date("Y-m-d H:i:s")),), array('id' => $appointment_id));
            }
            $ret_data = array('signature' => $content, 'uid' => $order_id, 'type' => $type);
            $this->response->success = 200;
            $this->response->message = $this->lang->line('Signature');
            $this->response->data = $ret_data;
            die(json_encode($this->response));
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GenerateSignatureForAddCard() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'patient'));

            require $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/kkb.utils.php';
            $path1 = $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/config.txt';
            $order_id = substr(str_shuffle("123456789"), 0, 7);
            $currency_id = 398;
            $amount = 1;
            $abonent_id = $user_record[0]->user_uid;
            $content = process_addcard_request($order_id, $currency_id, $amount, $path1, $abonent_id);

            $ret_data = array('signature' => $content, 'uid' => $order_id, 'type' => '');
            $this->response->success = 200;
            $this->response->message = $this->lang->line('Signature');
            $this->response->data = $ret_data;
            die(json_encode($this->response));
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GenerateSignatureForPaymentByCard() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $amount = ($this->input->post('amount')) ? $this->input->post('amount') : '';
        $promo_id = ($this->input->post('promo_id')) ? $this->input->post('promo_id') : 0;
        $discount_amt = ($this->input->post('discount_amt')) ? $this->input->post('discount_amt') : 0;
        $currency_id = ($this->input->post('currency_id')) ? $this->input->post('currency_id') : '';
        if (!empty($user_id) && !empty($type) && !empty($amount) && !empty($currency_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'patient'));

            require $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/kkb.utils.php';
            $path1 = $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/config.txt';
            $order_id = substr(str_shuffle("123456789"), 0, 7);
            $abonent_id = $user_record[0]->user_uid;
            $content = process_addcard_request($order_id, $currency_id, $amount, $path1, $abonent_id);

            if ($type == 'appointment') {
                if (empty($appointment_id)) {
                    $this->response->success = 201;
                    $this->response->message = $this->lang->line('required_field');
                    die(json_encode($this->response));
                }
                //update order id
                $payable_amt = $amount - $discount_amt;
                $updateArr = array(
                    'appointment_uid' => $order_id,
                    'promo_id' => $promo_id,
                    'promo_uses_date' => strtotime(date("Y-m-d H:i:s")),
                    'total_amount' => $amount,
                    'amount' => $payable_amt,
                    'discount_amt' => $discount_amt
                );
                $this->Custom->update_where('appointment', $updateArr, array('id' => $appointment_id));
            }
            if (!empty($promo_id)) {
                $promotion_rec = $this->Custom->query("select * from promotion where id = $promo_id");
                $promotion_applied_user_count = $promotion_rec[0]->promotion_applied_user_count + 1;
                $this->Custom->update_where('promotion', array('promotion_applied_user_count' => $applied_promotion), array('id' => $promotion_rec[0]->id));
                $this->Custom->update_where('applied_promotion', array('status' => 1), array('user_id' => $user_id, 'promo_id' => $promo_id));
            }
            $ret_data = array('signature' => $content, 'uid' => $order_id, 'type' => $type);
            $this->response->success = 200;
            $this->response->message = $this->lang->line('Signature');
            $this->response->data = $ret_data;
            die(json_encode($this->response));
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GenerateSignatureForPaymentByCardID() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $amount = ($this->input->post('amount')) ? $this->input->post('amount') : '';
        $promo_id = ($this->input->post('promo_id')) ? $this->input->post('promo_id') : 0;
        $discount_amt = ($this->input->post('discount_amt')) ? $this->input->post('discount_amt') : 0;
        $currency_id = ($this->input->post('currency_id')) ? $this->input->post('currency_id') : '';
        $card_id = ($this->input->post('card_id')) ? $this->input->post('card_id') : '';
        if (!empty($user_id) && !empty($type) && !empty($amount) && !empty($currency_id) && !empty($card_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'patient'));

            require $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/kkb.utils.php';
            $path1 = $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/config.txt';
            $order_id = substr(str_shuffle("123456789"), 0, 7);
            $abonent_id = $user_record[0]->user_uid;
            $content = process_card_payment_request($order_id, $currency_id, $amount, $path1, $abonent_id, $card_id);

            if ($type == 'appointment') {
                if (empty($appointment_id)) {
                    $this->response->success = 201;
                    $this->response->message = $this->lang->line('required_field');
                    die(json_encode($this->response));
                }
                //update order id
                $payable_amt = $amount - $discount_amt;
                $updateArr = array(
                    'appointment_uid' => $order_id,
                    'promo_id' => $promo_id,
                    'promo_uses_date' => strtotime(date("Y-m-d H:i:s")),
                    'total_amount' => $amount,
                    'amount' => $payable_amt,
                    'discount_amt' => $discount_amt
                );
                $this->Custom->update_where('appointment', $updateArr, array('id' => $appointment_id));
            }
            if (!empty($promo_id)) {
                $promotion_rec = $this->Custom->query("select * from promotion where id = $promo_id");
                $promotion_applied_user_count = $promotion_rec[0]->promotion_applied_user_count + 1;
                $this->Custom->update_where('promotion', array('promotion_applied_user_count' => $applied_promotion), array('id' => $promotion_rec[0]->id));
                $this->Custom->update_where('applied_promotion', array('status' => 1), array('user_id' => $user_id, 'promo_id' => $promo_id));
            }
            $ret_data = array('signature' => $content, 'uid' => $order_id, 'type' => $type);
            $this->response->success = 200;
            $this->response->message = $this->lang->line('Signature');
            $this->response->data = $ret_data;
            die(json_encode($this->response));
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GenerateSignatureToDeleteCard() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $card_id = ($this->input->post('card_id')) ? $this->input->post('card_id') : '';
        if (!empty($user_id) && !empty($card_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'patient'));
            if (empty($user_record)) {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
            require $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/kkb.utils.php';
            $path1 = $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/application/libraries/Live/config.txt';
            $abonent_id = $user_record[0]->user_uid;
            $action = 'delete';
            $content = process_delete_get_card_request($path1, $abonent_id, $card_id, $action);
            //$content = strip_slashes($content);
            //$content = str_replace("/", "", $content);
            $signature_data = (object) array(
                        'signature' => urlencode($content),
                        'order_uid' => ''
            );
            $this->response->success = 200;
            $this->response->message = $this->lang->line('Signature');
            $this->response->data = $signature_data;
            die(json_encode($this->response));
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetCards() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case 'all':
                        $cards = $this->Custom->query("select * from cards where user_id = $user_id AND deleted_status = 1 ORDER BY id DESC");
                        if (empty($cards)) {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }

                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('card_details');
                        $this->response->data = $cards;
                        die(json_encode($this->response));

                        break;

                    case 'default':
                        $cards = $this->Custom->query("select * from cards where user_id = $user_id AND deleted_status = 1 AND card_status = 1 ORDER BY id DESC");
                        if (empty($cards)) {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }

                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('card_details');
                        $this->response->data = $cards[0];
                        die(json_encode($this->response));

                        break;
                endswitch;
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DeleteCard() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $card_id = ($this->input->post('id')) ? $this->input->post('id') : '';
        if (!empty($user_id) && !empty($card_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $cards = $this->Custom->query("select * from cards where user_id = $user_id AND id = $card_id");
                if (empty($cards)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_card_id');
                    die(json_encode($this->response));
                }
                $del_status = $this->Custom->update_where('cards', array('deleted_status' => 2), array('id' => $card_id));
                if ($del_status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('card_deleted');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function MakeCardDefault() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $card_id = ($this->input->post('id')) ? $this->input->post('id') : '';
        if (!empty($user_id) && !empty($card_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $cards = $this->Custom->query("select * from cards where user_id = $user_id AND id = $card_id");
                if (empty($cards)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_card_id');
                    die(json_encode($this->response));
                }
                $update_status = $this->Custom->update_where('cards', array('card_status' => 1), array('id' => $card_id));
                if ($update_status) {
                    $this->Custom->update_where('cards', array('card_status' => 0), array('user_id' => $user_id, 'id !=' => $card_id));
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('default_card_set');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function UpdateCardIdPaymentDetailsForAppointment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_uid = ($this->input->post('appointment_uid')) ? $this->input->post('appointment_uid') : '';
        $merchant_id = ($this->input->post('merchant_id')) ? $this->input->post('merchant_id') : '';
        $amount = ($this->input->post('amount')) ? $this->input->post('amount') : '';
        $reference = ($this->input->post('reference')) ? $this->input->post('reference') : '';
        $bank_sign = ($this->input->post('bank_sign')) ? $this->input->post('bank_sign') : '';
        $CardId = ($this->input->post('CardId')) ? $this->input->post('CardId') : '';
        $today = date('Y-m-d');
        $time = strtotime(date("h:i A"));
        if (!empty($user_id) && !empty($appointment_uid) && !empty($merchant_id) && !empty($amount) && !empty($reference) && !empty($bank_sign) && !empty($CardId)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $cards = $this->Custom->get_where("cards", array('user_id' => $user_id, 'CardId' => $CardId));
                if (empty($cards)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_card_id');
                    die(json_encode($this->response));
                }
                $appointment = $this->Custom->query("select * from appointment where appointment_uid = '$appointment_uid'");
                $appointment_id = $appointment[0]->id;
                $doctor_id = $appointment[0]->doctor_id;
                $appointment_type = $appointment[0]->appointment_type;
                $card_id = ($cards) ? $cards[0]->id : "";

                $insertArr = array(
                    'type' => 'appointment',
                    'user_id' => $user_id,
                    'doctor_id' => $doctor_id,
                    'appointment_type' => $appointment_type,
                    'appointment_id' => $appointment_id,
                    'card_id' => $card_id,
                    'uid' => $appointment_uid,
                    'merchant_id' => $merchant_id,
                    'amount' => $amount,
                    'reference' => $reference,
                    'bank_sign' => $bank_sign,
                    'date' => $today
                );
                $insert_id = $this->Custom->insert_data('transaction', $insertArr);
                if ($insert_id) {
                    if ($appointment_type == 0)
                        $this->Custom->update_where('appointment', array('status' => 4), array('id' => $appointment_id));
                    else
                        $this->Custom->update_where('appointment', array('status' => 4, 'call_chat_timing' => 0), array('id' => $appointment_id));

                    //send notification
                    $this->PaymentNotification('appointment', $user_id, $doctor_id, 'patient', $appointment_id);

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('payment_completed');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function UpdateCardIdPaymentDetailsForEnquiry() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $enquiry_uid = ($this->input->post('enquiry_uid')) ? $this->input->post('enquiry_uid') : '';
        $merchant_id = ($this->input->post('merchant_id')) ? $this->input->post('merchant_id') : '';
        $amount = ($this->input->post('amount')) ? $this->input->post('amount') : '';
        $reference = ($this->input->post('reference')) ? $this->input->post('reference') : '';
        $bank_sign = ($this->input->post('bank_sign')) ? $this->input->post('bank_sign') : '';
        $CardId = ($this->input->post('CardId')) ? $this->input->post('CardId') : '';
        $today = date('Y-m-d');
        $time = strtotime(date("h:i A"));
        if (!empty($user_id) && !empty($enquiry_uid) && !empty($merchant_id) && !empty($amount) && !empty($reference) && !empty($bank_sign) && !empty($CardId)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $cards = $this->Custom->query("select * from cards where user_id = $user_id AND CardId = $CardId");
                if (empty($cards)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_card_id');
                    die(json_encode($this->response));
                }
                $card_id = $cards[0]->id;

                $insertArr = array(
                    'type' => 'enquiry',
                    'appointment_id' => '',
                    'card_id' => $card_id,
                    'uid' => $enquiry_uid,
                    'merchant_id' => $merchant_id,
                    'amount' => $amount,
                    'reference' => $reference,
                    'bank_sign' => $bank_sign,
                    'date' => $today
                );
                $insert_id = $this->Custom->insert_data('transaction', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('payment_completed');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GenerateChatToken() {
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $new_token = "";
        if (!empty($appointment_id)) {
            $appointment_data = $this->Custom->get_where('appointment', array('id' => $appointment_id));
            $user_data = $this->Custom->get_where('users', array('id' => $appointment_data[0]->user_id, 'role' => 'patient'));
            $user_data[0]->name = str_replace(" ", "-", $user_data[0]->name);
            $patient_identity = $user_data[0]->name . "-" . $appointment_data[0]->user_id . "-" . $appointment_id;
            $token = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $patient_identity);

            $chatGrant = new Twilio\Jwt\Grants\ChatGrant();
            $chatGrant->setServiceSid($this->serviceSid);

            $token->addGrant($chatGrant);
            $new_token = $token->toJWT();

            $doctor_data = $this->Custom->get_where('users', array('id' => $appointment_data[0]->doctor_id, 'role' => 'doctor'));
            $doctor_data[0]->name = str_replace(" ", "-", $doctor_data[0]->name);
            $doctor_identity = $doctor_data[0]->name . "-" . $appointment_data[0]->doctor_id . "-" . $appointment_id;

            $data = array('token' => $new_token, 'doctor_identity' => $doctor_identity);
            if (!empty($new_token)) {
                $this->response->success = 200;
                $this->response->message = $this->lang->line('token_generated');
                $this->response->data = $data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 202;
                $this->response->message = $this->lang->line('went_wrong');
                $this->response->token = $new_token;
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GenerateVoiceToken() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $new_token = "";

        if (!empty($user_id) && !empty($name)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $identity = $name;
                $token = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $identity);
                $voiceGrant = new Twilio\Jwt\Grants\VoiceGrant();
                $voiceGrant->setOutgoingApplicationSid($this->outgoingApplicationSid);
                if ($user_record[0]->device_type == 'ios')
                    $voiceGrant->setPushCredentialSid($this->PatientiOSPushCredentialSid);
                if ($user_record[0]->device_type == 'android')
                    $voiceGrant->setPushCredentialSid($this->PatientAndriodPushCredentialSid);

                $token->addGrant($voiceGrant);
                $new_token = $token->toJWT();
                $timestamp = strtotime(date('y-m-d H:i:s'));

                $data = array('token' => $new_token, 'doctor_identity' => '', 'timestamp' => $timestamp);
                if (!empty($new_token)) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('token_generated');
                    $this->response->data = $data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GenerateVideoToken() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $new_token = "";
        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }

                $appointment_data = $this->Custom->get_where('appointment', array('id' => $appointment_id));
                if (empty($appointment_data)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                if ($appointment_data[0]->status < 4) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->error('payment_error');
                    die(json_encode($this->response));
                }
                if ($appointment_data[0]->user_id != $user_id) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('userid_error');
                    die(json_encode($this->response));
                }
                $user_record[0]->name = str_replace(" ", "-", $user_record[0]->name);
                $identity = $user_record[0]->name . "-" . $user_id . "-" . $appointment_id;
                $roomName = $appointment_data[0]->chat_room_name;
                $token = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $identity);

                $videoGrant = new Twilio\Jwt\Grants\VideoGrant();
                $videoGrant->setRoom($roomName);

                $token->addGrant($videoGrant);
                $new_token = $token->toJWT();
//doctor token
                $doctor_data = $this->Custom->get_where('users', array('id' => $appointment_data[0]->doctor_id, 'role' => 'doctor'));
                $doctor_data[0]->name = str_replace(" ", "-", $doctor_data[0]->name);
                $doctor_identity = $doctor_data[0]->name . "-" . $appointment_data[0]->doctor_id . "-" . $appointment_id;
                $roomName1 = $appointment_data[0]->chat_room_name;
                $token1 = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $doctor_identity);

                $videoGrant1 = new Twilio\Jwt\Grants\VideoGrant();
                $videoGrant1->setRoom($roomName1);

                $token1->addGrant($videoGrant1);
                $doctor_token = $token1->toJWT();

                $data = array('token' => $new_token, 'roomName' => $roomName);
                if (!empty($new_token)) {
                    if ($type == 'call') {
                        $this->DoctorNotification('appointment', $user_id, $appointment_data[0]->doctor_id, 'patient', $appointment_id, $appointment_data[0]->appointment_type, 4, $doctor_token);
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('token_generated');
                    $this->response->data = $data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    $this->response->data = $data;
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AddFeedComment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        $comment = ($this->input->post('comment')) ? $this->input->post('comment') : '';
        if (!empty($user_id) && !empty($feed_id) && !empty($comment)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_feed = $this->Custom->get_where('doctor_feed', array('id' => $feed_id));
                if (empty($doctor_feed)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_feed');
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'user_id' => $user_id,
                    'user_type' => 'patient',
                    'feed_id' => $feed_id,
                    'comment' => $comment,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('comment_doctor_feed', $insertArr);
                if ($insert_id) {
//send notification
                    $this->DoctorNotification('feed', $user_id, $doctor_feed[0]->user_id, 'patient', $feed_id, 'feed_comment', '', '');
//get comment
                    $comment_data = $this->Custom->get_where('comment_doctor_feed', array('id' => $insert_id));
                    if ($comment_data[0]->user_type == 'patient') {
                        $patient_data = $this->PatientModel->GetPatientProfile($comment_data[0]->user_id);
                        if ($patient_data) {
                            $name = $patient_data[0]->name;
                            $profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                        } else {
                            $name = "";
                            $profile_image = "";
                        }
                    }
                    if ($comment_data[0]->user_type == 'doctor') {
                        $this->load->model('DoctorModel');
                        $doctor_data = $this->DoctorModel->GetDoctorProfile($val->user_id);
                        if ($doctor_data) {
                            $name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                            $profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                        } else {
                            $name = "";
                            $profile_image = "";
                        }
                    }
                    $comment_data[0]->name = $name;
                    $comment_data[0]->profile_image = $profile_image;
                    $comment_data[0]->timestamp = $comment_data[0]->created_at;

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('comment_added');
                    $this->response->data = $comment_data[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function FeedCommentList() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        if (!empty($user_id) && !empty($feed_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_feed = $this->Custom->get_where('doctor_feed', array('id' => $feed_id));
                if (empty($doctor_feed)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_feed');
                    die(json_encode($this->response));
                }
//get comment
                $comment_data = $this->Custom->query("select * from comment_doctor_feed where feed_id = $feed_id");
                if (empty($comment_data)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }

                foreach ($comment_data as $val) {
                    if ($val->user_type == 'patient') {
                        $patient_data = $this->PatientModel->GetPatientProfile($val->user_id);
                        if ($patient_data) {
                            $name = $patient_data[0]->name;
                            $profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                        } else {
                            $name = "";
                            $profile_image = "";
                        }
                    }
                    if ($val->user_type == 'doctor') {
                        $this->load->model('DoctorModel');
                        $doctor_data = $this->DoctorModel->GetDoctorProfile($val->user_id);
                        if ($doctor_data) {
                            $name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                            $profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                        } else {
                            $name = "";
                            $profile_image = "";
                        }
                    }
                    $val->name = $name;
                    $val->profile_image = $profile_image;
                    $val->timestamp = $val->created_at;
                }
                $this->response->success = 200;
                $this->response->message = $this->lang->line('comment_list');
                $this->response->data = $comment_data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetCreditHistory() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $total_expense = 0.00;
                $online_expense = 0.00;
                $offline_expense = 0.00;
                $invite_expense = 0.00;
                $enquiry_expense = 0.00;
//get credit history
                $transaction_data = $this->Custom->query("select * from transaction where user_id = $user_id");
                if (empty($transaction_data)) {
                    $transaction_history = (object) array(
                                'total_expense' => $total_expense,
                                'online_expense' => $online_expense,
                                'offline_expense' => $offline_expense,
                                'invite_expense' => $invite_expense,
                                'enquiry_expense' => $enquiry_expense
                    );
                } else {
                    foreach ($transaction_data as $trans) {
                        $total_expense = $total_expense + $trans->amount;
//total online expense
                        if ($trans->type == 'appointment' && ($trans->appointment_type == 1 || $trans->appointment_type == 2 || $trans->appointment_type == 3))
                            $online_expense = $online_expense + $trans->amount;
//total offline expense
                        if ($trans->type == 'appointment' && $trans->appointment_type == 0)
                            $offline_expense = $offline_expense + $trans->amount;
//total invite expense
                        if ($trans->type == 'appointment' && $trans->appointment_type == 4)
                            $invite_expense = $invite_expense + $trans->amount;
//total enquiry expense
                        if ($trans->type == 'enquiry')
                            $enquiry_expense = $enquiry_expense + $trans->amount;
                    }
                    $transaction_history = (object) array(
                                'total_expense' => $total_expense,
                                'online_expense' => $online_expense,
                                'offline_expense' => $offline_expense,
                                'invite_expense' => $invite_expense,
                                'enquiry_expense' => $enquiry_expense
                    );
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('credit_history');
                $this->response->data = $transaction_history;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetCreditHistoryDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
//get credit history
                if ($type == 'online')
                    $transaction_data = $this->Custom->query("select * from transaction where user_id = $user_id AND type = 'appointment' AND (appointment_type = 1 OR appointment_type = 2 OR appointment_type = 3) ORDER BY id ASC LIMIT $offset, " . $per_page);
                if ($type == 'offline')
                    $transaction_data = $this->Custom->query("select * from transaction where user_id = $user_id AND type = 'appointment' AND appointment_type = 0 ORDER BY id ASC LIMIT $offset, " . $per_page);
                if ($type == 'invite')
                    $transaction_data = $this->Custom->query("select * from transaction where user_id = $user_id AND type = 'appointment' AND appointment_type = 4 ORDER BY id ASC LIMIT $offset, " . $per_page);
                if ($type == 'enquiry')
                    $transaction_data = $this->Custom->query("select * from transaction where user_id = $user_id AND type = 'enquiry' ORDER BY id ASC LIMIT $offset, " . $per_page);
                if (empty($transaction_data)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('credit_history_details');
                $this->response->data = $transaction_data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function UpdateAppointmentCallTimings() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $call_chat_timing = ($this->input->post('call_chat_timing')) ? $this->input->post('call_chat_timing') : '';

        if (!empty($user_id) && !empty($appointment_id) && !empty($call_chat_timing)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_record = $this->Custom->get_where("appointment", array('user_id' => $user_id, 'id' => $appointment_id));
                if (empty($appointment_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                $consultation_settings = $this->Custom->get_where("consultation_settings", array('user_id' => $appointment_record[0]->doctor_id));
                $online_consult_time = $consultation_settings[0]->online_consult_time;
                $online_consult_time = "00:" . $online_consult_time . ":00";
                if ($call_chat_timing >= strtotime($online_consult_time)) {
                    $update_status = $this->Custom->update_where('appointment', array('status' => 5, 'call_chat_timing' => $call_chat_timing), array('id' => $appointment_id));
                } else {
                    $update_status = $this->Custom->update_where('appointment', array('call_chat_timing' => $call_chat_timing), array('id' => $appointment_id));
                }
                if ($update_status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('appointment_timing_updated');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetRecommedation() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if ($type == 'appointment') {
                    if (empty($appointment_id)) {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                    $patient_recommendation = $this->Custom->query("select * from patient_recommendation where appointment_id = $appointment_id ORDER BY id DESC LIMIT $offset, " . $per_page);
                }
                if ($type == 'user') {
                    $patient_recommendation = $this->Custom->query("select * from patient_recommendation where user_id = $user_id ORDER BY id DESC LIMIT $offset, " . $per_page);
                }
                if (empty($patient_recommendation)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }

                foreach ($patient_recommendation as $row) {
                    //get doctor details
                    $where = array('users.id' => $row->doctor_id, 'users.role' => 'doctor');
                    $doctor_data = $this->PatientModel->SearchDoctors($where);
                    $row->doctor_name = ($doctor_data) ? $doctor_data[0]->name : "";
                    $row->doctor_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                    //get appointment details
                    $appointment_rec = $this->Custom->get_where('appointment', array('id' => $row->appointment_id));
                    if (!empty($appointment_rec)) {
                        $row->appointment_type = $appointment_rec[0]->appointment_type;
                        $row->appointment_date = $appointment_rec[0]->appointment_date;
                        $row->start_time = $appointment_rec[0]->start_time;
                        $row->status = $appointment_rec[0]->status;
                    } else {
                        $row->appointment_type = '';
                        $row->appointment_date = '';
                        $row->start_time = '';
                        $row->status = '';
                    }
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('recommedations');
                $this->response->data = $patient_recommendation;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function NotifyDoctor() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';

        if (!empty($user_id) && !empty($type) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_record = $this->Custom->get_where('appointment', array('id' => $appointment_id));
                if (empty($appointment_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }

                $this->DoctorNotification('appointment', $user_id, $appointment_record[0]->doctor_id, 'patient', $appointment_id, $appointment_record[0]->appointment_type, $appointment_record[0]->status, '');
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetNotifications() {
        $_POST['lang'] = (isset($_POST['lang']) && !empty($_POST['lang'])) ? $_POST['lang'] : '';
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $per_page = 20;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $today_date = date('Y-m-d');
                $date = date('Y-m-d', strtotime('-2 day', strtotime($today_date)));
                $notifications = $this->Custom->query("select * from notifications where receiver_id = '$user_id' AND date(created_at) <= '$date'");
                if (!empty($notifications)) {
                    foreach ($notifications as $row) {
                        $this->Custom->delete_where('notifications', array('notification_id' => $row->notification_id));
                    }
                }
                $notification_rec = $this->Custom->query("select * from notifications where receiver_id = '$user_id' ORDER BY notification_id DESC LIMIT $offset, " . $per_page);
                if ($notification_rec) {
                    foreach ($notification_rec as $val) {
                        $users = $this->Custom->get_where('users', array('id' => $val->sender_id));
                        $doctor_record = $this->Custom->get_where('doctor_profile', array('user_id' => $val->sender_id));
                        $data[] = array(
                            'notification_id' => $val->notification_id,
                            'notification_type' => $val->notification_type,
                            'message' => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '')) ? $val->message : $val->message_ru,
                            'action_id' => $val->action_id,
                            'sender_id' => $val->sender_id,
                            'sender_name' => ($users) ? $users[0]->name : "",
                            'sender_profile_image' => ($doctor_record[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_record[0]->profile_image : "",
                            'timestamp' => $val->created_at,
                        );
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('notification_list');
                    $this->response->data = $data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function DeleteNotifications() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $notification_id = ($this->input->post('notification_id')) ? $this->input->post('notification_id') : '';

        if (!empty($user_id) && !empty($notification_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $notification_rec = $this->Custom->query("select * from notifications where receiver_id = '$user_id' AND notification_id = '$notification_id'");
                if (empty($notification_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_notification_id');
                    die(json_encode($this->response));
                }
                $del_status = $this->Custom->delete_where('notifications', array('notification_id' => $notification_id));
                if ($del_status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('notification_deleted');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function SendChatMessage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $message_type = ($this->input->post('message_type')) ? $this->input->post('message_type') : '';

        if (!empty($user_id) && !empty($appointment_id) && !empty($message_type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->query("select * from appointment where id = '$appointment_id'");
                if (empty($appointment_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                if ($message_type == 'image') {
                    if (!empty($_FILES['message']['name'])) {
                        $name = $_FILES['message']['name'];
                        $ext = end((explode(".", $name)));
                        $new_name = time() . '.' . $ext;
                        $config['file_name'] = $new_name;
                        $config['upload_path'] = CHAT_PATH . '/';
                        $config['allowed_types'] = '*';
                        $config['overwrite'] = TRUE;
                        $config['remove_spaces'] = TRUE;

                        if (!is_dir($config['upload_path']))
                            die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('message')) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('image_not_upload');
                            die(json_encode($this->response));
                        } else {
                            $message = $new_name;
                        }
                    } else {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                } else {
                    $message = ($this->input->post('message')) ? $this->input->post('message') : '';
                    if ($message == '') {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                }
                $insert_arr = array(
                    'appointment_id' => $appointment_id,
                    'sender_id' => $user_id,
                    'receiver_id' => $appointment_rec[0]->doctor_id,
                    'sender_type' => 'patient',
                    'message' => $message,
                    'message_type' => $message_type,
                    'message_status' => 0,
                    'sent_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('chat_message', $insert_arr);
                if ($insert_id) {
//get message
                    $chat_message_rec = $this->Custom->query("select * from chat_message where id = '$insert_id'");
                    if ($chat_message_rec[0]->message_type == 'image') {
                        $chat_message_rec[0]->message = base_url() . CHAT_URL . '/' . $chat_message_rec[0]->message;
                    }
                    if ($chat_message_rec[0]->sender_type == 'patient') {
                        $patient_data = $this->PatientModel->GetPatientProfile($chat_message_rec[0]->sender_id);
                        if ($patient_data) {
                            $sender_name = $patient_data[0]->name;
                            $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                        } else {
                            $sender_name = "";
                            $sender_profile_image = "";
                        }
                        $where = array('users.id' => $chat_message_rec[0]->receiver_id, 'users.role' => 'doctor');
                        $doctor_data = $this->PatientModel->SearchDoctors($where);
                        if ($doctor_data) {
                            $receiver_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                            $receiver_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                        } else {
                            $receiver_name = "";
                            $receiver_profile_image = "";
                        }
                    }
                    if ($chat_message_rec[0]->sender_type == 'doctor') {
                        $where = array('users.id' => $chat_message_rec[0]->sender_id, 'users.role' => 'doctor');
                        $doctor_data = $this->PatientModel->SearchDoctors($where);
                        if ($doctor_data) {
                            $sender_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                            $sender_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                        } else {
                            $sender_name = "";
                            $sender_profile_image = "";
                        }
                        $patient_data = $this->PatientModel->GetPatientProfile($row->receiver_id);
                        if ($patient_data) {
                            $receiver_name = $patient_data[0]->name;
                            $receiver_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                        } else {
                            $receiver_name = "";
                            $receiver_profile_image = "";
                        }
                    }
                    $chat_message_rec[0]->sender_name = $sender_name;
                    $chat_message_rec[0]->sender_profile_image = $sender_profile_image;
                    $chat_message_rec[0]->receiver_name = $receiver_name;
                    $chat_message_rec[0]->receiver_profile_image = $receiver_profile_image;
                    $chat_message_rec[0]->timestamp = $chat_message_rec[0]->sent_at;
//send notification
                    define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
                    $receiver_data = $this->Custom->get_where('users', array('id' => $appointment_rec[0]->doctor_id, 'role' => 'doctor'));
                    $message = $this->lang->line('appointment_new_message1');
                    if (isset($receiver_data) && !empty($receiver_data)) {
                        if ($receiver_data[0]->device_type == "android") {
                            if (!empty($receiver_data[0]->device_token)) {
                                $registatoin_ids = array($receiver_data[0]->device_token);
                                $message_data = array("message" => $message, 'notification_type' => 'chat', 'id' => $appointment_id, 'patient_id' => $user_id, 'patient_name' => $sender_name, 'patient_annoymous_status' => $appointment_rec[0]->annoymous_status);

                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $fields = array(
                                    'registration_ids' => $registatoin_ids,
                                    'data' => $message_data,
                                );
                                $headers = array(
                                    'Authorization: key=' . API_KEY,
                                    'Content-Type: application/json'
                                );

                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, $url);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                                $result = curl_exec($ch);
                                $res_array = json_decode($result);
                            }
                        } else {
                            $app_state = $this->app_state;
                            //$app_state = "";
                            $deviceToken = $receiver_data[0]->device_token;
                            $body['aps'] = array(
                                'alert' => array(
//'title' => "You have a notification",
                                    'content-available' => 1,
                                    'body' => $message,
                                ),
                                'badge' => 1,
                                'notification_type' => 'chat',
                                'id' => $appointment_id,
                                'patient_id' => $user_id,
                                'patient_name' => $sender_name,
                                'patient_annoymous_status' => $appointment_rec[0]->annoymous_status,
                                'sound' => 'default',
                            );

                            $passphrase = '123456789';
                            $ctx = stream_context_create();
                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                            } else {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                            }
                            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                            } else {
                                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                            }
                            $payload = json_encode($body);
                            $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                            $result = fwrite($fp, $msg, strlen($msg));
                            fclose($fp);
                        }
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('message_send');
                    $this->response->data = $chat_message_rec[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetChatMessageDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';

        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->query("select * from appointment where user_id = '$user_id' AND id = '$appointment_id'");
                if (empty($appointment_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
//update read status
                $this->Custom->update_where("chat_message", array('message_status' => 1), array('appointment_id' => $appointment_id, 'receiver_id' => $user_id));
//get messages
                $chat_message_rec = $this->Custom->query("select * from chat_message where appointment_id = '$appointment_id'");
                if (!empty($chat_message_rec)) {
                    foreach ($chat_message_rec as $row) {
                        if ($row->message_type == 'image') {
                            $row->message = base_url() . CHAT_URL . '/' . $row->message;
                        }
                        if ($row->sender_type == 'patient') {
                            $patient_data = $this->PatientModel->GetPatientProfile($row->sender_id);
                            if ($patient_data) {
                                $sender_name = $patient_data[0]->name;
                                $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                            } else {
                                $sender_name = "";
                                $sender_profile_image = "";
                            }
                            $where = array('users.id' => $row->receiver_id, 'users.role' => 'doctor');
                            $doctor_data = $this->PatientModel->SearchDoctors($where);
                            if ($doctor_data) {
                                $receiver_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                                $receiver_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                            } else {
                                $receiver_name = "";
                                $receiver_profile_image = "";
                            }
                        }
                        if ($row->sender_type == 'doctor') {
                            $where = array('users.id' => $row->sender_id, 'users.role' => 'doctor');
                            $doctor_data = $this->PatientModel->SearchDoctors($where);
                            if ($doctor_data) {
                                $sender_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                                $sender_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                            } else {
                                $sender_name = "";
                                $sender_profile_image = "";
                            }
                            $patient_data = $this->PatientModel->GetPatientProfile($row->receiver_id);
                            if ($patient_data) {
                                $receiver_name = $patient_data[0]->name;
                                $receiver_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                            } else {
                                $receiver_name = "";
                                $receiver_profile_image = "";
                            }
                        }
                        $row->sender_name = $sender_name;
                        $row->sender_profile_image = $sender_profile_image;
                        $row->receiver_name = $receiver_name;
                        $row->receiver_profile_image = $receiver_profile_image;
                        $row->timestamp = $row->sent_at;
                    }

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('message_details');
                    $this->response->data = $chat_message_rec;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_message');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetChatMessageByID() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $last_message_id = ($this->input->post('last_message_id')) ? $this->input->post('last_message_id') : '';

        if (!empty($user_id) && !empty($appointment_id) && !empty($last_message_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->query("select * from appointment where user_id = '$user_id' AND id = '$appointment_id'");
                if (empty($appointment_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
//update read status
                $this->Custom->update_where("chat_message", array('message_status' => 1), array('appointment_id' => $appointment_id, 'receiver_id' => $user_id));
//get messages
                $chat_message_rec = $this->Custom->query("select * from chat_message where appointment_id = '$appointment_id' AND id > '$last_message_id'");
                if (!empty($chat_message_rec)) {
                    foreach ($chat_message_rec as $row) {
                        if ($row->message_type == 'image') {
                            $row->message = base_url() . CHAT_URL . '/' . $row->message;
                        }
                        if ($row->sender_type == 'patient') {
                            $patient_data = $this->PatientModel->GetPatientProfile($row->sender_id);
                            if ($patient_data) {
                                $sender_name = $patient_data[0]->name;
                                $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                            } else {
                                $sender_name = "";
                                $sender_profile_image = "";
                            }
                            $where = array('users.id' => $row->receiver_id, 'users.role' => 'doctor');
                            $doctor_data = $this->PatientModel->SearchDoctors($where);
                            if ($doctor_data) {
                                $receiver_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                                $receiver_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                            } else {
                                $receiver_name = "";
                                $receiver_profile_image = "";
                            }
                        }
                        if ($row->sender_type == 'doctor') {
                            $where = array('users.id' => $row->sender_id, 'users.role' => 'doctor');
                            $doctor_data = $this->PatientModel->SearchDoctors($where);
                            if ($doctor_data) {
                                $sender_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                                $sender_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                            } else {
                                $sender_name = "";
                                $sender_profile_image = "";
                            }
                            $patient_data = $this->PatientModel->GetPatientProfile($row->receiver_id);
                            if ($patient_data) {
                                $receiver_name = $patient_data[0]->name;
                                $receiver_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                            } else {
                                $receiver_name = "";
                                $receiver_profile_image = "";
                            }
                        }
                        $row->sender_name = $sender_name;
                        $row->sender_profile_image = $sender_profile_image;
                        $row->receiver_name = $receiver_name;
                        $row->receiver_profile_image = $receiver_profile_image;
                        $row->timestamp = $row->sent_at;
                    }

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('message_details');
                    $this->response->data = $chat_message_rec;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_message');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function NotifyDoctorForCall() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';

        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->query("select * from appointment where id = '$appointment_id'");
                if (empty($appointment_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
//send notification
                define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
                $receiver_data = $this->Custom->get_where('users', array('id' => $appointment_rec[0]->doctor_id, 'role' => 'doctor'));
                $sender_name = $user_record[0]->name;
                $message = $this->lang->line('audio_call_message') . " " . $sender_name . ".";
                if (isset($receiver_data) && !empty($receiver_data)) {
                    if ($receiver_data[0]->device_type == "android") {
                        if (!empty($receiver_data[0]->device_token)) {
                            $registatoin_ids = array($receiver_data[0]->device_token);
                            $message_data = array("message" => $message, 'notification_type' => 'appointment', 'type' => $appointment_rec[0]->appointment_type, 'id' => $appointment_id, 'patient_id' => $user_id, 'patient_name' => $sender_name);

                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $fields = array(
                                'registration_ids' => $registatoin_ids,
                                'data' => $message_data,
                            );
                            $headers = array(
                                'Authorization: key=' . API_KEY,
                                'Content-Type: application/json'
                            );

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                            $result = curl_exec($ch);
                            $res_array = json_decode($result);
                        }
                    } else {
                        //$app_state = "";
                        $app_state = $this->app_state;
                        $deviceToken = $receiver_data[0]->device_token;
                        $body['aps'] = array(
                            'alert' => array(
//'title' => "You have a notification",
                                'content-available' => 1,
                                'body' => $message,
                            ),
                            'badge' => 1,
                            'notification_type' => 'appointment',
                            'type' => $appointment_rec[0]->appointment_type,
                            'id' => $appointment_id,
                            'patient_id' => $user_id,
                            'patient_name' => $sender_name,
                            'sound' => 'default',
                        );

                        $passphrase = '123456789';
                        $ctx = stream_context_create();
                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                        }
                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        } else {
                            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        }
                        $payload = json_encode($body);
                        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                        $result = fwrite($fp, $msg, strlen($msg));
                        fclose($fp);
                    }
                }
                $this->response->success = 200;
                $this->response->message = $this->lang->line('doctor_notified');
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function ApplyPromotion() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $promo_code = ($this->input->post('promo_code')) ? $this->input->post('promo_code') : '';

        if (!empty($user_id) && !empty($promo_code)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $promotion_rec = $this->Custom->query("select * from promotion where promo_code = '$promo_code'");
                if (empty($promotion_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_promo_code');
                    die(json_encode($this->response));
                }
                $applied_promotion = $this->Custom->get_where('applied_promotion', array('promo_id' => $promotion_rec[0]->id));
                if (!empty($applied_promotion)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('promo_code_already_applied');
                    die(json_encode($this->response));
                }
                if ($promotion_rec[0]->expiry_date >= date('Y-m-d')) {
                    $insertArr = array(
                        'user_id' => $user_id,
                        'promo_id' => $promotion_rec[0]->id
                    );
                    $this->Custom->insert_data('applied_promotion', $insertArr);

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('promo_code_applied');
                    $this->response->data = $promotion_rec[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('promo_code_expired');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function FeedReport() {
        $saveArray = $this->input->post();
        if (isset($saveArray['feed_id']) && !empty($saveArray['user_id']) && isset($saveArray['feed_id']) && !empty($saveArray['feed_id'])) {
            $this->response->success = 200;
            $this->response->message = $this->lang->line('report_success_user');
            die(json_encode($this->response));
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function CreateMessageForNotification($lang, $key) {
        $this->lang->load('message', $lang);
        $value = $this->lang->line($key);

        if ($_POST['lang'] == 'en')
            $this->lang->load('message', 'english');
        else if ($_POST['lang'] == 'ru')
            $this->lang->load('message', 'russian');
        else
            $this->lang->load('message', 'english');

        return $value;
    }

    public function NotifyForCallResponse() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $call_type = ($this->input->post('call_type')) ? $this->input->post('call_type') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';

        if (!empty($user_id) && !empty($doctor_id) && !empty($call_type) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                //send notification
                define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
                $receiver_data = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                $sender_name = $user_record[0]->name;
                if (isset($receiver_data) && !empty($receiver_data)) {
                    if ($receiver_data[0]->device_type == "android") {
                        if (!empty($receiver_data[0]->device_token)) {
                            $registatoin_ids = array($receiver_data[0]->device_token);
                            $message_data = array('notification_type' => 'appointment', 'type' => $type, 'call_type' => $call_type);

                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $fields = array(
                                'registration_ids' => $registatoin_ids,
                                'data' => $message_data,
                            );
                            $headers = array(
                                'Authorization: key=' . API_KEY,
                                'Content-Type: application/json'
                            );

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                            $result = curl_exec($ch);
                            $res_array = json_decode($result);
                        }
                    } else {
                        $app_state = $this->app_state;
                        $deviceToken = $receiver_data[0]->device_token;
                        $body['aps'] = array(
                            'alert' => array(
                                'content-available' => 1,
                                'body' => "",
                            ),
                            'badge' => 1,
                            'notification_type' => 'appointment',
                            'type' => $type,
                            'call_type' => $call_type,
                            'sound' => 'default',
                        );

                        $passphrase = '123456789';
                        $ctx = stream_context_create();
                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorProduction.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/DoctorDevelopment.pem');
                        }
                        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        } else {
                            $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
                        }
                        $payload = json_encode($body);
                        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
                        $result = fwrite($fp, $msg, strlen($msg));
                        fclose($fp);
                    }
                }
                $this->response->success = 200;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    /*     * ********** Not In Use *************** */

    public function TestGenerateVoiceToken() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $new_token = "";

        if (!empty($name) && !empty($user_id)) {
            $user_data = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'patient'));
            $identity = $name;

            $token = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $identity);
            $voiceGrant = new Twilio\Jwt\Grants\VoiceGrant();
            $voiceGrant->setOutgoingApplicationSid($this->outgoingApplicationSid);
            if ($user_data[0]->device_type == 'ios')
                $voiceGrant->setPushCredentialSid($this->PatientiOSPushCredentialSid);
            if ($user_data[0]->device_type == 'android')
                $voiceGrant->setPushCredentialSid($this->PatientAndriodPushCredentialSid);

            $token->addGrant($voiceGrant);
            $new_token = $token->toJWT();
//doctor token
            $doctor_identity = '';
            $data = array('token' => $new_token, 'doctor_identity' => $doctor_identity);
            if (!empty($new_token)) {
                /* if ($type == 'call') {
                  $this->DoctorNotification('appointment', $user_id, $appointment_data[0]->doctor_id, 'patient', $appointment_id, $appointment_data[0]->appointment_type, 4, $doctor_identity);
                  } */
                $this->response->success = 200;
                $this->response->message = 'Token generated successfully.';
                $this->response->data = $data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 202;
                $this->response->message = $this->lang->line('went_wrong');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetPackage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';

        if (isset($user_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if (isset($doctor_id) && !empty($doctor_id)) {
                    $doctor_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
                    if (empty($doctor_record)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('doctor_id_error');
                        die(json_encode($this->response));
                    }
                    $packages_record = $this->Custom->get_where('packages', array('doctor_id' => $doctor_id));
                    if (empty($packages_record)) {
                        $this->response->success = 205;
                        $this->response->message = $this->lang->line('no_record');
                        die(json_encode($this->response));
                    }
                } else {
                    $packages_record = $this->Custom->get_data('packages');
                    if (empty($packages_record)) {
                        $this->response->success = 205;
                        $this->response->message = $this->lang->line('no_record');
                        die(json_encode($this->response));
                    }
                }

                foreach ($packages_record as $row) {
                    $where = array('users.id' => $row->doctor_id, 'users.role' => 'doctor');
                    $doctor_data = $this->PatientModel->SearchDoctors($where);
                    if (!empty($doctor_data)) {
                        if ($doctor_data[0]->speciality != '' || $doctor_data[0]->speciality != 0) {
                            $specility_record = $this->Custom->get_where('specility', array('id' => $doctor_data[0]->speciality));
                            if ($specility_record) {
                                if ($_POST['lang'] == 'en') {
                                    $row->speciality_name = $specility_record[0]->specility_name;
                                } else if ($_POST['lang'] == 'ru') {
                                    $row->speciality_name = $specility_record[0]->specility_name_ru;
                                } else {
                                    $row->speciality_name = $specility_record[0]->specility_name;
                                }
                            } else {
                                $row->speciality_name = "";
                            }
                        } else {
                            $row->speciality_name = "";
                        }
                        $row->title = $doctor_data[0]->title;
                        $row->name = $doctor_data[0]->name;
                        $row->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                    }
                    $row->image = ($row->image) ? base_url() . PACKAGE_URL . '/' . $row->image : "";
                }

                $this->response->success = 200;
                $this->response->message = 'Package Lists.';
                $this->response->data = $packages_record;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetPrescription() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'status' => 1, 'role' => 'patient'));
            if (isset($user_record) && !empty($user_record)) {
//check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->activate_status == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('account_deactivate');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $prescription_details = $this->Custom->query("select * from prescription where patient_id = $user_id ORDER BY id ASC LIMIT $offset, " . $per_page);
                if (!empty($prescription_details)) {
                    foreach ($prescription_details as $value) {
                        $medicine_rec = array();
                        $medicineIdArr = explode(',', $value->medicine_id);
                        foreach ($medicineIdArr as $row) {
                            $medicine_details = $this->Custom->get_where('medicine', array('id' => $row));
                            $medicine_rec[] = $medicine_details[0];
                        }
                        $value->medicine = $medicine_rec;
                    }
                    $this->response->success = 200;
                    $this->response->message = 'Prescription Details.';
                    $this->response->data = $prescription_details;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('userid_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    /*     * ********** For Zumcare App ********* */

    public function postLinkResponseHandler() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://zumcare.com/Zumcare/ResponseHandlerForAddCard.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
    }

    public function ResponseHandlerByAddCard() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://zumcare.com/Zumcare/ResponseHandlerByAddCard.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
    }

    public function EnquiryResponseHandler() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://zumcare.com/Zumcare/EnquiryResponseHandler.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
    }

    public function FailurePostLink() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://zumcare.com/Zumcare/FailurePostLink.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
    }

    public function EnquiryResponseHandlerByAddCard() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://zumcare.com/Zumcare/EnquiryResponseHandlerByAddCard.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
    }

    public function ResponseHandler() {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://zumcare.com/Zumcare/ResponseHandler.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
    }

}
