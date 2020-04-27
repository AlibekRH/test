<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class DoctorController extends CI_Controller {

    public $twilioAccountSid = '';
    public $twilioApiKey = '';
    public $twilioApiSecret = '';
    public $outgoingApplicationSid = '';
    public $serviceSid = '';
    public $DoctoriOSPushCredentialSid = '';
    public $DoctorAndriodPushCredentialSid = '';
    public $app_state = '';

    public function __construct() {
        parent::__construct();
        $this->response = new stdClass();
        ini_set("display_errors", 0);
        error_reporting(0);
        $this->SetTimezone();
        $this->load->model('DoctorModel');
        require_once $_SERVER['DOCUMENT_ROOT'] . '/Zumcare/vendor/autoload.php';
        $this->twilioAccountSid = 'ACf1f5263f780f17cf70fcbf8356031c2e';
        $this->twilioApiKey = 'SK622195c2c3d543e0d05f873e02b210ef';
        $this->twilioApiSecret = 'ScNJPArn0hXCSSTUbpVICD6BGV87kmKg';

        $this->serviceSid = 'IS17041c3738a5bf5a8934d27a0f588d3f';
        $this->outgoingApplicationSid = 'AP524f6078754db4a98262aaa6e64ea7ea';
        //$this->DoctoriOSPushCredentialSid = 'CRbecfa5ff4dea2b0efadce15240d6b185';
        $this->DoctoriOSPushCredentialSid = 'CRce85cbc840643a2841c33411ed79c814';
        $this->DoctorAndriodPushCredentialSid = "CR8c594f330fa5b0a0f5810b7645bc70ed";
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
        echo "Hello, project setup.";
    }

    public function SetTimezone() {
        $user_id = ($this->input->post('user_id', TRUE)) ? $this->input->post('user_id', TRUE) : '';
        $timezone = "UTC";
        if (!empty($user_id)) {
            $users_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
        //$content = 'This is testing email.';

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
            foreach ($language_record as $row) {
                $languages[] = array(
                    'language_id' => $row->id,
                    'language_name' => $row->name
                );
            }
            $this->response->success = 200;
            $this->response->message = $this->lang->line('language_list');
            $this->response->data = $languages;
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

    public function GetSpecility() {
        $specility_record = $this->Custom->get_where('specility', array('parent_id' => 0));
        if ($specility_record) {
            foreach ($specility_record as $row) {
                if ($_POST['lang'] == 'en') {
                    $speciality_data[] = array(
                        'id' => $row->id,
                        'specility_name' => $row->specility_name,
                        'created_at' => $row->created_at
                    );
                } else if ($_POST['lang'] == 'ru') {
                    $speciality_data[] = array(
                        'id' => $row->id,
                        'specility_name' => $row->specility_name_ru,
                        'created_at' => $row->created_at
                    );
                } else {
                    $speciality_data[] = array(
                        'id' => $row->id,
                        'specility_name' => $row->specility_name,
                        'created_at' => $row->created_at
                    );
                }
            }
            $this->response->success = 200;
            $this->response->message = $this->lang->line('speciality_list');
            $this->response->data = $speciality_data;
            die(json_encode($this->response));
        } else {
            $this->response->success = 205;
            $this->response->message = $this->lang->line('speciality_list_error');
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
            $this->response->success = 201;
            $this->response->message = "Please send specility id";
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

    public function SignUp() {
        $name = (isset($_POST['name']) && !empty($_POST['name'])) ? $_POST['name'] : "";
        $title = (isset($_POST['title']) && !empty($_POST['title'])) ? $_POST['title'] : "";
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : "";
        $password = (isset($_POST['password']) && !empty($_POST['password'])) ? $_POST['password'] : "";
        $country_code = ($this->input->post('country_code', TRUE)) ? $this->input->post('country_code', TRUE) : '';
        $phone_number = (isset($_POST['phone_number']) && !empty($_POST['phone_number'])) ? $_POST['phone_number'] : "";
        $device_token = (isset($_POST['device_token']) && !empty($_POST['device_token'])) ? $_POST['device_token'] : '';
        $voip_device_token = ($this->input->post('voip_device_token', TRUE)) ? $this->input->post('voip_device_token', TRUE) : '';
        $device_type = (isset($_POST['device_type']) && !empty($_POST['device_type'])) ? $_POST['device_type'] : '';
        $latitude = (isset($_POST['latitude']) && !empty($_POST['latitude'])) ? $_POST['latitude'] : '';
        $longitude = (isset($_POST['longitude']) && !empty($_POST['longitude'])) ? $_POST['longitude'] : '';
        $timezone = (isset($_POST['timezone']) && !empty($_POST['timezone'])) ? $_POST['timezone'] : '';
        $created_at = strtotime(date("Y-m-d H:i:s"));

        if (!empty($name) && !empty($email) && !empty($password) && !empty($country_code) && !empty($phone_number)) {
            $check_mail_already_exist = $this->Custom->query("SELECT * FROM users WHERE email = '" . $email . "' AND role = 'doctor'");
            if ($check_mail_already_exist) {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('email_exist');
                die(json_encode($this->response));
            }
            /* $check_phone_number_already_exist = $this->Custom->query("SELECT * FROM users WHERE phone_number = '" . $phone_number . "' AND role = 'doctor'");
              if ($check_phone_number_already_exist) {
              $this->response->success = 203;
              $this->response->message = 'Phone Number is already exists.';
              die(json_encode($this->response));
              } */
            $otp = GenerateOTP(6);
            if ($timezone != '')
                date_default_timezone_set($timezone);
            $insert_arr = array(
                'comet_chat_id' => '',
                'title' => $title,
                'name' => $name,
                'email' => $email,
                'password' => md5($password),
                'country_code' => $country_code,
                'phone_number' => $phone_number,
                'role' => 'doctor',
                'device_type' => $device_type,
                'device_token' => $device_token,
                'voip_device_token' => $voip_device_token,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'verification_otp' => $otp,
                'authentication_token' => '',
                'number_verified' => 0,
                'status' => 0,
                'feature_date' => strtotime(date("Y-m-d")),
                'timezone' => $timezone,
                'created_at' => $created_at
            );
            $insert_id = $this->Custom->insert_data('users', $insert_arr);
            if ($insert_id) {
                //update user data
                $user_uid = $insert_id . rand(1111, 9999);
                $this->Custom->update_where('users', array('user_uid' => $user_uid), array('id' => $insert_id));
                //insert in doctor profile
                $this->Custom->insert_data('doctor_profile', array('user_id' => $insert_id, 'created_at' => $created_at));
                //insert in notification settings
                $this->Custom->insert_data('notification_settings', array('user_id' => $insert_id));
                //insert in clinic hours
                for ($day = 1; $day <= 7; $day++) {
                    $insert_clinic_hours = array(
                        'user_id' => $insert_id,
                        'day_id' => $day,
                        'start_hour' => strtotime(date('H:i', strtotime("10:00"))),
                        'end_hour' => strtotime(date('H:i', strtotime("17:00"))),
                        'working_status' => 1
                    );
                    $this->Custom->insert_data('clinic_hours', $insert_clinic_hours);
                }
                //consultation settings
                $this->Custom->insert_data('consultation_settings', array('user_id' => $insert_id, 'type' => 'basic'));
                //send mail
                $link = base_url('DoctorController/ConfirmAccount/' . urlencode($email) . '/' . $_POST['lang']);

                $mail_data = array('link' => $link, 'Verify_Email' => $this->lang->line('Verify_Email'), 'Hello' => $this->lang->line('Hello'), 'verification_content' => $this->lang->line('verification_content'), 'Thanks' => $this->lang->line('Thanks'), 'team' => $this->lang->line('team'));
                $content = $this->load->view('mail/signup', $mail_data, TRUE);
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
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function ResendVerificationEmail() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : "";
        if (!empty($email)) {
            $user_rec = $this->Custom->query("SELECT * FROM users WHERE email = '" . $email . "' AND role = 'doctor'");
            if (empty($user_rec)) {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('email_not_exist');
                die(json_encode($this->response));
            }
            //send mail
            $link = base_url('DoctorController/ConfirmAccount/' . urlencode($email) . '/' . $_POST['lang']);

            $mail_data = array('link' => $link, 'Verify_Email' => $this->lang->line('Verify_Email'), 'Hello' => $this->lang->line('Hello'), 'verification_content' => $this->lang->line('verification_content'), 'Thanks' => $this->lang->line('Thanks'), 'team' => $this->lang->line('team'));
            $content = $this->load->view('mail/signup', $mail_data, TRUE);
            $subject = $this->lang->line('verification_subject');
            $this->SendMail($email, $subject, $content);

            $result = $this->Custom->update('users', array('status' => 0), 'id', $user_rec[0]->id);

            $this->response->success = 200;
            $this->response->message = $this->lang->line('verification_mail');
            die(json_encode($this->response));
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function ConfirmAccount() {
        $email = $this->uri->segment(3);
        $language = $this->uri->segment(4);
        if ($language == 'en') {
            $language1 = 'english';
        } else if ($language == 'ru') {
            $language1 = 'russian';
        } else {
            $language1 = 'english';
        }
        $email = (isset($email) && !empty($email)) ? urldecode($email) : '';
        if (!empty($email)) {
            $check_user = $this->Custom->get_where('users', array('email' => $email, 'role' => 'doctor'));
            if (isset($check_user) && !empty($check_user)) {
                $user_id = $check_user[0]->id;
                $status = $check_user[0]->status;
                if ($status == 1) {
                    $mail_data['Hello'] = $this->CreateMessageForNotification($language1, 'Hello');
                    $mail_data['Thanks'] = $this->CreateMessageForNotification($language1, 'Thanks');
                    $mail_data['team'] = $this->CreateMessageForNotification($language1, 'team');
                    $mail_data['message'] = $this->CreateMessageForNotification($language1, 'account_already_confirmed');
                    $this->load->view('mail/confirmation_email', $mail_data);
                } else {
                    $result = $this->Custom->update('users', array('status' => 1), 'id', $user_id);
                    $mail_data1['Hello'] = $this->CreateMessageForNotification($language1, 'Hello');
                    $mail_data1['message'] = $this->CreateMessageForNotification($language1, 'wait_admin_approval_content');
                    $mail_data1['faithfully'] = $this->CreateMessageForNotification($language1, 'faithfully');
                    $mail_data1['team'] = $this->CreateMessageForNotification($language1, 'team');
                    $content = $this->load->view('mail/wait_confirmation_email', $mail_data1, TRUE);
                    $subject = $this->lang->line('wait_admin_approval_subject');
                    $this->SendMail($email, $subject, $content);

                    $mail_data['Hello'] = $this->CreateMessageForNotification($language1, 'Hello');
                    $mail_data['Thanks'] = $this->CreateMessageForNotification($language1, 'Thanks');
                    $mail_data['team'] = $this->CreateMessageForNotification($language1, 'team');
                    $mail_data['message'] = $this->CreateMessageForNotification($language1, 'account_confirmed');
                    $this->load->view('mail/confirmation_email', $mail_data);
                }
            } else {
                $mail_data['Hello'] = $this->CreateMessageForNotification($language1, 'Hello');
                $mail_data['Thanks'] = $this->CreateMessageForNotification($language1, 'Thanks');
                $mail_data['team'] = $this->CreateMessageForNotification($language1, 'team');
                $mail_data['message'] = $this->CreateMessageForNotification($language1, 'account_not_confirmed');
                $this->load->view('mail/confirmation_email', $mail_data);
            }
        } else {
            $mail_data['Hello'] = $this->CreateMessageForNotification($language1, 'Hello');
            $mail_data['Thanks'] = $this->CreateMessageForNotification($language1, 'Thanks');
            $mail_data['team'] = $this->CreateMessageForNotification($language1, 'team');
            $mail_data['message'] = $this->CreateMessageForNotification($language1, 'account_not_confirmed');
            $this->load->view('mail/confirmation_email', $mail_data);
        }
    }

    public function VerifyOTP() {
        $user_id = ($this->input->post('user_id', TRUE)) ? $this->input->post('user_id', TRUE) : '';
        $phone_number = ($this->input->post('phone_number', TRUE)) ? $this->input->post('phone_number', TRUE) : '';
        $otp = ($this->input->post('otp', TRUE)) ? $this->input->post('otp', TRUE) : '';
        if (!empty($user_id) && !empty($phone_number) && !empty($otp)) {
            $user_data = $this->Custom->get_where('users', array('id' => $user_id, 'phone_number' => $phone_number, 'role' => 'doctor'));
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
                $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                if ($doctor_data[0]->speciality != '') {
                    $speciality = explode(',', $doctor_data[0]->speciality);
                    foreach ($speciality as $spe) {
                        $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                        if ($specility_record) {
                            if ($_POST['lang'] == 'en') {
                                $speciality_data[] = array(
                                    'id' => $spe,
                                    'specility_name' => $specility_record[0]->specility_name
                                );
                            } else if ($_POST['lang'] == 'ru') {
                                $speciality_data[] = array(
                                    'id' => $spe,
                                    'specility_name' => $specility_record[0]->specility_name_ru
                                );
                            } else {
                                $speciality_data[] = array(
                                    'id' => $spe,
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
                                'treatment_id' => $des,
                                'treatment_name' => $treatment_record[0]->name
                            );
                        } else {
                            $treatment_data[] = array(
                                'treatment_id' => $des,
                                'treatment_name' => ""
                            );
                        }
                    }
                    $doctor_data[0]->treatment = $treatment_data;
                } else {
                    $doctor_data[0]->treatment = array();
                }
                $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
                $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
                $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
                $doctor_data[0]->signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";
                $education_details = GetDetails('doctor_education', array('user_id' => $user_id));
                $doctor_data[0]->education_details = ($education_details) ? $education_details : array();

                $this->response->success = 200;
                $this->response->message = $this->lang->line('account_verified');
                $this->response->data = $doctor_data[0];
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

    public function Login() {
        $type = ($this->input->post('type', TRUE)) ? $this->input->post('type', TRUE) : '';
        $country_code = ($this->input->post('country_code', TRUE)) ? $this->input->post('country_code', TRUE) : '';
        $phone_number = ($this->input->post('phone_number', TRUE)) ? $this->input->post('phone_number', TRUE) : '';
        $email = ($this->input->post('email', TRUE)) ? $this->input->post('email', TRUE) : '';
        $password = ($this->input->post('password', TRUE)) ? $this->input->post('password', TRUE) : '';
        $latitude = ($this->input->post('latitude', TRUE)) ? $this->input->post('latitude', TRUE) : '';
        $longitude = ($this->input->post('longitude', TRUE)) ? $this->input->post('longitude', TRUE) : '';
        $device_token = ($this->input->post('device_token', TRUE)) ? $this->input->post('device_token', TRUE) : '';
        $voip_device_token = ($this->input->post('voip_device_token', TRUE)) ? $this->input->post('voip_device_token', TRUE) : '';
        $device_type = ($this->input->post('device_type', TRUE)) ? $this->input->post('device_type', TRUE) : '';
        $timezone = ($this->input->post('timezone', TRUE)) ? $this->input->post('timezone', TRUE) : '';

        if (!empty($type)) {
            switch ($type):
                case 'manual':
                    if (!empty($email) && !empty($password)) {
                        $user_data_email = $this->Custom->get_where('users', array('email' => $email, 'role' => 'doctor'));
                        if ($user_data_email) {
                            $user_data = $this->Custom->get_where('users', array('email' => $email, 'password' => md5($password), 'role' => 'doctor'));
                            if (empty($user_data)) {
                                $this->response->success = 203;
                                $this->response->message = $this->lang->line('invalid_email_password');
                                die(json_encode($this->response));
                            }

                            if ($user_data[0]->status != 1) {
                                $this->response->success = 204;
                                $this->response->message = $this->lang->line('account_not_activate');
                                die(json_encode($this->response));
                            }
                            /* if ($user_data[0]->approve_status == 0) {
                              $this->response->success = 206;
                              $this->response->message = $this->lang->line('not_approved');
                              die(json_encode($this->response));
                              } */

                            $user_id = $user_data[0]->id;
                            $auth_token = GenerateRandomNumber(11);
                            if ($user_data[0]->user_uid == "")
                                $user_uid = $user_id . rand(1111, 9999);
                            else
                                $user_uid = $user_data[0]->user_uid;
                            $update_arr = array(
                                'user_uid' => $user_uid,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'authentication_token' => $auth_token,
                                'activate_status' => 1,
                                'timezone' => $timezone
                            );
                            $this->Custom->update_where('users', $update_arr, array('id' => $user_id));
                            if (!empty($device_token))
                                $this->Custom->update_where('users', array('device_token' => $device_token), array('id' => $user_id));

                            if (!empty($voip_device_token))
                                $this->Custom->update_where('users', array('voip_device_token' => $voip_device_token), array('id' => $user_id));

                            if (!empty($device_type))
                                $this->Custom->update_where('users', array('device_type' => $device_type), array('id' => $user_id));

                            //get data
                            $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                            if ($doctor_data[0]->clinic_locality != "") {
                                $clinicAddress = str_replace($doctor_data[0]->clinic_locality . ',', '', $doctor_data[0]->clinic_address);
                                $doctor_data[0]->clinic_address = $clinicAddress;
                            }
                            if ($doctor_data[0]->speciality != '') {
                                $speciality = explode(',', $doctor_data[0]->speciality);
                                foreach ($speciality as $spe) {
                                    $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                                    if ($specility_record) {
                                        if ($_POST['lang'] == 'en') {
                                            $speciality_data[] = array(
                                                'id' => $spe,
                                                'specility_name' => $specility_record[0]->specility_name
                                            );
                                        } else if ($_POST['lang'] == 'ru') {
                                            $speciality_data[] = array(
                                                'id' => $spe,
                                                'specility_name' => $specility_record[0]->specility_name_ru
                                            );
                                        } else {
                                            $speciality_data[] = array(
                                                'id' => $spe,
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
                            $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                            $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
                            $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
                            $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
                            $doctor_data[0]->signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";
                            $education_details = GetDetails('doctor_education', array('user_id' => $user_id));
                            $doctor_data[0]->education_details = ($education_details) ? $education_details : array();

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('logged_in');
                            $this->response->data = $doctor_data[0];
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('email_not_exist');
                            die(json_encode($this->response));
                        }
                    } else {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                    break;
                case 'phone_number':
                    if (!empty($country_code) && !empty($phone_number)) {
                        $user_data_phone = $this->Custom->get_where('users', array('country_code' => $country_code, 'phone_number' => $phone_number, 'role' => 'doctor'));
                        if ($user_data_phone) {
                            $otp = GenerateOTP(6);
                            $user_id = $user_data_phone[0]->id;
                            if ($user_data_phone[0]->user_uid == "")
                                $user_uid = $user_id . rand(1111, 9999);
                            else
                                $user_uid = $user_data_phone[0]->user_uid;
                            $update_arr = array(
                                'user_uid' => $user_uid,
                                'verification_otp' => $otp,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'activate_status' => 1,
                                'timezone' => $timezone
                            );
                            $this->Custom->update_where('users', $update_arr, array('id' => $user_id));
                            if (!empty($device_token))
                                $this->Custom->update_where('users', array('device_token' => $device_token), array('id' => $user_id));

                            if (!empty($voip_device_token))
                                $this->Custom->update_where('users', array('voip_device_token' => $voip_device_token), array('id' => $user_id));

                            if (!empty($device_type))
                                $this->Custom->update_where('users', array('device_type' => $device_type), array('id' => $user_id));

                            //send mail
                            $link = base_url('DoctorController/ConfirmAccount/' . urlencode($email));

                            $mail_data = array('otp' => $otp, 'Hello' => $this->lang->line('Hello'), 'verification_content' => $this->lang->line('verification_otp'), 'Thanks' => $this->lang->line('Thanks'), 'team' => $this->lang->line('team'));
                            $content = $this->load->view('mail/confirmation_mail', $mail_data, TRUE);
                            $subject = $this->lang->line('verification_subject');

                            $this->SendMail($user_data_phone[0]->email, $subject, $content);

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('verify_otp');
                            $this->response->data = array('user_id' => $user_id, 'phone_number' => $phone_number, 'otp' => $otp);
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('phone_no_not_exist');
                            die(json_encode($this->response));
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

    public function Logout() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (isset($user_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_data('users', 'id', $user_id);
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $user_id);
                if (empty($auth_record)) {
                    $this->response->success = 404;
                    $this->response->message = $this->lang->line('authentication_error');
                    die(json_encode($this->response));
                }
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

    public function ForgotPassword() {
        $email = (isset($_POST['email']) && !empty($_POST['email'])) ? $_POST['email'] : "";
        if (!empty($email)) {
            $user_record = $this->Custom->get_where('users', array('email' => $email, 'role' => 'doctor'));
            if ($user_record) {
                $password = GenerateRandomNumber(7);
                $new_password = md5($password);
                if ($this->Custom->update('users', array('password' => $new_password), 'id', $user_record[0]->id)) {
                    $mail_data = array('password' => $password, 'Hello' => $this->lang->line('Hello'), 'forgot_content' => $this->lang->line('forgot_content'), 'Thanks' => $this->lang->line('Thanks'), 'team' => $this->lang->line('team'));
                    $content = $this->load->view('mail/forgot_mail', $mail_data, TRUE);
                    $subject = $this->lang->line('forgot_subject');
                    $this->SendMail($email, $subject, $content);

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('forgot_password');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('email_not_exist');
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                    ;
                    die(json_encode($this->response));
                }
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                if ($doctor_data[0]->clinic_locality != "") {
                    $clinicAddress = str_replace($doctor_data[0]->clinic_locality . ',', '', $doctor_data[0]->clinic_address);
                    $doctor_data[0]->clinic_address = $clinicAddress;
                }
                if ($doctor_data[0]->speciality != '') {
                    $speciality = explode(',', $doctor_data[0]->speciality);
                    foreach ($speciality as $spe) {
                        $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                        if ($specility_record) {
                            if ($_POST['lang'] == 'en') {
                                $speciality_data[] = array(
                                    'id' => $spe,
                                    'specility_name' => $specility_record[0]->specility_name
                                );
                            } else if ($_POST['lang'] == 'ru') {
                                $speciality_data[] = array(
                                    'id' => $spe,
                                    'specility_name' => $specility_record[0]->specility_name_ru
                                );
                            } else {
                                $speciality_data[] = array(
                                    'id' => $spe,
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
                $education_details = GetDetails('doctor_education', array('user_id' => $user_id));
                $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
                $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
                $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
                $doctor_data[0]->signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";
                $doctor_data[0]->education_details = ($education_details) ? $education_details : array();

                if ($doctor_data) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('user_details');
                    $this->response->data = $doctor_data[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 203;
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

    public function UpdateProfileImage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (isset($user_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $user_profile_record = $this->Custom->get_where('doctor_profile', array('user_id' => $user_id));
                if (!empty($_FILES['profile_image']['name'])) {
                    $name = $_FILES['profile_image']['name'];
                    $get_ext = explode(".", $name);
                    $ext = end($get_ext);
                    $new_name = time() . '.' . $ext;
                    $config['file_name'] = $new_name;
                    $config['upload_path'] = DOCTOR_PROFILE_PATH . '/';
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
                            unlink(DOCTOR_PROFILE_PATH . '/' . $user_profile_record[0]->profile_image);
                        $_POST['profile_image'] = $new_name;
                    }
                    $update_status = $this->Custom->update_where('doctor_profile', array('profile_image' => $_POST['profile_image']), array('user_id' => $user_id));
                    if ($update_status) {
                        $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                        $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";

                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('profile_image_updated');
                        $this->response->data = $doctor_data[0]->profile_image;
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

    public function UpdateProfile() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case 'basic':
                        unset($_POST['user_id']);
                        unset($_POST['type']);
                        unset($_POST['lang']);
                        $update_status = $this->Custom->update('users', $_POST, 'id', $user_id);
                        if ($update_status) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('profile_updated');
                            $this->response->data = $_POST;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'profile':
                        unset($_POST['user_id']);
                        unset($_POST['type']);
                        unset($_POST['lang']);
                        $update_status = $this->Custom->update('doctor_profile', $_POST, 'user_id', $user_id);
                        if ($update_status) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('profile_updated');
                            $this->response->data = $_POST;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'id_number':
                        $id_number = ($this->input->post('id_number')) ? $this->input->post('id_number') : '';
                        if (empty($id_number)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        unset($_POST['user_id']);
                        unset($_POST['type']);
                        unset($_POST['lang']);
                        $update_status = $this->Custom->update('doctor_profile', $_POST, 'user_id', $user_id);
                        if ($update_status) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('profile_updated');
                            $this->response->data = $_POST;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'qualification':
                        $degree = ($this->input->post('degree')) ? $this->input->post('degree') : '';
                        $university = ($this->input->post('university')) ? $this->input->post('university') : '';
                        $graduation_year = ($this->input->post('graduation_year')) ? $this->input->post('graduation_year') : '';
                        if (empty($degree) || empty($university) || empty($graduation_year)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        unset($_POST['user_id']);
                        unset($_POST['type']);
                        $doctor_education = $this->Custom->get_where('doctor_education', array('user_id' => $user_id, 'degree' => $degree, 'university' => $university, 'graduation_year' => $graduation_year));
                        if (isset($doctor_education) && !empty($doctor_education)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('qualification_exist');
                            die(json_encode($this->response));
                        }
                        $insert_arr = array(
                            'user_id' => $user_id,
                            'degree' => $degree,
                            'university' => $university,
                            'graduation_year' => $graduation_year
                        );
                        $insert_id = $this->Custom->insert_data('doctor_education', $insert_arr);
                        if ($insert_id) {
                            $doctor_education = $this->Custom->get_where('doctor_education', array('id' => $insert_id));
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('qualification_added');
                            $this->response->data = $doctor_education[0];
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'clinic_details':
                        $clinic_name = ($this->input->post('clinic_name')) ? $this->input->post('clinic_name') : '';
                        $clinic_phone_number = ($this->input->post('clinic_phone_number')) ? $this->input->post('clinic_phone_number') : '';
                        $clinic_address = ($this->input->post('clinic_address')) ? $this->input->post('clinic_address') : '';
                        $clinic_city = ($this->input->post('clinic_city')) ? $this->input->post('clinic_city') : '';
                        $clinic_locality = ($this->input->post('clinic_locality')) ? $this->input->post('clinic_locality') : '';
                        $clinic_pincode = ($this->input->post('clinic_pincode')) ? $this->input->post('clinic_pincode') : '';
                        $clinic_state = ($this->input->post('clinic_state')) ? $this->input->post('clinic_state') : '';
                        $availability = ($this->input->post('availability')) ? $this->input->post('availability') : '';
                        $consulation_fee = ($this->input->post('consulation_fee')) ? $this->input->post('consulation_fee') : '';
                        $clinic_lat = ($this->input->post('clinic_lat')) ? $this->input->post('clinic_lat') : '';
                        $clinic_lng = ($this->input->post('clinic_lng')) ? $this->input->post('clinic_lng') : '';
                        if (empty($clinic_name) || empty($clinic_address)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $_POST['clinic_address'] = ($_POST['clinic_locality'] && $_POST['clinic_locality'] != '') ? $_POST['clinic_locality'] . "," . $_POST['clinic_address'] : $_POST['clinic_address'];
                        unset($_POST['user_id']);
                        unset($_POST['type']);
                        unset($_POST['lang']);
                        $update_status = $this->Custom->update('doctor_profile', $_POST, 'user_id', $user_id);
                        if ($update_status) {
                            $_POST['clinic_address'] = $clinic_address;
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('profile_updated');
                            $this->response->data = $_POST;
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

    public function UpdateDoctorDocument() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        if (isset($user_id) && !empty($user_id) && isset($type) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if (empty($_FILES['image']['name'])) {
                    $this->response->success = 201;
                    $this->response->message = "Please send image";
                    die(json_encode($this->response));
                }
                $user_profile_record = $this->Custom->get_where('doctor_profile', array('user_id' => $user_id));
                switch ($type):
                    case 'medical':
                        $name = $_FILES['image']['name'];
                        $get_ext = explode(".", $name);
                        $ext = end($get_ext);
                        $new_name = time() . '.' . $ext;
                        $config['file_name'] = $new_name;
                        $config['upload_path'] = DOCTOR_DOCUMENT_PATH . '/';
                        $config['allowed_types'] = '*';
                        $config['overwrite'] = TRUE;
                        $config['remove_spaces'] = TRUE;

                        if (!is_dir($config['upload_path']))
                            die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('image')) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('image_not_upload');
                            die(json_encode($this->response));
                        } else {
                            if ($user_profile_record[0]->medical_registration_proof != "")
                                unlink(DOCTOR_DOCUMENT_PATH . '/' . $user_profile_record[0]->medical_registration_proof);
                            $image = $new_name;
                        }
                        $update_status = $this->Custom->update_where('doctor_profile', array('medical_registration_proof' => $image), array('user_id' => $user_id));
                        if ($update_status) {
                            $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                            $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('medical_proof_updated');
                            $this->response->data = $doctor_data[0]->medical_registration_proof;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'photo_id':
                        $name = $_FILES['image']['name'];
                        $get_ext = explode(".", $name);
                        $ext = end($get_ext);
                        $new_name = time() . '.' . $ext;
                        $config['file_name'] = $new_name;
                        $config['upload_path'] = DOCTOR_DOCUMENT_PATH . '/';
                        $config['allowed_types'] = '*';
                        $config['overwrite'] = TRUE;
                        $config['remove_spaces'] = TRUE;

                        if (!is_dir($config['upload_path']))
                            die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('image')) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('image_not_upload');
                            die(json_encode($this->response));
                        } else {
                            if ($user_profile_record[0]->photo_id_proof != "")
                                unlink(DOCTOR_DOCUMENT_PATH . '/' . $user_profile_record[0]->photo_id_proof);
                            $image = $new_name;
                        }
                        $update_status = $this->Custom->update_where('doctor_profile', array('photo_id_proof' => $image), array('user_id' => $user_id));
                        if ($update_status) {
                            $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                            $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('photoid_proof_updated');
                            $this->response->data = $doctor_data[0]->photo_id_proof;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'degree':
                        $name = $_FILES['image']['name'];
                        $get_ext = explode(".", $name);
                        $ext = end($get_ext);
                        $new_name = time() . '.' . $ext;
                        $config['file_name'] = $new_name;
                        $config['upload_path'] = DOCTOR_DOCUMENT_PATH . '/';
                        $config['allowed_types'] = '*';
                        $config['overwrite'] = TRUE;
                        $config['remove_spaces'] = TRUE;

                        if (!is_dir($config['upload_path']))
                            die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('image')) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('image_not_upload');
                            die(json_encode($this->response));
                        } else {
                            if ($user_profile_record[0]->degree_proof != "")
                                unlink(DOCTOR_DOCUMENT_PATH . '/' . $user_profile_record[0]->degree_proof);
                            $image = $new_name;
                        }
                        $update_status = $this->Custom->update_where('doctor_profile', array('degree_proof' => $image), array('user_id' => $user_id));
                        if ($update_status) {
                            $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                            $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('degree_proof_updated');
                            $this->response->data = $doctor_data[0]->degree_proof;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'signature':
                        $name = $_FILES['image']['name'];
                        $get_ext = explode(".", $name);
                        $ext = end($get_ext);
                        $new_name = time() . '.' . $ext;
                        $config['file_name'] = $new_name;
                        $config['upload_path'] = DOCTOR_DOCUMENT_PATH . '/';
                        $config['allowed_types'] = '*';
                        $config['overwrite'] = TRUE;
                        $config['remove_spaces'] = TRUE;

                        if (!is_dir($config['upload_path']))
                            die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('image')) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('image_not_upload');
                            die(json_encode($this->response));
                        } else {
                            if ($user_profile_record[0]->signature != "")
                                unlink(DOCTOR_DOCUMENT_PATH . '/' . $user_profile_record[0]->signature);
                            $image = $new_name;
                        }
                        $update_status = $this->Custom->update_where('doctor_profile', array('signature' => $image), array('user_id' => $user_id));
                        if ($update_status) {
                            $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                            $doctor_data[0]->signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('signature_proof_updated');
                            $this->response->data = $doctor_data[0]->signature;
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

    public function UpdateAccount() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $email = ($this->input->post('email')) ? $this->input->post('email') : '';
        $phone_number = ($this->input->post('phone_number')) ? $this->input->post('phone_number') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $registration_no = ($this->input->post('registration_no')) ? $this->input->post('registration_no') : '';
        if (isset($user_id) && !empty($user_id) && isset($name) && !empty($name) && isset($email) && !empty($email) && isset($phone_number) && !empty($phone_number) && isset($gender) && !empty($gender) && isset($registration_no) && !empty($registration_no)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $updateBasicArr = array(
                    'name' => $name,
                    'email' => $email,
                    'phone_number' => $phone_number
                );
                $update_res = $this->Custom->update("users", $updateBasicArr, 'id', $user_id);
                if ($update_res) {
                    $updateProfileArr = array(
                        'gender' => $gender,
                        'registration_no' => $registration_no
                    );
                    $update_res1 = $this->Custom->update("doctor_profile", $updateProfileArr, 'user_id', $user_id);
                    $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                    if ($doctor_data[0]->speciality != '') {
                        $speciality = explode(',', $doctor_data[0]->speciality);
                        foreach ($speciality as $spe) {
                            $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                            if ($specility_record) {
                                if ($_POST['lang'] == 'en') {
                                    $speciality_data[] = array(
                                        'id' => $spe,
                                        'specility_name' => $specility_record[0]->specility_name
                                    );
                                } else if ($_POST['lang'] == 'ru') {
                                    $speciality_data[] = array(
                                        'id' => $spe,
                                        'specility_name' => $specility_record[0]->specility_name_ru
                                    );
                                } else {
                                    $speciality_data[] = array(
                                        'id' => $spe,
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
                    $education_details = GetDetails('doctor_education', array('user_id' => $user_id));
                    $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                    $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
                    $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
                    $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
                    $doctor_data[0]->signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";
                    $doctor_data[0]->education_details = ($education_details) ? $education_details : array();

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('account_details_updated');
                    $this->response->data = $doctor_data[0];
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

    public function ChangePassword() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $old_password = ($this->input->post('old_password')) ? $this->input->post('old_password') : '';
        $new_password = ($this->input->post('new_password')) ? $this->input->post('new_password') : '';
        if (isset($user_id) && !empty($user_id) && isset($old_password) && !empty($old_password) && isset($new_password) && !empty($new_password)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if ($user_record[0]->password != md5($old_password)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('old_password_error');
                    die(json_encode($this->response));
                }
                $update_res = $this->Custom->update("users", array('password' => md5($new_password)), 'id', $user_id);
                if ($update_res) {
                    $doctor_data = $this->DoctorModel->GetDoctorProfile($user_id);
                    if ($doctor_data[0]->speciality != '') {
                        $speciality = explode(',', $doctor_data[0]->speciality);
                        foreach ($speciality as $spe) {
                            $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                            if ($specility_record) {
                                if ($_POST['lang'] == 'en') {
                                    $speciality_name[] = $specility_record[0]->specility_name;
                                } else if ($_POST['lang'] == 'ru') {
                                    $speciality_name[] = $specility_record[0]->specility_name_ru;
                                } else {
                                    $speciality_name[] = $specility_record[0]->specility_name;
                                }
                            } else {
                                $speciality_name[] = "";
                            }
                        }
                        $doctor_data[0]->speciality_name = $speciality_name;
                    } else {
                        $doctor_data[0]->speciality_name = array();
                    }
                    if ($doctor_data[0]->diseases != '') {
                        $diseases = explode(',', $doctor_data[0]->diseases);
                        foreach ($diseases as $des) {
                            $diseases_record = $this->Custom->get_where('diseases', array('id' => $des));
                            if ($diseases_record)
                                $diseases_name[] = $diseases_record[0]->diseases_name;
                            else
                                $diseases_name[] = "";
                        }
                        $doctor_data[0]->diseases_name = $diseases_name;
                    } else {
                        $doctor_data[0]->diseases_name = array();
                    }
                    if ($doctor_data[0]->treatment != '') {
                        $treatment = explode(',', $doctor_data[0]->treatment);
                        foreach ($treatment as $tre) {
                            $treatment_record = $this->Custom->get_where('treatment', array('id' => $tre));
                            if ($treatment_record) {
                                $treatment_data[] = array(
                                    'treatment_id' => $des,
                                    'treatment_name' => $treatment_record[0]->name
                                );
                            } else {
                                $treatment_data[] = array(
                                    'treatment_id' => $des,
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
                                    'language_id' => $des,
                                    'language_name' => ""
                                );
                            }
                        }
                        $doctor_data[0]->language = $language_data;
                    } else {
                        $doctor_data[0]->language = array();
                    }
                    $education_details = GetDetails('doctor_education', array('user_id' => $user_id));
                    $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                    $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
                    $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
                    $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
                    $doctor_data[0]->education_details = ($education_details) ? $education_details : array();

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('password_changed');
                    $this->response->data = $doctor_data[0];
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

    public function AddBankDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $account_holder_name = ($this->input->post('account_holder_name')) ? $this->input->post('account_holder_name') : '';
        $bank_name = ($this->input->post('bank_name')) ? $this->input->post('bank_name') : '';
        $branch_address = ($this->input->post('branch_address')) ? $this->input->post('branch_address') : '';
        $ifsc_code = ($this->input->post('ifsc_code')) ? $this->input->post('ifsc_code') : '';
        $account_number = ($this->input->post('account_number')) ? $this->input->post('account_number') : '';
        $account_type = ($this->input->post('account_type')) ? $this->input->post('account_type') : '';
        $micr_code = ($this->input->post('micr_code')) ? $this->input->post('micr_code') : '';
        $registration_date = ($this->input->post('registration_date')) ? $this->input->post('registration_date') : '';
        $business_address = ($this->input->post('business_address')) ? $this->input->post('business_address') : '';
        if (!empty($user_id) && !empty($account_holder_name) && !empty($bank_name) && !empty($branch_address) && !empty($ifsc_code) && !empty($account_number) && !empty($account_type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $bank_details = $this->Custom->get_where('bank_details', array('user_id' => $user_id, 'account_number' => $account_number));
                if (!empty($bank_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('bank_detail_exist');
                    die(json_encode($this->response));
                }
                $_POST['created_at'] = strtotime(date("Y-m-d H:i:s"));
                unset($_POST['lang']);
                $insert_id = $this->Custom->insert_data('bank_details', $_POST);
                if ($insert_id) {
                    $bank_details = $this->Custom->get_where('bank_details', array('id' => $insert_id));
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('bank_detail_added');
                    $this->response->data = $bank_details[0];
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

    public function UpdateBankDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $account_id = ($this->input->post('account_id')) ? $this->input->post('account_id') : '';
        $account_holder_name = ($this->input->post('account_holder_name')) ? $this->input->post('account_holder_name') : '';
        $bank_name = ($this->input->post('bank_name')) ? $this->input->post('bank_name') : '';
        $branch_address = ($this->input->post('branch_address')) ? $this->input->post('branch_address') : '';
        $ifsc_code = ($this->input->post('ifsc_code')) ? $this->input->post('ifsc_code') : '';
        $account_number = ($this->input->post('account_number')) ? $this->input->post('account_number') : '';
        $account_type = ($this->input->post('account_type')) ? $this->input->post('account_type') : '';
        $micr_code = ($this->input->post('micr_code')) ? $this->input->post('micr_code') : '';
        $registration_date = ($this->input->post('registration_date')) ? $this->input->post('registration_date') : '';
        $business_address = ($this->input->post('business_address')) ? $this->input->post('business_address') : '';
        if (!empty($user_id) && !empty($account_id) && !empty($account_holder_name) && !empty($bank_name) && !empty($branch_address) && !empty($ifsc_code) && !empty($account_number) && !empty($account_type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $bank_details = $this->Custom->get_where('bank_details', array('user_id' => $user_id, 'id' => $account_id));
                if (empty($bank_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_account_id');
                    die(json_encode($this->response));
                }
                $updateArr = array(
                    'account_holder_name' => $account_holder_name,
                    'bank_name' => $bank_name,
                    'branch_address' => $branch_address,
                    'ifsc_code' => $ifsc_code,
                    'account_number' => $account_number,
                    'account_type' => $account_type,
                    'micr_code' => $micr_code,
                    'business_address' => $business_address,
                    'registration_date' => $registration_date
                );
                $update_status = $this->Custom->update('bank_details', $updateArr, 'id', $account_id);
                if ($update_status) {
                    $bank_details = $this->Custom->get_where('bank_details', array('id' => $account_id));
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('bank_detail_updated');
                    $this->response->data = $bank_details[0];
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

    public function GetBankDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (isset($user_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $bank_details = $this->Custom->get_where('bank_details', array('user_id' => $user_id));
                if (!empty($bank_details)) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('bank_detail');
                    $this->response->data = $bank_details;
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

    public function UpdateConsultSettings() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : 'basic';
        $offline_consult_status = ($this->input->post('offline_consult_status')) ? $this->input->post('offline_consult_status') : '';
        $offline_consult_charge = ($this->input->post('offline_consult_charge')) ? $this->input->post('offline_consult_charge') : '';
        $offline_consult_time = ($this->input->post('offline_consult_time')) ? $this->input->post('offline_consult_time') : '';
        $online_consult_status = ($this->input->post('online_consult_status')) ? $this->input->post('online_consult_status') : '';
        $online_consult_charge = ($this->input->post('online_consult_charge')) ? $this->input->post('online_consult_charge') : '';
        $online_consult_time = ($this->input->post('online_consult_time')) ? $this->input->post('online_consult_time') : '';
        $invite_consult_status = ($this->input->post('invite_consult_status')) ? $this->input->post('invite_consult_status') : '';
        $invite_consult_charge = ($this->input->post('invite_consult_charge')) ? $this->input->post('invite_consult_charge') : '';
        $invite_consult_time = ($this->input->post('invite_consult_time')) ? $this->input->post('invite_consult_time') : '';
        $enquiry_consult_status = ($this->input->post('enquiry_consult_status')) ? $this->input->post('enquiry_consult_status') : '1';
        $enquiry_consult_charge = ($this->input->post('enquiry_consult_charge')) ? $this->input->post('enquiry_consult_charge') : '';
        $enquiry_consult_time = ($this->input->post('enquiry_consult_time')) ? $this->input->post('enquiry_consult_time') : '';

        if (isset($user_id) && !empty($user_id) && isset($type) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                unset($_POST['lang']);
                switch ($type):
                    case 'basic':
                        $get_details = $this->Custom->get_where('consultation_settings', array('user_id' => $user_id, 'type' => $type));
                        if (!empty($get_details)) {
                            $update_res = $this->Custom->update("consultation_settings", $_POST, 'id', $get_details[0]->id);
                            if ($update_res) {
                                $consult_details = $this->Custom->get_where('consultation_settings', array('id' => $get_details[0]->id));
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('consultation_setting_updated');
                                $this->response->data = $consult_details[0];
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 202;
                                $this->response->message = $this->lang->line('went_wrong');
                                die(json_encode($this->response));
                            }
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

    public function GetConsultSettings() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (isset($user_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $consult_details = $this->Custom->get_where('consultation_settings', array('user_id' => $user_id, 'type' => 'basic'));
                if ($consult_details) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('consultation_settings');
                    $this->response->data = $consult_details[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record_consultation_setting');
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

    public function AddClinicHours() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $day_id = ($this->input->post('day_id')) ? $this->input->post('day_id') : '';
        $working_status = ($this->input->post('working_status')) ? $this->input->post('working_status') : '';
        $start_hour = ($this->input->post('start_hour')) ? $this->input->post('start_hour') : '';
        $end_hour = ($this->input->post('end_hour')) ? $this->input->post('end_hour') : '';
        if (!empty($user_id) && !empty($day_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }

                if ($working_status == 1) {
                    if (empty($start_hour) || empty($end_hour)) {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                }
                $insert_clinic_hours = array(
                    'user_id' => $user_id,
                    'day_id' => $day_id,
                    'start_hour' => $start_hour,
                    'end_hour' => $end_hour,
                    'working_status' => $working_status
                );
                $insert_id = $this->Custom->insert_data('clinic_hours', $insert_clinic_hours);
                if ($insert_id) {
                    $clinic_record = $this->Custom->get_where('clinic_hours', array('id' => $insert_id));
                    $clinic_data = (object) array(
                                'id' => $clinic_record[0]->id,
                                'user_id' => $clinic_record[0]->user_id,
                                'day_id' => $clinic_record[0]->day_id,
                                'start_hour' => $clinic_record[0]->start_hour,
                                'end_hour' => $clinic_record[0]->end_hour
                    );
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('clinic_hour_added');
                    $this->response->data = $clinic_data;
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

    public function UpdateClinicHours() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $id = ($this->input->post('id')) ? $this->input->post('id') : '';
        $working_status = ($this->input->post('working_status')) ? $this->input->post('working_status') : '';
        $start_hour = ($this->input->post('start_hour')) ? $this->input->post('start_hour') : '';
        $end_hour = ($this->input->post('end_hour')) ? $this->input->post('end_hour') : '';
        if (!empty($user_id) && !empty($id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $hour_record = $this->Custom->get_where('clinic_hours', array('id' => $id, 'user_id' => $user_id));
                if ($hour_record) {
                    if ($working_status == 1) {
                        if (empty($start_hour) || empty($end_hour)) {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                    }
                    $update_arr = array(
                        'start_hour' => $start_hour,
                        'end_hour' => $end_hour,
                        'working_status' => $working_status
                    );
                    $update_status = $this->Custom->update_where('clinic_hours', $update_arr, array('user_id' => $user_id, 'id' => $id));

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('clinic_hour_updated');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('user_id_id_error');
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

    public function GetClinicHours() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (isset($user_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $clinic_hours_details = $this->DoctorModel->GetClinicHours($user_id);
                if ($clinic_hours_details) {
                    foreach ($clinic_hours_details as $row) {
                        $day_id[] = $row->day_id;
                    }
                    $day_ids = array_unique($day_id);
                    foreach ($day_ids as $day) {
                        $clinic_data = array();
                        $work_status = 0;
                        $clinic_hours_data = $this->DoctorModel->GetClinicHours($user_id);
                        foreach ($clinic_hours_data as $CH) {
                            if ($day == $CH->day_id) {
                                if ($CH->working_status == 1) {
                                    $work_status = 1;
                                }
                                $clinic_data[] = array(
                                    "id" => $CH->id,
                                    "user_id" => $CH->user_id,
                                    "day_id" => $CH->day_id,
                                    "start_hour" => $CH->start_hour,
                                    "end_hour" => $CH->end_hour
                                );
                            }
                        }
                        $actual_data[] = array(
                            'day_id' => $day,
                            'work_staus' => $work_status,
                            'clinic_data' => $clinic_data
                        );
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('clinic_hours');
                    $this->response->data = $actual_data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record_clinic_hours');
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

    public function DeleteClinicHours() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $clinic_hour_id = ($this->input->post('clinic_hour_id')) ? $this->input->post('clinic_hour_id') : '';
        if (!empty($user_id) && !empty($clinic_hour_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $clinic_hour_record = $this->Custom->get_where('clinic_hours', array('id' => $clinic_hour_id, 'user_id' => $user_id));
                if (empty($clinic_hour_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_clinic_hours');
                    die(json_encode($this->response));
                }
                $del_status = $this->Custom->delete_where('clinic_hours', array('id' => $clinic_hour_id));
                if ($del_status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('clinic_hour_deleted');
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

    public function GetDoctorDocument() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (isset($user_id) && !empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_data = $this->Custom->get_where('doctor_profile', array('user_id' => $user_id));
                if ($doctor_data[0]->medical_registration_proof != '' || $doctor_data[0]->degree_proof != "" || $doctor_data[0]->photo_id_proof != "" || $doctor_data[0]->signature != "") {
                    $medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
                    $degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
                    $photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
                    $signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";

                    $document = array(
                        'id' => $doctor_data[0]->id,
                        'user_id' => $doctor_data[0]->user_id,
                        'medical_registration_proof' => $medical_registration_proof,
                        'degree_proof' => $degree_proof,
                        'photo_id_proof' => $photo_id_proof,
                        'signature' => $signature,
                    );

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('docu_list');
                    $this->response->data = $document;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_document');
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

    public function DeleteDoctorDocument() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $id = ($this->input->post('document_id')) ? $this->input->post('document_id') : '';
        $key_name = ($this->input->post('key_name')) ? $this->input->post('key_name') : '';
        if (!empty($user_id) && !empty($id) && !empty($key_name)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $del_status = $this->DoctorModel->DeleteDocument($id, $key_name);
                if ($del_status) {
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

    public function UpdateEducationDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $edu_id = ($this->input->post('edu_id')) ? $this->input->post('edu_id') : '';
        $degree = ($this->input->post('degree')) ? $this->input->post('degree') : '';
        $university = ($this->input->post('university')) ? $this->input->post('university') : '';
        $graduation_year = ($this->input->post('graduation_year')) ? $this->input->post('graduation_year') : '';
        if (!empty($user_id) && !empty($edu_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $edu_details = $this->Custom->get_where('doctor_education', array('user_id' => $user_id, 'id' => $edu_id));
                if (empty($edu_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_education_id');
                    die(json_encode($this->response));
                }
                $updateArr = array(
                    'degree' => $degree,
                    'university' => $university,
                    'graduation_year' => $graduation_year
                );
                $update_status = $this->Custom->update('doctor_education', $updateArr, 'id', $edu_id);
                if ($update_status) {
                    $edu_details = $this->Custom->get_where('doctor_education', array('id' => $edu_id));
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('education_details_updated');
                    $this->response->data = $edu_details[0];
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

    public function GetAppointments() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $today_date = date('Y-m-d');
                $current_time = date('H:i:s');
                $appointment_record = $this->Custom->query('select * from appointment where doctor_id = "' . $user_id . '"');
                if ($appointment_record) {
                    foreach ($appointment_record as $value) {
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
                }
                switch ($type):
                    case 'request':
                        $appointment_rec = $this->Custom->query('select DISTINCT appointment_date from appointment where doctor_id = "' . $user_id . '" AND status = 0');
                        if (!empty($appointment_rec)) {
                            foreach ($appointment_rec as $value) {
                                if (strtotime($value->appointment_date) >= strtotime($today)) {
                                    $record = array(
                                        'booking_date' => $value->appointment_date
                                    );
                                    $appointmentData[] = $record;
                                }
                            }

                            if (!empty($appointmentData))
                                $appointmentArr = $appointmentData;

                            if (!empty($appointmentArr)) {
                                foreach ($appointmentArr as $AP)
                                    $appointment[] = $AP;
                            } else
                                $appointment = array();
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('appointment_list');
                            $this->response->data = $appointment;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_appointment');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'confirm':
                        $appointment_record = $this->Custom->query('select DISTINCT appointment_date from appointment where doctor_id = "' . $user_id . '" AND (status = 1 OR status = 4 OR status = 5 OR status = 6)');
                        if (!empty($appointment_record)) {
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
                            }
                        }

                        $appointment_rec = $this->Custom->query('select DISTINCT appointment_date from appointment where doctor_id = "' . $user_id . '" AND (status = 1 OR status = 4 OR status = 5 OR status = 6)');
                        if (!empty($appointment_rec)) {
                            foreach ($appointment_rec as $value) {
                                if (strtotime($value->appointment_date) >= strtotime($today)) {
                                    $record = array(
                                        'booking_date' => $value->appointment_date
                                    );
                                    $appointment[] = $record;
                                }
                            }

                            if ($appointment) {
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('appointment_list');
                                $this->response->data = $appointment;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record_appointment');
                                die(json_encode($this->response));
                            }
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_appointment');
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

    public function GetAppointmentByType() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $date = ($this->input->post('date')) ? $this->input->post('date') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $timezone = ($this->input->post('timezone')) ? $this->input->post('timezone') : '';
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if (!empty($timezone))
                    $this->Custom->update_where('users', array('timezone' => $timezone), array('id' => $user_id));
                //close previous appointment date
                $today_date = date('Y-m-d');
                $current_time = date('H:i');
                $appointment_record = $this->Custom->query('select * from appointment where doctor_id = "' . $user_id . '"');
                if ($appointment_record) {
                    foreach ($appointment_record as $value) {
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
                }
                //get appointments
                if ($type == 'request') {
                    $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = $user_id AND status = 0 order by start_time ASC");
                }
                if ($type == 'confirm') {
                    $appointment_record = $this->Custom->query('select DISTINCT appointment_date from appointment where doctor_id = "' . $user_id . '" AND (status = 1 OR status = 4 OR status = 5)');
                    if (!empty($appointment_record)) {
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
                        }
                    }
                    if (!empty($date)) {
                        $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = $user_id AND appointment_date = '$date' AND (status = 1 OR status = 4 OR status = 5) order by start_time ASC");
                    } else {
                        $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = $user_id AND (status = 1 OR status = 4) order by start_time ASC");
                    }
                }
                if ($type == 'history') {
                    $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = $user_id AND (status = 2 OR status = 3 OR status = 5 OR status = 6 OR status = 7 OR status = 9) order BY id DESC");
                }
                if ($type == '') {
                    if (empty($date)) {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                    $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = $user_id AND appointment_date = '$date' AND (status = 0 OR status = 1 OR status = 4 OR status = 5 OR status = 6)");
                }
                if (!empty($appointment_rec)) {
                    foreach ($appointment_rec as $AR) {
                        $patients_record = $this->DoctorModel->GetPatientProfile($AR->user_id);
                        if ($patients_record) {
                            $patients_record[0]->profile_image = ($patients_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patients_record[0]->profile_image : "";
                        }
                        $AR->created_by = 'patient';
                        $AR->patient = array(
                            'user_id' => $patients_record[0]->user_id,
                            'name' => $patients_record[0]->name,
                            'email' => $patients_record[0]->email,
                            'phone_number' => $patients_record[0]->phone_number,
                            'dob' => $patients_record[0]->dob,
                            'gender' => $patients_record[0]->gender,
                            'profile_image' => $patients_record[0]->profile_image
                        );
                        if ($AR->user_type == 'patient_member') {
                            $patient_member = $this->Custom->get_where('patient_member', array('id' => $AR->member_id));
                            $additional_record = $this->Custom->get_where('member_additional_info', array('member_id' => $AR->member_id));
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
                            $AR->member = (object) array(
                                        'member_id' => $patient_member[0]->id,
                                        'name' => $patient_member[0]->name,
                                        'relationship' => $patient_member[0]->relationship,
                                        'dob' => $patient_member[0]->dob,
                                        'gender' => $patient_member[0]->gender,
                                        'height' => $patient_member[0]->height,
                                        'weight' => $patient_member[0]->weight,
                                        'city' => $patient_member[0]->city,
                                        'locality' => $patient_member[0]->locality,
                                        'gender' => $patient_member[0]->gender,
                                        'additional_info' => $add_infoArr
                            );
                        } else {
                            $AR->member = new stdClass();
                        }
                        //get unread message count
                        $unread_message_count = 0;
                        $chat_message_rec = $this->Custom->query("select * from chat_message where appointment_id = '" . $AR->id . "' AND receiver_id = '" . $user_id . "' AND message_status = 0");
                        if (!empty($chat_message_rec)) {
                            $unread_message_count = count($chat_message_rec);
                        } else {
                            $unread_message_count = 0;
                        }

                        $AR->unread_message_count = $unread_message_count;
                        $appointment[] = $AR;
                    }
                    if (!empty($appointment)) {
                        foreach ($appointment as $row) {
                            $AppintmentDoc = array();
                            if ($row->appointment_doc != '') {
                                $appointment_doc = explode(',', $row->appointment_doc);
                                foreach ($appointment_doc as $doc) {
                                    $AppintmentDoc[] = base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $doc;
                                }
                            }
                            $row->AppintmentDoc = $AppintmentDoc;
                        }
                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('appointment_list');
                        $this->response->data = $appointment;
                        die(json_encode($this->response));
                    } else {
                        $this->response->success = 205;
                        $this->response->message = $this->lang->line('no_record_appointment');
                        die(json_encode($this->response));
                    }
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

    public function GetPublicQuestion() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case 'all':
                        $question_record = $this->Custom->query("select * from questions where doctor_id = 0 ORDER BY id ASC LIMIT $offset, " . $per_page);
                        if ($question_record) {
                            foreach ($question_record as $value) {
                                $answer_record = $this->Custom->get_where('answers', array('question_id' => $value->id));
                                $patient_record = $this->DoctorModel->GetPatientProfile($value->user_id);
                                if ($patient_record) {
                                    $value->name = $patient_record[0]->name;
                                    $value->gender = $patient_record[0]->gender;
                                    $value->profile_image = ($patient_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . "/" . $patient_record[0]->profile_image : "";
                                } else {
                                    $value->name = "";
                                    $value->gender = "";
                                    $value->profile_image = "";
                                }
                                $value->timestamp = $value->created_at;
                                $value->answer_status = ($answer_record) ? 1 : 0;
                                $value->answer = ($answer_record) ? $answer_record[0]->reply : "";
                            }
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('public_question');
                            $this->response->data = $question_record;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'answer':
                        $question_record = $this->Custom->query("select * from questions where doctor_id = 0 ORDER BY id ASC LIMIT $offset, " . $per_page);
                        if ($question_record) {
                            foreach ($question_record as $value) {
                                $answer_record = $this->Custom->get_where('answers', array('question_id' => $value->id));
                                if (!empty($answer_record)) {
                                    $patient_record = $this->DoctorModel->GetPatientProfile($value->user_id);
                                    if ($patient_record) {
                                        $value->name = $patient_record[0]->name;
                                        $value->gender = $patient_record[0]->gender;
                                        $value->profile_image = ($patient_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . "/" . $patient_record[0]->profile_image : "";
                                    } else {
                                        $value->name = "";
                                        $value->gender = "";
                                        $value->profile_image = "";
                                    }
                                    $value->timestamp = $value->created_at;
                                    $value->answer_status = 1;
                                    $value->answer = ($answer_record) ? $answer_record[0]->reply : "";
                                    $ques_re[] = $value;
                                }
                            }
                            if ($ques_re) {
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('public_question');
                                $this->response->data = $ques_re;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record');
                                die(json_encode($this->response));
                            }
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'unanswer':
                        $question_record = $this->Custom->query("select * from questions where doctor_id = 0 ORDER BY id ASC LIMIT $offset, " . $per_page);
                        if ($question_record) {
                            foreach ($question_record as $value) {
                                $answer_record = $this->Custom->get_where('answers', array('question_id' => $value->id));
                                if (empty($answer_record)) {
                                    $patient_record = $this->DoctorModel->GetPatientProfile($value->user_id);
                                    if ($patient_record) {
                                        $value->name = $patient_record[0]->name;
                                        $value->gender = $patient_record[0]->gender;
                                        $value->profile_image = ($patient_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . "/" . $patient_record[0]->profile_image : "";
                                    } else {
                                        $value->name = "";
                                        $value->gender = "";
                                        $value->profile_image = "";
                                    }
                                    $value->timestamp = $value->created_at;
                                    $value->answer_status = 0;
                                    $value->answer = ($answer_record) ? $answer_record[0]->reply : "";
                                    $ques_re[] = $value;
                                }
                            }
                            if ($ques_re) {
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('public_question');
                                $this->response->data = $ques_re;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record');
                                die(json_encode($this->response));
                            }
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

    public function GetPrivateQuestion() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case 'all':
                        $question_record = $this->Custom->query("select * from questions where doctor_id = '" . $user_id . "' ORDER BY id ASC LIMIT $offset, " . $per_page);
                        if ($question_record) {
                            foreach ($question_record as $value) {
                                $answer_record = $this->Custom->get_where('answers', array('question_id' => $value->id));
                                $patient_record = $this->DoctorModel->GetPatientProfile($value->user_id);
                                if ($patient_record) {
                                    $value->name = $patient_record[0]->name;
                                    $value->gender = $patient_record[0]->gender;
                                    $value->profile_image = ($patient_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . "/" . $patient_record[0]->profile_image : "";
                                } else {
                                    $value->name = "";
                                    $value->gender = "";
                                    $value->profile_image = "";
                                }
                                $value->timestamp = $value->created_at;
                                $value->answer_status = ($answer_record) ? 1 : 0;
                                $value->answer = ($answer_record) ? $answer_record[0]->reply : "";
                            }
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('private_question');
                            $this->response->data = $question_record;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'answer':
                        $question_record = $this->Custom->query("select * from questions where doctor_id = '" . $user_id . "' ORDER BY id ASC LIMIT $offset, " . $per_page);
                        if ($question_record) {
                            foreach ($question_record as $value) {
                                $answer_record = $this->Custom->get_where('answers', array('question_id' => $value->id));
                                if (!empty($answer_record)) {
                                    $patient_record = $this->DoctorModel->GetPatientProfile($value->user_id);
                                    if ($patient_record) {
                                        $value->name = $patient_record[0]->name;
                                        $value->gender = $patient_record[0]->gender;
                                        $value->profile_image = ($patient_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . "/" . $patient_record[0]->profile_image : "";
                                    } else {
                                        $value->name = "";
                                        $value->gender = "";
                                        $value->profile_image = "";
                                    }
                                    $value->timestamp = $value->created_at;
                                    $value->answer_status = 1;
                                    $value->answer = ($answer_record) ? $answer_record[0]->reply : "";
                                    $ques_re[] = $value;
                                }
                            }
                            if ($ques_re) {
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('private_question');
                                $this->response->data = $ques_re;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record');
                                die(json_encode($this->response));
                            }
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record');
                            die(json_encode($this->response));
                        }
                        break;

                    case 'unanswer':
                        $question_record = $this->Custom->query("select * from questions where doctor_id = '" . $user_id . "' ORDER BY id ASC LIMIT $offset, " . $per_page);
                        if ($question_record) {
                            foreach ($question_record as $value) {
                                $answer_record = $this->Custom->get_where('answers', array('question_id' => $value->id));
                                if (empty($answer_record)) {
                                    $patient_record = $this->DoctorModel->GetPatientProfile($value->user_id);
                                    if ($patient_record) {
                                        $value->name = $patient_record[0]->name;
                                        $value->gender = $patient_record[0]->gender;
                                        $value->profile_image = ($patient_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . "/" . $patient_record[0]->profile_image : "";
                                    } else {
                                        $value->name = "";
                                        $value->gender = "";
                                        $value->profile_image = "";
                                    }
                                    $value->timestamp = $value->created_at;
                                    $value->answer_status = 0;
                                    $value->answer = ($answer_record) ? $answer_record[0]->reply : "";
                                    $ques_re[] = $value;
                                }
                            }
                            if ($ques_re) {
                                $this->response->success = 200;
                                $this->response->message = $this->lang->line('private_question');
                                $this->response->data = $ques_re;
                                die(json_encode($this->response));
                            } else {
                                $this->response->success = 205;
                                $this->response->message = $this->lang->line('no_record');
                                die(json_encode($this->response));
                            }
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

    public function PostAnswer() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $question_id = ($this->input->post('question_id')) ? $this->input->post('question_id') : '';
        $answer = ($this->input->post('answer')) ? $this->input->post('answer') : '';

        if (!empty($user_id) && !empty($answer) && !empty($question_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $questions_record = $this->Custom->get_where('questions', array('id' => $question_id));
                if (empty($questions_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_question');
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'question_id' => $question_id,
                    'reply' => $answer,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('answers', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('answer_posted');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
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
        $status = ($this->input->post('status')) ? $this->input->post('status') : '';

        if (!empty($user_id) && !empty($appointment_id) && !empty($status)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }

                switch ($status):
                    case 1:
                        $appointment_record = $this->Custom->get_where('appointment', array('id' => $appointment_id, 'doctor_id' => $user_id, 'status' => 0));
                        if (empty($appointment_record)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('invalid_appointment');
                            die(json_encode($this->response));
                        }
                        $all_appointment = $this->Custom->get_where('appointment', array('doctor_id' => $user_id, 'appointment_date' => $appointment_record[0]->appointment_date, 'start_time' => $appointment_record[0]->start_time, 'end_time' => $appointment_record[0]->end_time, 'status' => 1));
                        if (!empty($all_appointment)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('already_appointment_on_time');
                            die(json_encode($this->response));
                        }
                        $update_status = $this->Custom->update('appointment', array('status' => $status, 'doctor_accepted_date' => strtotime(date("Y-m-d H:i:s"))), 'id', $appointment_id);
                        if ($update_status) {
                            $this->PatientNotification('appointment', $user_id, $appointment_record[0]->user_id, 'doctor', $appointment_id, $appointment_record[0]->appointment_type, 1, '');
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('appointment_confirmed');
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;

                    case 2:
                        $appointment_record = $this->Custom->get_where('appointment', array('id' => $appointment_id, 'doctor_id' => $user_id, 'status' => 0));
                        if (empty($appointment_record)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('invalid_appointment');
                            die(json_encode($this->response));
                        }
                        $update_status = $this->Custom->update('appointment', array('status' => $status), 'id', $appointment_id);
                        if ($update_status) {
                            $this->PatientNotification('appointment', $user_id, $appointment_record[0]->user_id, 'doctor', $appointment_id, $appointment_record[0]->appointment_type, 2, '');
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('appointment_cancelled');
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 202;
                            $this->response->message = $this->lang->line('went_wrong');
                            die(json_encode($this->response));
                        }
                        break;

                    case "satisfy":
                        $appointment_record = $this->Custom->get_where('appointment', array('id' => $appointment_id, 'doctor_id' => $user_id));
                        if (empty($appointment_record)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('invalid_appointment');
                            die(json_encode($this->response));
                        }
                        $update_status = $this->Custom->update_where('appointment', array('status' => 5, 'chat_status' => 2), array('id' => $appointment_id));
                        if ($update_status) {
                            $this->PatientNotification('appointment', $user_id, $appointment_record[0]->user_id, 'doctor', $appointment_id, $appointment_record[0]->appointment_type, 5, '');

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
                $this->response->message = $this->lang->line('doctor_id_error');
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treatment_details = $this->Custom->get_where('treatment');
                if (!empty($treatment_details)) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('treatment_list');
                    $this->response->data = $treatment_details;
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

    public function AddTreatment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $treaments = ($this->input->post('treatments')) ? $this->input->post('treatments') : '';
        if (!empty($user_id) && !empty($treaments)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treamentsArr = explode(',', $treaments);
                foreach ($treamentsArr as $row) {
                    $treatment_details = $this->Custom->get_where('treatment', array('id' => $row));
                    if (empty($treatment_details)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('invalid_treatment');
                        die(json_encode($this->response));
                    }
                }
                foreach ($treamentsArr as $val) {
                    $treatment_details = $this->Custom->get_where('doctor_treatment', array('doctor_id' => $user_id));
                    foreach ($treatment_details as $TD) {
                        $TDArr = explode(',', $TD->treatments);
                        if (in_array($val, $TDArr)) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('treatment_exist');
                            die(json_encode($this->response));
                        }
                    }
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'treatments' => $treaments,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('doctor_treatment', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('treatment_added');
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

    public function GetDoctorTreatment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treatment_details = $this->Custom->get_where('doctor_treatment', array('doctor_id' => $user_id));
                if (!empty($treatment_details)) {
                    foreach ($treatment_details as $row) {
                        $treatments_data = array();
                        $Arr = explode(',', $row->treatments);
                        foreach ($Arr as $val) {
                            $treatment_data = $this->Custom->get_where('treatment', array('id' => $val));
                            if ($treatment_data) {
                                $treatmentsData = array(
                                    'treatment_id' => $val,
                                    'treatment_name' => $treatment_data[0]->name
                                );
                            } else {
                                $treatmentsData = array(
                                    'treatment_id' => $val,
                                    'treatment_name' => ""
                                );
                            }
                            $treatments_data[] = $treatmentsData;
                        }
                        $row->treatments = $treatments_data;
                        $all_data[] = $row;
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('treatment_list');
                    $this->response->data = $all_data;
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

    public function GetEnquiryHistory() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treatment_enquiry = $this->Custom->query("select * from treatment_enquiry where doctor_id = '" . $user_id . "' ORDER BY id DESC LIMIT $offset, " . $per_page);
                if (!empty($treatment_enquiry)) {
                    foreach ($treatment_enquiry as $row) {
                        $conversaton_details = $this->Custom->query("select enquiry_conversations.id as conversation_id,enquiry_messages.* from enquiry_conversations INNER JOIN enquiry_messages ON enquiry_conversations.id = enquiry_messages.conversation_id where enquiry_conversations.enquiry_id = $row->id ORDER BY enquiry_messages.id DESC");
                        if (!empty($conversaton_details)) {
                            if ($conversaton_details[0]->sender_type == 'patient') {
                                $this->load->model('PatientModel');
                                $patient_id = $conversaton_details[0]->sender_id;
                                $patient_data = $this->PatientModel->GetPatientProfile($conversaton_details[0]->sender_id);
                                if ($patient_data) {
                                    $sender_name = $patient_data[0]->name;
                                    $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                                } else {
                                    $sender_name = "";
                                    $sender_profile_image = "";
                                }
                            }
                            if ($conversaton_details[0]->sender_type == 'doctor') {
                                $this->load->model('PatientModel');
                                $patient_id = $conversaton_details[0]->receiver_id;
                                $patient_data = $this->PatientModel->GetPatientProfile($conversaton_details[0]->receiver_id);
                                if ($patient_data) {
                                    $sender_name = $patient_data[0]->name;
                                    $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                                } else {
                                    $sender_name = "";
                                    $sender_profile_image = "";
                                }
                            }
                            if ($conversaton_details[0]->message_type != 'text')
                                $conversaton_details[0]->message = ($conversaton_details[0]->message) ? base_url() . ENQUIRY_MESSAGE_URL . '/' . $conversaton_details[0]->message : "";
                            $all_data[] = array(
                                'enquiry_id' => $row->id,
                                'sender_id' => $conversaton_details[0]->sender_id,
                                'sender_name' => $sender_name,
                                'sender_profile_image' => $sender_profile_image,
                                'receiver_id' => $conversaton_details[0]->receiver_id,
                                'sender_type' => $conversaton_details[0]->sender_type,
                                'message' => $conversaton_details[0]->message,
                                'message_type' => $conversaton_details[0]->message_type,
                                'sent_at' => $conversaton_details[0]->sent_at,
                                'timestamp' => $conversaton_details[0]->sent_at,
                                'patient_id' => $patient_id
                            );
                        } else {
                            $this->load->model('PatientModel');
                            $patient_data = $this->PatientModel->GetPatientProfile($row->patient_id);
                            if ($patient_data) {
                                $sender_name = $patient_data[0]->name;
                                $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                            } else {
                                $sender_name = "";
                                $sender_profile_image = "";
                            }
                            $all_data[] = array(
                                'enquiry_id' => $row->id,
                                'sender_id' => $row->patient_id,
                                'sender_name' => $sender_name,
                                'sender_profile_image' => $sender_profile_image,
                                'receiver_id' => $row->doctor_id,
                                'sender_type' => 'patient',
                                'message' => $row->query_details,
                                'message_type' => 'text',
                                'sent_at' => $row->created_at,
                                'timestamp' => $row->created_at,
                                'patient_id' => $row->patient_id
                            );
                        }
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('treatment_enquiry_history');
                    $this->response->data = $all_data;
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $treatment_enquiry = $this->Custom->get_where('treatment_enquiry', array('doctor_id' => $user_id, 'id' => $enquiry_id));
                if (!empty($treatment_enquiry)) {
                    $this->load->model('PatientModel');
                    $patient_data = $this->PatientModel->GetPatientProfile($treatment_enquiry[0]->patient_id);
                    if ($patient_data) {
                        $sender_name = $patient_data[0]->name;
                        $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                    } else {
                        $sender_name = "";
                        $sender_profile_image = "";
                    }
                    $doctor_data = $this->DoctorModel->GetDoctorProfile($treatment_enquiry[0]->doctor_id);
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
                        //'sent_at' => $treatment_enquiry[0]->created_at,
                        'timestamp' => $treatment_enquiry[0]->created_at,
                        'patient_id' => $treatment_enquiry[0]->patient_id
                    );
                    $conversaton_details = $this->Custom->query("select enquiry_conversations.id as conversation_id,enquiry_messages.* from enquiry_conversations INNER JOIN enquiry_messages ON enquiry_conversations.id = enquiry_messages.conversation_id where enquiry_conversations.enquiry_id = '" . $enquiry_id . "' ORDER BY enquiry_messages.id ASC");
                    if (!empty($conversaton_details)) {
                        foreach ($conversaton_details as $CD) {
                            $sender_name = "";
                            $sender_profile_image = "";
                            $receiver_name = "";
                            $receiver_profile_image = "";
                            if ($CD->sender_type == 'patient') {
                                $patient_id = $CD->sender_id;
                                $patient_data = $this->PatientModel->GetPatientProfile($CD->sender_id);
                                if ($patient_data) {
                                    $sender_name = $patient_data[0]->name;
                                    $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                                } else {
                                    $sender_name = "";
                                    $sender_profile_image = "";
                                }
                                $doctor_data = $this->DoctorModel->GetDoctorProfile($CD->receiver_id);
                                if ($doctor_data) {
                                    $receiver_name = $doctor_data[0]->title . " " . $doctor_data[0]->name;
                                    $receiver_profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
                                } else {
                                    $receiver_name = "";
                                    $receiver_profile_image = "";
                                }
                            }
                            if ($CD->sender_type == 'doctor') {
                                $patient_id = $CD->receiver_id;
                                $doctor_data = $this->DoctorModel->GetDoctorProfile($CD->sender_id);
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
                                //'sent_at' => $CD->sent_at,
                                'timestamp' => $CD->sent_at,
                                'patient_id' => $patient_id
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

    public function SendEnquiryMessage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $enquiry_id = ($this->input->post('enquiry_id')) ? $this->input->post('enquiry_id') : '';
        $message_type = ($this->input->post('message_type')) ? $this->input->post('message_type') : '';
        if (!empty($user_id) && !empty($patient_id) && !empty($enquiry_id) && !empty($message_type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('users', array('id' => $patient_id, 'role' => 'patient'));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                $treatment_enquiry = $this->Custom->get_where('treatment_enquiry', array('doctor_id' => $user_id, 'id' => $enquiry_id));
                if (empty($treatment_enquiry)) {
                    $this->response->success = 203;
                    $this->response->message = "Enquiry id is not valid";
                    die(json_encode($this->response));
                }
                $enquiry_conversations_record = $this->Custom->query('select * from enquiry_conversations where user_id = "' . $patient_id . '" AND doctor_id = "' . $user_id . '" AND enquiry_id = "' . $enquiry_id . '"');
                if (empty($enquiry_conversations_record)) {
                    $insertArr = array(
                        'enquiry_id' => $enquiry_id,
                        'user_id' => $patient_id,
                        'doctor_id' => $user_id,
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
                    'receiver_id' => $patient_id,
                    'sender_type' => 'doctor',
                    'message' => $message,
                    'message_type' => $message_type,
                    'sent_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('enquiry_messages', $insertMessageArr);
                if ($insert_id) {
                    //get message
                    $enquiry_messages = $this->Custom->get_where('enquiry_messages', array('id' => $insert_id));
                    if ($enquiry_messages[0]->message_type == 'image')
                        $enquiry_messages[0]->message = ($enquiry_messages[0]->message) ? base_url() . ENQUIRY_MESSAGE_URL . '/' . $enquiry_messages[0]->message : "";

                    if ($enquiry_messages[0]->sender_type == 'doctor') {
                        $this->load->model('PatientModel');
                        $patient_id = $enquiry_messages[0]->receiver_id;
                        $patient_data = $this->PatientModel->GetPatientProfile($enquiry_messages[0]->receiver_id);
                        if ($patient_data) {
                            $sender_name = $patient_data[0]->name;
                            $sender_profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
                        } else {
                            $sender_name = "";
                            $sender_profile_image = "";
                        }
                    }

                    $ret_data = (object) array(
                                'enquiry_id' => $enquiry_messages[0]->id,
                                'sender_id' => $enquiry_messages[0]->sender_id,
                                'sender_name' => $sender_name,
                                'sender_profile_image' => $sender_profile_image,
                                'receiver_id' => $enquiry_messages[0]->receiver_id,
                                'sender_type' => $enquiry_messages[0]->sender_type,
                                'message' => $enquiry_messages[0]->message,
                                'message_type' => $enquiry_messages[0]->message_type,
                                'sent_at' => $enquiry_messages[0]->sent_at,
                                'timestamp' => $enquiry_messages[0]->sent_at,
                                'patient_id' => $patient_id
                    );
                    //send notification
                    define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
                    $receiver_data = $this->Custom->get_where('users', array('id' => $patient_id));
                    $message = $this->lang->line('enquiry_new_message');
                    if (isset($receiver_data) && !empty($receiver_data)) {
                        if ($receiver_data[0]->device_type == "android") {
                            if (!empty($receiver_data[0]->device_token)) {
                                $registatoin_ids = array($receiver_data[0]->device_token);

                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $fields = array(
                                    'registration_ids' => $registatoin_ids,
                                    'data' => array("message" => $message, 'notification_type' => 'enquiry', 'type' => 'enquiry', 'id' => $enquiry_id, 'sender_id' => $user_id, 'sender_name' => $sender_name),
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
                                    'body' => $message,
                                ),
                                'badge' => 1,
                                'notification_type' => 'enquiry',
                                'type' => 'enquiry',
                                'id' => $enquiry_id,
                                'sender_id' => $user_id,
                                'sender_name' => $sender_name,
                                'sound' => 'default',
                            );
                            $passphrase = '';
                            $ctx = stream_context_create();
                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientProduction.pem');
                            } else {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientDevelopment.pem');
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
                    $this->response->data = $ret_data;
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

    public function PatientNotification($notification_type, $sender_id, $receiver_id, $sender_type, $action_id, $type, $status, $token) {
        define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
        $created = strtotime(date("Y-m-d H:i:s"));
        $sender_data = $this->Custom->get_where('users', array('id' => $sender_id));
        $receiver_data = $this->Custom->get_where('users', array('id' => $receiver_id));
        $_POST['lang'] = (isset($_POST['lang']) && !empty($_POST['lang'])) ? $_POST['lang'] : '';
        $message_ru = '';
        if ($notification_type == 'appointment') {
            $appointment_data = $this->Custom->get_where('appointment', array('id' => $action_id));
            if ($status == 1) {
                //$message = $this->lang->line('appointment_accepted');
                $message = $this->CreateMessageForNotification('english', 'appointment_accepted');
                $message_ru = $this->CreateMessageForNotification('russian', 'appointment_accepted');
            }

            if ($status == 2) {
                //$message = $this->lang->line('appointment_rejected');
                $message = $this->CreateMessageForNotification('english', 'appointment_rejected');
                $message_ru = $this->CreateMessageForNotification('russian', 'appointment_rejected');
            }

            if ($status == 5) {
                // $message = $sender_data[0]->name . " " . $this->lang->line('satisfy_with_consultation');
                $message = $sender_data[0]->name . " " . $this->CreateMessageForNotification('english', 'satisfy_with_consultation');
                $message_ru = $sender_data[0]->name . " " . $this->CreateMessageForNotification('russian', 'satisfy_with_consultation');
            }

            if ($status == 6) {
                // $message = $sender_data[0]->name . " " . $this->lang->line('satisfy_with_consultation');
                $message = $this->CreateMessageForNotification('english', 'doctor_send_recommendation');
                $message_ru = $this->CreateMessageForNotification('russian', 'doctor_send_recommendation');
            }

            if ($status == 4) {
                if ($type == 2) {
                    //$message = $this->lang->line('receive_video_call') . " " . $sender_data[0]->name . ".";
                    $message = $this->CreateMessageForNotification('english', 'appointment_rejected') . " " . $sender_data[0]->name . ".";
                    $message_ru = $this->CreateMessageForNotification('russian', 'appointment_rejected') . " " . $sender_data[0]->name . ".";
                }
                if ($type == 1) {
                    // $message = $this->lang->line('receive_audio_call') . " " . $sender_data[0]->name . ".";
                    $message = $this->CreateMessageForNotification('english', 'appointment_rejected') . " " . $sender_data[0]->name . ".";
                    $message_ru = $this->CreateMessageForNotification('russian', 'appointment_rejected') . " " . $sender_data[0]->name . ".";
                }
                if ($type == 3) {
                    $message = "vdvd";
                    $message_ru = "vdvd";
                }
            }

            if ($status == '') {
                //$message = $this->lang->line('call_appointment_rejected') . " " . $sender_data[0]->name . ".";
                $message = $this->CreateMessageForNotification('english', 'call_appointment_rejected') . " " . $sender_data[0]->name . ".";
                $message_ru = $this->CreateMessageForNotification('russian', 'call_appointment_rejected') . " " . $sender_data[0]->name . ".";
            }

            $doctor_name = $sender_data[0]->title . " " . $sender_data[0]->name;
            $room_name = ($appointment_data) ? $appointment_data[0]->chat_room_name : "";

            $send_msg = array("message" => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '' )) ? $message : $message_ru, 'notification_type' => $notification_type, 'type' => $type, 'user_id' => $receiver_id, 'token' => $token, 'doctor_id' => $sender_id, 'doctor_name' => $doctor_name, 'room_name' => $room_name, 'appointment_id' => $action_id, 'status' => $status);
        }

        if (isset($receiver_data) && !empty($receiver_data)) {
            if ($receiver_data[0]->device_type == "android") {
                if (!empty($receiver_data[0]->device_token)) {
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
                    if ($res_array->success == 1) {
                        if (($status == 1 || $status == 2 || $status == 6) && $notification_type == 'appointment') {
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
            } else {
                $app_state = $this->app_state;
                //$app_state = "";
                $deviceToken = $receiver_data[0]->device_token;

                if ($notification_type == 'appointment') {

                    if ($status == 4 && ($type == 1 || $type == 2 || $type == 3)) {
                        $deviceToken = $receiver_data[0]->voip_device_token;
                    } else {
                        $deviceToken = $receiver_data[0]->device_token;
                    }

                    $body['aps'] = array(
                        'alert' => array(
                            //'title' => "You have a notification",
                            'body' => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '' )) ? $message : $message_ru,
                        ),
                        'badge' => 1,
                        'status' => $status,
                        'notification_type' => $notification_type,
                        'user_id' => $receiver_id,
                        'doctor_name' => $doctor_name,
                        'room_name' => $room_name,
                        'appointment_id' => $action_id,
                        'doctor_id' => $sender_id,
                        'type' => $type,
                        'token' => $token,
                        'sound' => 'default',
                    );

                    $passphrase = '';

                    $ctx = stream_context_create();

                    if ($status == 4 && ($type == 1 || $type == 2 || $type == 3)) {
                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientVideo.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/Patientapns-dev-cert.pem');
                        }
                    } else {
                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientProduction.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientDevelopment.pem');
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

                if (($status == 1 || $status == 2 || $status == 6) && $notification_type == 'appointment') {
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

    public function GenerateChatToken() {
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $new_token = "";
        if (!empty($appointment_id)) {
            $appointment_data = $this->Custom->get_where('appointment', array('id' => $appointment_id));
            $doctor_data = $this->Custom->get_where('users', array('id' => $appointment_data[0]->doctor_id, 'role' => 'doctor'));
            $doctor_data[0]->name = str_replace(" ", "-", $doctor_data[0]->name);
            $doctor_identity = $doctor_data[0]->name . "-" . $appointment_data[0]->doctor_id . "-" . $appointment_id;
            $token = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $doctor_identity);

            $chatGrant = new Twilio\Jwt\Grants\ChatGrant();
            $chatGrant->setServiceSid($this->serviceSid);

            $token->addGrant($chatGrant);
            $new_token = $token->toJWT();

            $user_data = $this->Custom->get_where('users', array('id' => $appointment_data[0]->user_id, 'role' => 'patient'));
            $user_data[0]->name = str_replace(" ", "-", $user_data[0]->name);
            $patient_identity = $user_data[0]->name . "-" . $appointment_data[0]->user_id . "-" . $appointment_id;

            $data = array('token' => $new_token, 'patient_identity' => $patient_identity);
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }

                $identity = $name;
                $token = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $identity);
                $voiceGrant = new Twilio\Jwt\Grants\VoiceGrant();
                $voiceGrant->setOutgoingApplicationSid($this->outgoingApplicationSid);
                if ($user_record[0]->device_type == 'ios')
                    $voiceGrant->setPushCredentialSid($this->DoctoriOSPushCredentialSid);
                if ($user_record[0]->device_type == 'android')
                    $voiceGrant->setPushCredentialSid($this->DoctorAndriodPushCredentialSid);

                $token->addGrant($voiceGrant);
                $new_token = $token->toJWT();
                $timestamp = strtotime(date('y-m-d H:i:s'));

                $data = array('token' => $new_token, 'patient_identity' => '', 'timestamp' => $timestamp);
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
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
                    $this->response->message = $this->lang->line('payment_error');
                    die(json_encode($this->response));
                }
                if ($appointment_data[0]->doctor_id != $user_id) {
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
                //patient token
                $user_data = $this->Custom->get_where('users', array('id' => $appointment_data[0]->user_id, 'role' => 'patient'));
                $user_data[0]->name = str_replace(" ", "-", $user_data[0]->name);
                $patient_identity = $user_data[0]->name . "-" . $appointment_data[0]->user_id . "-" . $appointment_id;
                $roomName1 = $appointment_data[0]->chat_room_name;
                $token1 = new Twilio\Jwt\AccessToken($this->twilioAccountSid, $this->twilioApiKey, $this->twilioApiSecret, 3600 * 24, $patient_identity);

                $videoGrant1 = new Twilio\Jwt\Grants\VideoGrant();
                $videoGrant1->setRoom($roomName1);

                $token1->addGrant($videoGrant1);
                $patient_token = $token1->toJWT();

                $data = array('token' => $new_token, 'roomName' => $roomName);
                if (!empty($new_token)) {
                    if ($type == 'call') {
                        $this->PatientNotification('appointment', $user_id, $appointment_data[0]->user_id, 'doctor', $appointment_id, $appointment_data[0]->appointment_type, 4, $patient_token);
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('token_generated');
                    $this->response->data = $data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    $this->response->data = array();
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

    public function SendNotificationOnCallReject() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_record = $this->Custom->get_where('appointment', array('id' => $appointment_id, 'doctor_id' => $user_id));
                if (empty($appointment_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                $this->PatientNotification('appointment', $user_id, $appointment_record[0]->user_id, 'doctor', $appointment_id, $appointment_record[0]->appointment_type, '', '');

                $this->response->success = 200;
                $this->response->message = $this->lang->line('patient_notified');
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

    public function AddFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_category_id = ($this->input->post('feed_category_id')) ? $this->input->post('feed_category_id') : '';
        $subject = ($this->input->post('subject')) ? $this->input->post('subject') : '';
        $description = ($this->input->post('description')) ? $this->input->post('description') : '';
        $width = ($this->input->post('width')) ? $this->input->post('width') : '';
        $height = ($this->input->post('height')) ? $this->input->post('height') : '';
        if (!empty($user_id) && !empty($feed_category_id) && !empty($subject) && !empty($description) && !empty($width) && !empty($height)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $feed_category = $this->Custom->get_where('feed_category', array('id' => $feed_category_id));
                if (empty($feed_category)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_feed_id');
                    die(json_encode($this->response));
                }
                if (!empty($_FILES['image']['name'])) {
                    $name = $_FILES['image']['name'];
                    $get_ext = explode(".", $name);
                    $ext = end($get_ext);
                    $new_name = time() . '.' . $ext;
                    $config['file_name'] = $new_name;
                    $config['upload_path'] = DOCTOR_FEED_PATH . '/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $config['remove_spaces'] = TRUE;

                    if (!is_dir($config['upload_path']))
                        die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('image')) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('image_not_upload');
                        die(json_encode($this->response));
                    } else {
                        $_POST['image'] = $new_name;
                    }
                } else {
                    $_POST['image'] = "";
                }
                unset($_POST['lang']);
                $_POST['created_at'] = strtotime(date("Y-m-d H:i:s"));
                $feed_record = $this->Custom->get_where('doctor_feed', array('user_id' => $user_id, 'subject' => $subject));
                if (!empty($feed_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('subject_exist');
                    die(json_encode($this->response));
                }
                $insert_id = $this->Custom->insert_data('doctor_feed', $_POST);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('feed_added');
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

    public function UpdateFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        $feed_category_id = ($this->input->post('feed_category_id')) ? $this->input->post('feed_category_id') : '';
        $subject = ($this->input->post('subject')) ? $this->input->post('subject') : '';
        $description = ($this->input->post('description')) ? $this->input->post('description') : '';
        $width = ($this->input->post('width')) ? $this->input->post('width') : '';
        $height = ($this->input->post('height')) ? $this->input->post('height') : '';
        if (!empty($user_id) && !empty($feed_id) && !empty($feed_category_id) && !empty($subject) && !empty($description)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $get_feed = $this->Custom->get_where('doctor_feed', array('id' => $feed_id, 'user_id' => $user_id));
                if (empty($get_feed)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_feed');
                    die(json_encode($this->response));
                }
                $feed_category = $this->Custom->get_where('feed_category', array('id' => $feed_category_id));
                if (empty($feed_category)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_feed_id');
                    die(json_encode($this->response));
                }
                if (!empty($_FILES['image']['name'])) {
                    $name = $_FILES['image']['name'];
                    $get_ext = explode(".", $name);
                    $ext = end($get_ext);
                    $new_name = time() . '.' . $ext;
                    $config['file_name'] = $new_name;
                    $config['upload_path'] = DOCTOR_FEED_PATH . '/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $config['remove_spaces'] = TRUE;

                    if (!is_dir($config['upload_path']))
                        die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('image')) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('image_not_upload');
                        die(json_encode($this->response));
                    } else {
                        $_POST['image'] = $new_name;
                    }
                } else {
                    $_POST['image'] = $get_feed[0]->image;
                    $_POST['height'] = $get_feed[0]->height;
                    $_POST['width'] = $get_feed[0]->width;
                }
                $updateArr = array(
                    'feed_category_id' => $_POST['feed_category_id'],
                    'subject' => $_POST['subject'],
                    'description' => $_POST['description'],
                    'image' => $_POST['image'],
                    'height' => $_POST['height'],
                    'width' => $_POST['width']
                );
                $update_status = $this->Custom->update('doctor_feed', $updateArr, 'id', $feed_id);
                if ($update_status) {
                    $this->db->select('doctor_feed.*, feed_category.category_name_en, feed_category.category_name_ru, users.title, users.name, doctor_profile.profile_image');
                    $this->db->from('doctor_feed');
                    $this->db->join('feed_category', 'doctor_feed.feed_category_id = feed_category.id');
                    $this->db->join('users', 'doctor_feed.user_id = users.id');
                    $this->db->join('doctor_profile', 'users.id = doctor_profile.user_id');
                    $this->db->where('doctor_feed.id', $feed_id);
                    $query = $this->db->get();
                    $feed_record = $query->result();
                    if (empty($feed_record)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('invalid_feed');
                        die(json_encode($this->response));
                    }
                    $feed_record[0]->image = ($feed_record[0]->image) ? base_url() . DOCTOR_FEED_URL . '/' . $feed_record[0]->image : "";
                    $feed_record[0]->profile_image = ($feed_record[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $feed_record[0]->profile_image : "";
                    $feed_record[0]->timestamp = $feed_record[0]->created_at;
                    $feed_record[0]->like_count = GetTotalCount('like_dislike_doctor_feed', array('feed_id' => $feed_id, 'status' => 1));
                    $feed_record[0]->comment_count = GetTotalCount('comment_doctor_feed', array('feed_id' => $feed_id));
                    $feed_like = $this->Custom->get_where('like_dislike_doctor_feed', array('user_id' => $user_id, 'feed_id' => $feed_id));
                    $feed_record[0]->isLike = ($feed_like) ? $feed_like[0]->status : 0;
                    if ($_POST['lang'] == 'en') {
                        $feed_record[0]->category_name = $feed_record[0]->category_name_en;
                    } else if ($_POST['lang'] == 'ru') {
                        $feed_record[0]->category_name = $feed_record[0]->category_name_ru;
                    } else {
                        $feed_record[0]->category_name = $feed_record[0]->category_name_en;
                    }
                    unset($feed_record[0]->category_name_ru);
                    unset($feed_record[0]->category_name_en);
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('feed_updated');
                    $this->response->data = $feed_record[0];
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

    public function GetFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $feed_record = $this->DoctorModel->GetFeeds($user_id, $type, $offset, $per_page);
                if ($feed_record) {
                    foreach ($feed_record as $value) {
                        $value->image = ($value->image) ? base_url() . DOCTOR_FEED_URL . '/' . $value->image : "";
                        $value->profile_image = ($value->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $value->profile_image : "";
                        $value->timestamp = $value->created_at;
                        $value->like_count = GetTotalCount('like_dislike_doctor_feed', array('feed_id' => $value->id, 'status' => 1));
                        $value->comment_count = GetTotalCount('comment_doctor_feed', array('feed_id' => $value->id));
                        $feed_like = $this->Custom->get_where('like_dislike_doctor_feed', array('user_id' => $user_id, 'feed_id' => $value->id));
                        $value->isLike = ($feed_like) ? $feed_like[0]->status : 0;
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
                    $this->response->message = $this->lang->line('feed_details');
                    $this->response->data = $feed_details;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record_for_feed_data');
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

    public function GetFeedDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        if (!empty($user_id) && !empty($feed_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $this->db->select('doctor_feed.*, feed_category.category_name_en, feed_category.category_name_ru, users.title, users.name, doctor_profile.profile_image');
                $this->db->from('doctor_feed');
                $this->db->join('feed_category', 'doctor_feed.feed_category_id = feed_category.id');
                $this->db->join('users', 'doctor_feed.user_id = users.id');
                $this->db->join('doctor_profile', 'users.id = doctor_profile.user_id');
                $this->db->where('doctor_feed.id', $feed_id);
                $query = $this->db->get();
                $feed_record = $query->result();
                if (empty($feed_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_feed');
                    die(json_encode($this->response));
                }
                $feed_record[0]->image = ($feed_record[0]->image) ? base_url() . DOCTOR_FEED_URL . '/' . $feed_record[0]->image : "";
                $feed_record[0]->profile_image = ($feed_record[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $feed_record[0]->profile_image : "";
                $feed_record[0]->timestamp = $feed_record[0]->created_at;
                $feed_record[0]->like_count = GetTotalCount('like_dislike_doctor_feed', array('feed_id' => $feed_id, 'status' => 1));
                $feed_record[0]->comment_count = GetTotalCount('comment_doctor_feed', array('feed_id' => $feed_id));
                $feed_like = $this->Custom->get_where('like_dislike_doctor_feed', array('user_id' => $user_id, 'feed_id' => $feed_id));
                $feed_record[0]->isLike = ($feed_like) ? $feed_like[0]->status : 0;
                if ($_POST['lang'] == 'en') {
                    $feed_record[0]->category_name = $feed_record[0]->category_name_en;
                } else if ($_POST['lang'] == 'ru') {
                    $feed_record[0]->category_name = $feed_record[0]->category_name_ru;
                } else {
                    $feed_record[0]->category_name = $feed_record[0]->category_name_en;
                }
                unset($feed_record[0]->category_name_ru);
                unset($feed_record[0]->category_name_en);

                $this->response->success = 200;
                $this->response->message = $this->lang->line('feed_details');
                $this->response->data = $feed_record[0];
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

    public function DeleteFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        if (!empty($user_id) && !empty($feed_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $del_status = $this->Custom->delete_where('doctor_feed', array('id' => $feed_id));
                if ($del_status) {
                    $this->Custom->delete_where('comment_doctor_feed', array('feed_id' => $feed_id));
                    $this->Custom->delete_where('like_dislike_doctor_feed', array('feed_id' => $feed_id));
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('comment_deleted');
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }

                $this->response->success = 200;
                $this->response->message = $this->lang->line('feed_details');
                $this->response->data = $feed_record[0];
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

    public function AddFeedComment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        $comment = ($this->input->post('comment')) ? $this->input->post('comment') : '';
        if (!empty($user_id) && !empty($feed_id) && !empty($comment)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
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
                    'user_type' => 'doctor',
                    'feed_id' => $feed_id,
                    'comment' => $comment,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('comment_doctor_feed', $insertArr);
                if ($insert_id) {
                    //get comment
                    $comment_data = $this->Custom->get_where('comment_doctor_feed', array('id' => $insert_id));
                    if ($comment_data[0]->user_type == 'patient') {
                        $this->load->model('PatientModel');
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
                        $doctor_data = $this->DoctorModel->GetDoctorProfile($comment_data[0]->user_id);
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

    public function DeleteFeedComment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $comment_id = ($this->input->post('comment_id')) ? $this->input->post('comment_id') : '';
        if (!empty($user_id) && !empty($comment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $comment_doctor_feed = $this->Custom->get_where('comment_doctor_feed', array('id' => $comment_id, 'user_id' => $user_id, 'user_type' => 'doctor'));
                if (empty($comment_doctor_feed)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_comment_id');
                    die(json_encode($this->response));
                }
                $del_status = $this->Custom->delete_where('comment_doctor_feed', array('id' => $comment_id));
                if ($del_status) {
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('comment_deleted');
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
        $per_page = 10;
        $page_number = (isset($_POST['page_number']) && !empty($_POST['page_number'])) ? $_POST['page_number'] : 1;
        if ($page_number != 1)
            $offset = ($page_number - 1) * $per_page;
        else
            $offset = 0;
        if (!empty($user_id) && !empty($feed_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_feed = $this->Custom->get_where('doctor_feed', array('id' => $feed_id));
                if (empty($doctor_feed)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_feed');
                    die(json_encode($this->response));
                }
                //get comment
                //$comment_data = $this->Custom->query("select * from comment_doctor_feed where feed_id = $feed_id ORDER BY id ASC LIMIT $offset, " . $per_page);
                $comment_data = $this->Custom->query("select * from comment_doctor_feed where feed_id = $feed_id ORDER BY id ASC");
                if (empty($comment_data)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }

                foreach ($comment_data as $val) {
                    if ($val->user_type == 'patient') {
                        $this->load->model('PatientModel');
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

    public function LikeDislikeFeed() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $feed_id = ($this->input->post('feed_id')) ? $this->input->post('feed_id') : '';
        $status = ($this->input->post('status')) ? $this->input->post('status') : '';
        if (!empty($user_id) && !empty($feed_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $doctor_feed = $this->Custom->get_where('doctor_feed', array('id' => $feed_id));
                if (empty($doctor_feed)) {
                    $this->response->success = 203;
                    $this->response->message = "Feed id is not valid";
                    die(json_encode($this->response));
                }
                unset($_POST['lang']);
                $feed_record = $this->Custom->get_where('like_dislike_doctor_feed', array('user_id' => $user_id, 'feed_id' => $feed_id));
                if (!empty($feed_record)) {
                    $this->Custom->update_where('like_dislike_doctor_feed', array('status' => $status), array('id' => $feed_record[0]->id));

                    if ($status == 0)
                        $message = $this->lang->line('feed_dislike');
                    if ($status == 1)
                        $message = $this->lang->line('feed_like');
                    $this->response->success = 200;
                    $this->response->message = $message;
                    die(json_encode($this->response));
                } else {
                    $insert_id = $this->Custom->insert_data('like_dislike_doctor_feed', $_POST);
                    if ($insert_id) {
                        if ($status == 0)
                            $message = $this->lang->line('feed_dislike');
                        if ($status == 1)
                            $message = $this->lang->line('feed_like');

                        $this->response->success = 200;
                        $this->response->message = $message;
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

    public function GetPatients() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $this->load->model('PatientModel');
                $appointment = $this->Custom->get_where('appointment', array('doctor_id' => $user_id));
                if ($appointment) {
                    $patient_ids = array();
                    foreach ($appointment as $row) {
                        if (!in_array($row->user_id, $patient_ids))
                            $patient_ids[] = $row->user_id;
                    }
                    foreach ($patient_ids as $row) {
                        $patient_details = $this->PatientModel->GetPatientProfile($row, '');
                        $patient_details[0]->profile_image = ($patient_details[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_details[0]->profile_image : "";
                        $patient_data[] = array(
                            'id' => $row,
                            'profile_image' => ($patient_details) ? $patient_details[0]->profile_image : "",
                            'name' => ($patient_details) ? $patient_details[0]->name : "",
                            'email' => ($patient_details) ? $patient_details[0]->email : "",
                            'mobile_number' => ($patient_details) ? $patient_details[0]->phone_number : "",
                            'dob' => ($patient_details) ? $patient_details[0]->dob : "",
                            'gender' => ($patient_details) ? $patient_details[0]->gender : "",
                            'status' => ($patient_details) ? $patient_details[0]->status : ""
                        );
                    }

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('patient_list');
                    $this->response->data = $patient_data;
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record_for_patient');
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

    public function SearchData() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $keyword = ($this->input->post('keyword')) ? $this->input->post('keyword') : '';
        if (!empty($user_id) && !empty($type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                switch ($type):
                    case 'patient':
                        $this->load->model('PatientModel');
                        //$where = 'users.name LIKE "%' . $keyword . '%"';
                        $patient_details = $this->Custom->query('select * from users where name LIKE "%' . $keyword . '%" AND role = "patient"');
                        //$patient_details = $this->PatientModel->GetPatientProfile($where);
                        if (!empty($patient_details)) {
                            foreach ($patient_details as $value) {
                                $patient_details = $this->PatientModel->GetPatientProfile($value->id, '');
                                $patient_details[0]->profile_image = ($patient_details[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_details[0]->profile_image : "";
                                $patient_data[] = array(
                                    'id' => $patient_details[0]->user_id,
                                    'profile_image' => $patient_details[0]->profile_image,
                                    'name' => $patient_details[0]->name,
                                    'email' => $patient_details[0]->email,
                                    'mobile_number' => $patient_details[0]->phone_number,
                                    'dob' => $patient_details[0]->dob,
                                    'gender' => $patient_details[0]->gender,
                                    'status' => $patient_details[0]->status
                                );
                            }

                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('patient_list');
                            $this->response->data = $patient_data;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_for_patient');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'medicine':
                        //$medicine_details = $this->Custom->query('select * from medicine where doctor_id =' . $user_id . ' AND medicine_name LIKE "%' . $keyword . '%"');
                        $medicine_details = $this->Custom->query('select * from medicine where doctor_id =' . $user_id);
                        if ($medicine_details) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('medicine_list');
                            $this->response->data = $medicine_details;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_for_medicine');
                            die(json_encode($this->response));
                        }
                        break;
                    case 'consultant':
                        //$consultant_details = $this->Custom->query('select * from consultant where doctor_id =' . $user_id . ' AND name LIKE "%' . $keyword . '%"');
                        $consultant_details = $this->Custom->query('select * from consultant where doctor_id =' . $user_id);
                        if ($consultant_details) {
                            $this->response->success = 200;
                            $this->response->message = $this->lang->line('consultant_list');
                            $this->response->data = $consultant_details;
                            die(json_encode($this->response));
                        } else {
                            $this->response->success = 205;
                            $this->response->message = $this->lang->line('no_record_for_consultant');
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

    public function GetRevenue() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $filter_type = ($this->input->post('filter_type')) ? $this->input->post('filter_type') : 'all';
        $year = ($this->input->post('year')) ? $this->input->post('year') : '';
        $month = ($this->input->post('month')) ? $this->input->post('month') : '';
        $start_date = ($this->input->post('start_date')) ? $this->input->post('start_date') : '';
        $end_date = ($this->input->post('end_date')) ? $this->input->post('end_date') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $total = 0.00;
                $online = 0.00;
                $offline = 0.00;
                $invite = 0.00;
                $enquiry = 0.00;
                $total_earn = 0.00;
                switch ($filter_type):
                    case 'all':
                        $transaction_data = $this->Custom->query("select * from transaction where doctor_id = $user_id");
                        if (empty($transaction_data)) {
                            $transaction_history = (object) array(
                                        'total' => $total,
                                        'online' => $online,
                                        'offline' => $offline,
                                        'invite' => $invite,
                                        'enquiry' => $enquiry,
                                        'total_earn' => $total_earn
                            );
                        } else {
                            foreach ($transaction_data as $trans) {
                                $total = $total + $trans->amount;
                                //total online expense
                                if ($trans->type == 'appointment' && ($trans->appointment_type == 1 || $trans->appointment_type == 2 || $trans->appointment_type == 3))
                                    $online = $online + $trans->amount;
                                //total offline expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 0)
                                    $offline = $offline + $trans->amount;
                                //total invite expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 4)
                                    $invite = $invite + $trans->amount;
                                //total enquiry expense
                                if ($trans->type == 'enquiry')
                                    $enquiry = $enquiry + $trans->amount;
                            }
                        }
                        break;

                    case 'year':
                        if ($year == '') {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $transaction_data = $this->Custom->query("select * from transaction where doctor_id = $user_id  AND YEAR(date) = '$year'");
                        if (empty($transaction_data)) {
                            $transaction_history = (object) array(
                                        'total' => $total,
                                        'online' => $online,
                                        'offline' => $offline,
                                        'invite' => $invite,
                                        'enquiry' => $enquiry,
                                        'total_earn' => $total_earn
                            );
                        } else {
                            foreach ($transaction_data as $trans) {
                                $transaction_year = date('Y', strtotime($trans->date));
                                $total = $total + $trans->amount;
                                //total online expense
                                if ($trans->type == 'appointment' && ($trans->appointment_type == 1 || $trans->appointment_type == 2 || $trans->appointment_type == 3))
                                    $online = $online + $trans->amount;
                                //total offline expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 0)
                                    $offline = $offline + $trans->amount;
                                //total invite expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 4)
                                    $invite = $invite + $trans->amount;
                                //total enquiry expense
                                if ($trans->type == 'enquiry')
                                    $enquiry = $enquiry + $trans->amount;
                            }
                        }
                        break;

                    case 'month':
                        if ($year == '' || $month == '') {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $transaction_year = $year . "-" . $month;
                        $transaction_data = $this->Custom->query("select * from transaction where doctor_id = $user_id AND DATE_FORMAT(date, '%Y-%m') = '$transaction_year'");
                        if (empty($transaction_data)) {
                            $transaction_history = (object) array(
                                        'total' => $total,
                                        'online' => $online,
                                        'offline' => $offline,
                                        'invite' => $invite,
                                        'enquiry' => $enquiry,
                                        'total_earn' => $total_earn
                            );
                        } else {
                            $search = $year . "-" . $month;
                            foreach ($transaction_data as $trans) {
                                $total = $total + $trans->amount;
                                //total online expense
                                if ($trans->type == 'appointment' && ($trans->appointment_type == 1 || $trans->appointment_type == 2 || $trans->appointment_type == 3))
                                    $online = $online + $trans->amount;
                                //total offline expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 0)
                                    $offline = $offline + $trans->amount;
                                //total invite expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 4)
                                    $invite = $invite + $trans->amount;
                                //total enquiry expense
                                if ($trans->type == 'enquiry')
                                    $enquiry = $enquiry + $trans->amount;
                            }
                        }
                        break;

                    case 'week':
                        if ($start_date == '' || $end_date == '') {
                            $this->response->success = 201;
                            $this->response->message = $this->lang->line('required_field');
                            die(json_encode($this->response));
                        }
                        $transaction_data = $this->Custom->query("select * from transaction where doctor_id = $user_id AND date >= '$start_date' AND date <= '$end_date'");
                        if (empty($transaction_data)) {
                            $transaction_history = (object) array(
                                        'total' => $total,
                                        'online' => $online,
                                        'offline' => $offline,
                                        'invite' => $invite,
                                        'enquiry' => $enquiry,
                                        'total_earn' => $total_earn
                            );
                        } else {
                            foreach ($transaction_data as $trans) {
                                $total = $total + $trans->amount;
                                //total online expense
                                if ($trans->type == 'appointment' && ($trans->appointment_type == 1 || $trans->appointment_type == 2 || $trans->appointment_type == 3))
                                    $online = $online + $trans->amount;
                                //total offline expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 0)
                                    $offline = $offline + $trans->amount;
                                //total invite expense
                                if ($trans->type == 'appointment' && $trans->appointment_type == 4)
                                    $invite = $invite + $trans->amount;
                                //total enquiry expense
                                if ($trans->type == 'enquiry')
                                    $enquiry = $enquiry + $trans->amount;
                            }
                        }
                        break;
                endswitch;

                $transaction_history = (object) array(
                            'total' => $total,
                            'online' => $online,
                            'offline' => $offline,
                            'invite' => $invite,
                            'enquiry' => $enquiry,
                            'total_earn' => $total_earn
                );

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

    public function GetNotificationSettings() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $notification_settings = $this->Custom->query("select * from notification_settings where user_id = $user_id");

                $this->response->success = 200;
                $this->response->message = $this->lang->line('notification_settings');
                $this->response->data = $notification_settings[0];
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

    public function UpdateNotificationSettings() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $notification_id = ($this->input->post('notification_id')) ? $this->input->post('notification_id') : '';
        if (!empty($user_id) && !empty($notification_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $notification_settings = $this->Custom->query("select * from notification_settings where user_id = $user_id AND id = $notification_id");
                if (empty($notification_settings)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_notification_id');
                    die(json_encode($this->response));
                }
                unset($_POST['notification_id']);
                unset($_POST['user_id']);
                unset($_POST['lang']);
                $update_status = $this->Custom->update('notification_settings', $_POST, 'id', $notification_id);
                if ($update_status) {
                    $notification_settings = $this->Custom->query("select * from notification_settings where id = $notification_id");

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('notification_setting_updated');
                    $this->response->data = $notification_settings[0];
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

    public function AddPatientRecommendation() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $probable_condition = ($this->input->post('probable_condition')) ? $this->input->post('probable_condition') : '';
        $investigations_tests = ($this->input->post('investigations_tests')) ? $this->input->post('investigations_tests') : '';
        $referal_to_specialist = ($this->input->post('referal_to_specialist')) ? $this->input->post('referal_to_specialist') : '';
        $medication_info = ($this->input->post('medication_info')) ? $this->input->post('medication_info') : '';
        $medical_recommedation = ($this->input->post('medical_recommedation')) ? $this->input->post('medical_recommedation') : '';

        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }

                $appointment_record = $this->Custom->get_where('appointment', array('id' => $appointment_id));
                if (empty($appointment_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                if ($appointment_record[0]->status != 5) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('not_add_recommendation');
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'user_id' => $appointment_record[0]->user_id,
                    'doctor_id' => $user_id,
                    'appointment_id' => $appointment_id,
                    'probable_condition' => $probable_condition,
                    'investigations_tests' => $investigations_tests,
                    'referal_to_specialist' => $referal_to_specialist,
                    'medication_info' => $medication_info,
                    'medical_recommedation' => $medical_recommedation,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('patient_recommendation', $insertArr);
                if ($insert_id) {
                    $update_status = $this->Custom->update('appointment', array('status' => 6), 'id', $appointment_id);

                    $this->PatientNotification('appointment', $user_id, $appointment_record[0]->user_id, 'doctor', $appointment_id, $appointment_record[0]->appointment_type, 6, '');

                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('recommendation_added');
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

    public function GetPatientAppointments() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        if (!empty($user_id) && !empty($patient_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('users', array('id' => $patient_id, 'role' => 'patient'));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->get_where('appointment', array('user_id' => $patient_id, 'doctor_id' => $user_id));
                if (!empty($appointment_rec)) {
                    foreach ($appointment_rec as $AR) {
                        $patients_record = $this->DoctorModel->GetPatientProfile($AR->user_id);
                        if ($patients_record) {
                            $patients_record[0]->profile_image = ($patients_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patients_record[0]->profile_image : "";
                        }
                        $AR->created_by = 'patient';
                        $AR->patient = array(
                            'user_id' => $patients_record[0]->user_id,
                            'name' => $patients_record[0]->name,
                            'email' => $patients_record[0]->email,
                            'phone_number' => $patients_record[0]->phone_number,
                            'dob' => $patients_record[0]->dob,
                            'gender' => $patients_record[0]->gender,
                            'profile_image' => $patients_record[0]->profile_image
                        );
                        if ($AR->user_type == 'patient_member') {
                            $patient_member = $this->Custom->get_where('patient_member', array('id' => $AR->member_id));
                            $additional_record = $this->Custom->get_where('member_additional_info', array('member_id' => $AR->member_id));
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
                            $AR->member = (object) array(
                                        'member_id' => $patient_member[0]->id,
                                        'name' => $patient_member[0]->name,
                                        'relationship' => $patient_member[0]->relationship,
                                        'dob' => $patient_member[0]->dob,
                                        'gender' => $patient_member[0]->gender,
                                        'height' => $patient_member[0]->height,
                                        'weight' => $patient_member[0]->weight,
                                        'city' => $patient_member[0]->city,
                                        'locality' => $patient_member[0]->locality,
                                        'gender' => $patient_member[0]->gender,
                                        'additional_info' => $add_infoArr
                            );
                        } else {
                            $AR->member = new stdClass();
                        }
                        $appointment[] = $AR;
                    }
                    if (!empty($appointment)) {
                        $this->response->success = 200;
                        $this->response->message = $this->lang->line('appointment_list');
                        $this->response->data = $appointment;
                        die(json_encode($this->response));
                    } else {
                        $this->response->success = 205;
                        $this->response->message = $this->lang->line('no_record_appointment');
                        die(json_encode($this->response));
                    }
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

    public function AddMedicine() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $medicine_name = ($this->input->post('medicine_name')) ? $this->input->post('medicine_name') : '';
        $dosage = ($this->input->post('dosage')) ? $this->input->post('dosage') : '';
        $frequency = ($this->input->post('frequency')) ? $this->input->post('frequency') : '';
        $duration = ($this->input->post('duration')) ? $this->input->post('duration') : '';
        $meal = ($this->input->post('meal')) ? $this->input->post('meal') : '';
        $insruction = ($this->input->post('insruction')) ? $this->input->post('insruction') : '';

        if (!empty($user_id) && !empty($medicine_name)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $medicine_record = $this->Custom->get_where('medicine', array('doctor_id' => $user_id, 'medicine_name' => $medicine_name));
                if (!empty($medicine_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('medicine_exist');
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'medicine_name' => $medicine_name,
                    'dosage' => $dosage,
                    'frequency' => $frequency,
                    'duration' => $duration,
                    'meal' => $meal,
                    'insruction' => $insruction,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('medicine', $insertArr);
                if ($insert_id) {
                    $medicine_record = $this->Custom->get_where('medicine', array('id' => $insert_id));
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('medicine_added');
                    $this->response->data = $medicine_record[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetPatientDocument() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        if (!empty($user_id) && !empty($patient_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('users', array('id' => $patient_id, 'role' => 'patient'));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                $patients_document = $this->Custom->get_where('patients_document', array('patient_id' => $patient_id));
                if ($patients_document) {
                    foreach ($patients_document as $val) {
                        $val->image = ($val->image) ? base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $val->image : "";
                    }
                    $this->response->success = 200;
                    $this->response->message = $this->lang->line('patient_document');
                    $this->response->data = $patients_document;
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
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
                        if ($val->notification_type == 'appointment') {
                            $appointment = $this->Custom->get_where('appointment', array('id' => $val->action_id));
                            $val->annoymous_status = ($appointment) ? $appointment[0]->annoymous_status : 0;
                        } else {
                            $val->annoymous_status = 0;
                        }
                        $users = $this->Custom->get_where('users', array('id' => $val->sender_id));
                        $patients_record = $this->Custom->get_where('patient_profile', array('user_id' => $val->sender_id));
                        $data[] = array(
                            'notification_id' => $val->notification_id,
                            'notification_type' => $val->notification_type,
                            'message' => (isset($_POST['lang']) && !empty($_POST['lang']) && ($_POST['lang'] == 'en' OR $_POST['lang'] == '')) ? $val->message : $val->message_ru,
                            'action_id' => $val->action_id,
                            'annoymous_status' => $val->annoymous_status,
                            'sender_id' => $val->sender_id,
                            'sender_name' => ($users) ? $users[0]->name : "",
                            'sender_profile_image' => ($patients_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patients_record[0]->profile_image : "",
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
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

    public function GetAppointmentDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $today_date = date('Y-m-d');
                $appointment_record = $this->Custom->query('select * from appointment where doctor_id = "' . $user_id . '"');
                if ($appointment_record) {
                    foreach ($appointment_record as $value) {
                        if ($value->appointment_date < $today_date && ($value->status == 0 || $value->status == 1 || $value->status == 4)) {
                            $this->Custom->update_where('appointment', array('status' => 9), array('id' => $value->id));
                        }
                    }
                }
                $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = $user_id AND id = $appointment_id");
                if (empty($appointment_rec)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_appointment');
                    die(json_encode($this->response));
                }
                $patients_record = $this->DoctorModel->GetPatientProfile($appointment_rec[0]->user_id);
                if ($patients_record) {
                    $patients_record[0]->profile_image = ($patients_record[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patients_record[0]->profile_image : "";
                }
                $appointment_rec[0]->created_by = 'patient';
                $appointment_rec[0]->patient = array(
                    'user_id' => $patients_record[0]->user_id,
                    'name' => $patients_record[0]->name,
                    'email' => $patients_record[0]->email,
                    'phone_number' => $patients_record[0]->phone_number,
                    'dob' => $patients_record[0]->dob,
                    'gender' => $patients_record[0]->gender,
                    'profile_image' => $patients_record[0]->profile_image
                );
                if ($appointment_rec[0]->user_type == 'patient_member') {
                    $patient_member = $this->Custom->get_where('patient_member', array('id' => $appointment_rec[0]->member_id));
                    $additional_record = $this->Custom->get_where('member_additional_info', array('member_id' => $appointment_rec[0]->member_id));
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
                    $appointment_rec[0]->member = (object) array(
                                'member_id' => $patient_member[0]->id,
                                'name' => $patient_member[0]->name,
                                'relationship' => $patient_member[0]->relationship,
                                'dob' => $patient_member[0]->dob,
                                'gender' => $patient_member[0]->gender,
                                'height' => $patient_member[0]->height,
                                'weight' => $patient_member[0]->weight,
                                'city' => $patient_member[0]->city,
                                'locality' => $patient_member[0]->locality,
                                'gender' => $patient_member[0]->gender,
                                'additional_info' => $add_infoArr
                    );
                } else {
                    $appointment_rec[0]->member = new stdClass();
                }

                $AppintmentDoc = array();
                if ($appointment_rec[0]->appointment_doc != '') {
                    $appointment_doc = explode(',', $appointment_rec[0]->appointment_doc);
                    foreach ($appointment_doc as $doc) {
                        $AppintmentDoc[] = base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $doc;
                    }
                }
                $appointment_rec[0]->AppintmentDoc = $AppintmentDoc;


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

    public function SendChatMessage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $message_type = ($this->input->post('message_type')) ? $this->input->post('message_type') : '';

        if (!empty($user_id) && !empty($appointment_id) && !empty($message_type)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
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
                    'receiver_id' => $appointment_rec[0]->user_id,
                    'sender_type' => 'doctor',
                    'message' => $message,
                    'message_type' => $message_type,
                    'message_status' => 0,
                    'sent_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('chat_message', $insert_arr);
                if ($insert_id) {
                    if ($appointment_rec[0]->chat_status == 0) {
                        $this->Custom->update_where('appointment', array('chat_status' => 1), array('id' => $appointment_id));
                    }
                    //get messages
                    $chat_message_rec = $this->Custom->query("select * from chat_message where id = '$insert_id'");
                    $this->load->model('PatientModel');
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
                    $receiver_data = $this->Custom->get_where('users', array('id' => $appointment_rec[0]->user_id));
                    $message = $this->lang->line('appointment_new_message');
                    if (isset($receiver_data) && !empty($receiver_data)) {
                        if ($receiver_data[0]->device_type == "android") {
                            if (!empty($receiver_data[0]->device_token)) {
                                $registatoin_ids = array($receiver_data[0]->device_token);

                                $url = 'https://fcm.googleapis.com/fcm/send';
                                $fields = array(
                                    'registration_ids' => $registatoin_ids,
                                    'data' => array("message" => $message, 'notification_type' => 'appointment', 'type' => 'chat', 'appointment_id' => $appointment_id, 'sender_id' => $user_id, 'sender_name' => $sender_name),
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
                                    'body' => $message,
                                ),
                                'badge' => 1,
                                'notification_type' => 'appointment',
                                'type' => 'chat',
                                'appointment_id' => $appointment_id,
                                'sender_id' => $user_id,
                                'sender_name' => $sender_name,
                                'sound' => 'default',
                            );
                            $passphrase = '';
                            $ctx = stream_context_create();
                            if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientProduction.pem');
                            } else {
                                stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientDevelopment.pem');
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }

                $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = '$user_id' AND id = '$appointment_id'");
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
                        $this->load->model('PatientModel');
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
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = '$user_id' AND id = '$appointment_id'");
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
                        $this->load->model('PatientModel');
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

    public function UpdateAppointmentCallTimings() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';
        $call_chat_timing = ($this->input->post('call_chat_timing')) ? $this->input->post('call_chat_timing') : '';

        if (!empty($user_id) && !empty($appointment_id) && !empty($call_chat_timing)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $appointment_rec = $this->Custom->query("select * from appointment where doctor_id = '$user_id' AND id = '$appointment_id'");
                if (empty($appointment_rec)) {
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

    public function NotifyPatientForCall() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $appointment_id = ($this->input->post('appointment_id')) ? $this->input->post('appointment_id') : '';

        if (!empty($user_id) && !empty($appointment_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
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
                $receiver_data = $this->Custom->get_where('users', array('id' => $appointment_rec[0]->user_id));
                $sender_name = $user_record[0]->name;
                $message = $this->lang->line('audio_call_message') . " " . $sender_name . ".";
                if (isset($receiver_data) && !empty($receiver_data)) {
                    if ($receiver_data[0]->device_type == "android") {
                        if (!empty($receiver_data[0]->device_token)) {
                            $registatoin_ids = array($receiver_data[0]->device_token);

                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $fields = array(
                                'registration_ids' => $registatoin_ids,
                                'data' => array("message" => $message, 'notification_type' => 'appointment', 'type' => $appointment_rec[0]->appointment_type, 'appointment_id' => $appointment_id, 'doctor_id' => $user_id, 'doctor_name' => $sender_name),
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
                                'body' => $message,
                            ),
                            'badge' => 1,
                            'notification_type' => 'appointment',
                            'type' => $appointment_rec[0]->appointment_type,
                            'appointment_id' => $appointment_id,
                            'doctor_id' => $user_id,
                            'doctor_name' => $sender_name,
                            'sound' => 'default',
                        );
                        $passphrase = '';
                        $ctx = stream_context_create();
                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientProduction.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientDevelopment.pem');
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
                $this->response->message = $this->lang->line('patient_notified');
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

    public function GetRatingAndReviews() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $this->load->model('PatientModel');

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $rating_and_reviews = $this->Custom->query("select * from rating_and_reviews where receiver_id = '$user_id' AND type = 'doctor' order by id DESC");
                if (empty($rating_and_reviews)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }
                foreach ($rating_and_reviews as $row) {
                    $sender_data = $this->PatientModel->GetPatientProfile($row->sender_id);
                    if ($sender_data) {
                        $row->sender_name = $sender_data[0]->name;
                        $row->sender_profile_image = ($sender_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $sender_data[0]->profile_image : "";
                    } else {
                        $row->sender_name = "";
                        $row->sender_profile_image = "";
                    }
                    $row->timestamp = $row->created_at;
                }
                $this->response->success = 200;
                $this->response->message = $this->lang->line('reviews_details');
                $this->response->data = $rating_and_reviews;
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

    public function GetRatingAndReviewsDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $rating_id = ($this->input->post('rating_id')) ? $this->input->post('rating_id') : '';
        $this->load->model('PatientModel');

        if (!empty($user_id) && !empty($rating_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $rating_and_reviews = $this->Custom->query("select * from rating_and_reviews where receiver_id = '$user_id' AND id = $rating_id");
                if (empty($rating_and_reviews)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('invalid_rating_id');
                    die(json_encode($this->response));
                }
                $sender_data = $this->PatientModel->GetPatientProfile($rating_and_reviews[0]->sender_id);
                if ($sender_data) {
                    $rating_and_reviews[0]->sender_name = $sender_data[0]->name;
                    $rating_and_reviews[0]->sender_profile_image = ($sender_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $sender_data[0]->profile_image : "";
                } else {
                    $rating_and_reviews[0]->sender_name = "";
                    $rating_and_reviews[0]->sender_profile_image = "";
                }
                $rating_and_reviews[0]->timestamp = $rating_and_reviews[0]->created_at;

                $this->response->success = 200;
                $this->response->message = $this->lang->line('reviews_details');
                $this->response->data = $rating_and_reviews;
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

    public function GetAverageRating() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $rating_record = GetDetails('rating_and_reviews', array('receiver_id' => $user_id, 'type' => 'doctor'));
                $userCount = count($rating_record);
                $AverageRating = AverageRating($user_id, 'doctor');
                $this->response->success = 200;
                $this->response->message = $this->lang->line('reviews_details');
                $this->response->AverageRating = ($AverageRating) ? $AverageRating : 0.0;
                $this->response->user = ($userCount) ? $userCount : 0;
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
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $call_type = ($this->input->post('call_type')) ? $this->input->post('call_type') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';

        if (!empty($user_id) && !empty($call_type) && !empty($type) && !empty($patient_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                //send notification
                define("API_KEY", "AIzaSyCNdCz5c3Il9jdjHVdCDa2ridWIT9NuRpE");
                $receiver_data = $this->Custom->get_where('users', array('id' => $patient_id, 'role' => 'patient'));
                if (isset($receiver_data) && !empty($receiver_data)) {
                    if ($receiver_data[0]->device_type == "android") {
                        if (!empty($receiver_data[0]->device_token)) {
                            $registatoin_ids = array($receiver_data[0]->device_token);

                            $url = 'https://fcm.googleapis.com/fcm/send';
                            $fields = array(
                                'registration_ids' => $registatoin_ids,
                                'data' => array('notification_type' => 'appointment', 'type' => $type, 'call_type' => $call_type),
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
                                'body' => $message,
                            ),
                            'badge' => 1,
                            'notification_type' => 'appointment',
                            'type' => $type,
                            'call_type' => $call_type,
                            'sound' => 'default',
                        );
                        $passphrase = '';
                        $ctx = stream_context_create();
                        if (!empty($app_state) && $app_state == 'prod' && isset($app_state)) {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientProduction.pem');
                        } else {
                            stream_context_set_option($ctx, 'ssl', 'local_cert', '/var/www/html/Zumcare/certificate/PatientDevelopment.pem');
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

    /*     * ******* Not in Use *********** */

    public function AddPatients() {
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $mobile_number = ($this->input->post('mobile_number')) ? $this->input->post('mobile_number') : '';
        $landline = ($this->input->post('landline')) ? $this->input->post('landline') : '';
        $email = ($this->input->post('email')) ? $this->input->post('email') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $refered_by = ($this->input->post('refered_by')) ? $this->input->post('refered_by') : '';
        $dob = ($this->input->post('dob')) ? $this->input->post('dob') : '';
        $clinic = ($this->input->post('clinic')) ? $this->input->post('clinic') : '';
        $send_clinic_address = ($this->input->post('send_clinic_address')) ? $this->input->post('send_clinic_address') : '';
        if (!empty($doctor_id) && !empty($name) && !empty($mobile_number) && !empty($email) && !empty($clinic)) {
            $user_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $doctor_id);
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $profile_record = $this->Custom->get_where('doctor_profile', array('user_id' => $doctor_id));
                if ($profile_record[0]->clinic_name != $clinic) {
                    $this->response->success = 203;
                    $this->response->message = "Clinic name is not valid.";
                    die(json_encode($this->response));
                }
                if (!empty($_FILES['image']['name'])) {
                    $img_name = $_FILES['image']['name'];
                    $get_ext = explode(".", $img_name);
                    $ext = end($get_ext);
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

                    if (!$this->upload->do_upload('image')) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('image_not_upload');
                        die(json_encode($this->response));
                    } else {
                        $_POST['profile_image'] = $new_name;
                    }
                } else {
                    $_POST['profile_image'] = "";
                }
                $patient_uid = GenerateRandomNumber(9);
                $_POST['patient_uid'] = $patient_uid;
                $_POST['created_at'] = strtotime(date("Y-m-d H:i:s"));
                $patient_record = $this->Custom->get_where('patients', array('doctor_id' => $doctor_id, 'email' => $email));
                if (!empty($patient_record)) {
                    $this->response->success = 203;
                    $this->response->message = "Patient already added with this doctor.";
                    die(json_encode($this->response));
                }
                $insert_id = $this->Custom->insert_data('patients', $_POST);
                if ($insert_id) {
                    //send mail
                    $mail_data = array('name' => $name, 'doctor_name' => $user_record[0]->title . " " . $user_record[0]->name, 'send_clinic_address' => $send_clinic_address, 'patient_uid' => $patient_uid, 'doctor_profile_record' => $profile_record);
                    $content = $this->load->view('mail/add_patient', $mail_data, TRUE);
                    $subject = 'Patient Registration - zumcare App';
                    $this->SendMail($email, $subject, $content);

                    $patient_data = $this->Custom->get_where('patients', array('id' => $insert_id));
                    $patient_data[0]->profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : '';
                    $this->response->success = 200;
                    $this->response->message = 'Patient added successfully.';
                    $this->response->data = $patient_data[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function UpdatePatients() {
        $doctor_id = ($this->input->post('doctor_id')) ? $this->input->post('doctor_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $mobile_number = ($this->input->post('mobile_number')) ? $this->input->post('mobile_number') : '';
        $landline = ($this->input->post('landline')) ? $this->input->post('landline') : '';
        $email = ($this->input->post('email')) ? $this->input->post('email') : '';
        $gender = ($this->input->post('gender')) ? $this->input->post('gender') : '';
        $refered_by = ($this->input->post('refered_by')) ? $this->input->post('refered_by') : '';
        $dob = ($this->input->post('dob')) ? $this->input->post('dob') : '';
        $clinic = ($this->input->post('clinic')) ? $this->input->post('clinic') : '';
        $send_clinic_address = ($this->input->post('send_clinic_address')) ? $this->input->post('send_clinic_address') : '';
        if (!empty($patient_id) && !empty($doctor_id) && !empty($name) && !empty($mobile_number) && !empty($email) && !empty($clinic)) {
            $user_record = $this->Custom->get_where('users', array('id' => $doctor_id, 'role' => 'doctor'));
            if (isset($user_record) && !empty($user_record)) {
                //check authentication
                $headers = apache_request_headers();
                $auth_record = CheckAuthentication($headers['AUTHENTICATIONTOKEN'], $doctor_id);
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $profile_record = $this->Custom->get_where('doctor_profile', array('user_id' => $doctor_id));
                if ($profile_record[0]->clinic_name != $clinic) {
                    $this->response->success = 203;
                    $this->response->message = "Clinic name is not valid.";
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('patients', array('id' => $patient_id));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                if (!empty($_FILES['image']['name'])) {
                    $img_name = $_FILES['image']['name'];
                    $get_ext = explode(".", $img_name);
                    $ext = end($get_ext);
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

                    if (!$this->upload->do_upload('image')) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('image_not_upload');
                        die(json_encode($this->response));
                    } else {
                        if ($patient_details[0]->profile_image != "")
                            unlink(PATIENT_PROFILE_PATH . '/' . $patient_details[0]->profile_image);
                        $profile_image = $new_name;
                    }
                } else {
                    $profile_image = ($patient_details[0]->profile_image) ? $patient_details[0]->profile_image : "";
                }

                $updateArr = array(
                    'doctor_id' => $doctor_id,
                    'profile_image' => $profile_image,
                    'name' => $name,
                    'mobile_number' => $mobile_number,
                    'landline' => $landline,
                    'email' => $email,
                    'gender' => $gender,
                    'refered_by' => $refered_by,
                    'dob' => $dob,
                    'clinic' => $clinic,
                    'send_clinic_address' => $send_clinic_address
                );
                $update_status = $this->Custom->update('patients', $updateArr, 'id', $patient_id);
                if ($update_status) {
                    //send mail
                    $mail_data = array('name' => $name, 'doctor_name' => $user_record[0]->title . " " . $user_record[0]->name, 'send_clinic_address' => $send_clinic_address, 'doctor_profile_record' => $profile_record);
                    $content = $this->load->view('mail/update_patient', $mail_data, TRUE);
                    $subject = 'Patient Updation - zumcare App';
                    $this->SendMail($email, $subject, $content);

                    $patient_data = $this->Custom->get_where('patients', array('id' => $patient_id));
                    $patient_data[0]->profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : '';
                    $this->response->success = 200;
                    $this->response->message = 'Patient details updated successfully.';
                    $this->response->data = $patient_data[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AddPatientPayment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $type = ($this->input->post('type')) ? $this->input->post('type') : '';
        $balance_due = ($this->input->post('balance_due')) ? $this->input->post('balance_due') : '';
        $issue_refund = ($this->input->post('issue_refund')) ? $this->input->post('issue_refund') : '';
        $amount_collect = ($this->input->post('amount_collect')) ? $this->input->post('amount_collect') : '';
        $payment_date = ($this->input->post('payment_date')) ? $this->input->post('payment_date') : '';

        if (!empty($user_id) && !empty($type) && !empty($patient_id) && !empty($balance_due) && !empty($issue_refund) && !empty($amount_collect) && !empty($payment_date)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                if ($type == 'by_me') {
                    $patients_record = $this->Custom->get_where('patients', array('id' => $patient_id, 'doctor_id' => $user_id));
                    if (empty($patients_record)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('patient_id_error');
                        die(json_encode($this->response));
                    }
                }
                if ($type == 'by_patient') {
                    $patients_record = $this->Custom->get_where('users', array('id' => $patient_id, 'role' => 'patient'));
                    if (empty($patients_record)) {
                        $this->response->success = 203;
                        $this->response->message = $this->lang->line('patient_id_error');
                        die(json_encode($this->response));
                    }
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'patient_id' => $patient_id,
                    'type' => $type,
                    'balance_due' => $balance_due,
                    'issue_refund' => $issue_refund,
                    'amount_collect' => $amount_collect,
                    'payment_date' => $payment_date,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('patient_payment', $insertArr);
                if ($insert_id) {
                    $payment_record = $this->Custom->get_where('patient_payment', array('id' => $insert_id));
                    $payment_record[0]->patient_name = $patients_record[0]->name;
                    $this->response->success = 200;
                    $this->response->message = 'Payment added successfully.';
                    $this->response->data = $payment_record[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AddConsultant() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $mobile_number = ($this->input->post('mobile_number')) ? $this->input->post('mobile_number') : '';
        $email = ($this->input->post('email')) ? $this->input->post('email') : '';
        $shares = ($this->input->post('shares')) ? $this->input->post('shares') : 0;

        if (!empty($user_id) && !empty($name) && !empty($mobile_number) && !empty($email)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $consultant_record = $this->Custom->get_where('consultant', array('doctor_id' => $user_id, 'email' => $email));
                if (!empty($consultant_record)) {
                    $this->response->success = 203;
                    $this->response->message = "Email already exists.";
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'name' => $name,
                    'mobile_number' => $mobile_number,
                    'email' => $email,
                    'shares' => $shares,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('consultant', $insertArr);
                if ($insert_id) {
                    $consultant_record = $this->Custom->get_where('consultant', array('id' => $insert_id));
                    $this->response->success = 200;
                    $this->response->message = 'Consultant added successfully.';
                    $this->response->data = $consultant_record[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function CreateAppointment() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $start_time = ($this->input->post('start_time')) ? $this->input->post('start_time') : '';
        $end_time = ($this->input->post('end_time')) ? $this->input->post('end_time') : '';
        $consultant_id = ($this->input->post('consultant_id')) ? $this->input->post('consultant_id') : "";
        $notes = ($this->input->post('notes')) ? $this->input->post('notes') : "";
        $booking_date = ($this->input->post('booking_date')) ? $this->input->post('booking_date') : "";
        $send_sms = ($this->input->post('send_sms')) ? $this->input->post('send_sms') : "";

        if (!empty($user_id) && !empty($patient_id) && !empty($start_time) && !empty($end_time) && !empty($consultant_id) && !empty($booking_date)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patients_record = $this->Custom->get_where('patients', array('id' => $patient_id, 'doctor_id' => $user_id));
                if (empty($patients_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                $consultantIdArr = explode(',', $consultant_id);
                foreach ($consultantIdArr as $val) {
                    $consultant_record = $this->Custom->get_where('consultant', array('doctor_id' => $user_id, 'id' => $val));
                    if (empty($consultant_record)) {
                        $this->response->success = 203;
                        $this->response->message = "Consultant id " . $val . " is not valid.";
                        die(json_encode($this->response));
                    }
                }

                $insertArr = array(
                    'doctor_id' => $user_id,
                    'patient_id' => $patient_id,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'consultant_id' => $consultant_id,
                    'notes' => $notes,
                    'booking_date' => $booking_date,
                    'send_sms' => $send_sms,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('doctor_appointment', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = 'Appointment created successfully.';
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetPatientPaymentDues() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patientIdArr = array();
                $patient_payment_details = $this->Custom->query('select patient_id, id, doctor_id, type from patient_payment where doctor_id = "' . $user_id . '"');
                if (!empty($patient_payment_details)) {
                    foreach ($patient_payment_details as $row) {
                        if (!in_array($row->patient_id, $patientIdArr)) {
                            $patientIdArr[] = $row->patient_id;
                            $unique[] = $row;
                        }
                    }
                    foreach ($unique as $value) {
                        if ($value->type == 'by_me')
                            $patient_details = $this->Custom->query('select * from patients where id = "' . $value->patient_id . '"');
                        if ($value->type == 'by_patient')
                            $patient_details = $this->Custom->query('select * from users where id = "' . $value->patient_id . '"');
                        $payment_details = $this->Custom->query('select sum(balance_due) as due_amount from patient_payment where patient_id = "' . $value->patient_id . '"');
                        $value->due_amount = $payment_details[0]->due_amount;
                        $value->name = ($patient_details) ? $patient_details[0]->name : "";
                        $value->profile_image = ($patient_details[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_details[0]->profile_image : "";
                        $actual_data[] = $value;
                    }
                    $this->response->success = 200;
                    $this->response->message = 'Payment Dues.';
                    $this->response->data = $actual_data;
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

    public function CreatePackage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $pay_online = ($this->input->post('pay_online')) ? $this->input->post('pay_online') : '';
        $package_name = ($this->input->post('package_name')) ? $this->input->post('package_name') : '';
        $package_description = ($this->input->post('package_description')) ? $this->input->post('package_description') : '';
        $validity_type = ($this->input->post('validity_type')) ? $this->input->post('validity_type') : '';
        $validity = ($this->input->post('validity')) ? $this->input->post('validity') : '';
        $no_of_text_consultation = ($this->input->post('no_of_text_consultation')) ? $this->input->post('no_of_text_consultation') : '';
        $no_of_audio_consultation = ($this->input->post('no_of_audio_consultation')) ? $this->input->post('no_of_audio_consultation') : '';
        $no_of_video_consultation = ($this->input->post('no_of_video_consultation')) ? $this->input->post('no_of_video_consultation') : '';
        $audio_video_call_duration = ($this->input->post('audio_video_call_duration')) ? $this->input->post('audio_video_call_duration') : '';
        $medicine_included = ($this->input->post('medicine_included')) ? $this->input->post('medicine_included') : '';
        $price = ($this->input->post('price')) ? $this->input->post('price') : '';
        $online_price = ($this->input->post('online_price')) ? $this->input->post('online_price') : '';
        $height = ($this->input->post('height')) ? $this->input->post('height') : '';
        $width = ($this->input->post('width')) ? $this->input->post('width') : '';
        $images = (isset($_FILES['image']) && !empty($_FILES['image'])) ? $_FILES['image'] : '';

        if (!empty($user_id) && !empty($package_name) && !empty($package_description) && !empty($validity_type) && !empty($validity) && (!empty($no_of_text_consultation) || !empty($no_of_audio_consultation) || !empty($no_of_video_consultation)) && !empty($audio_video_call_duration) && !empty($price) && !empty($online_price)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $package_record = $this->Custom->get_where('packages', array('package_name' => $package_name));
                if (!empty($package_record)) {
                    $this->response->success = 203;
                    $this->response->message = "Package name is already exists.";
                    die(json_encode($this->response));
                }
                if (!empty($images)) {
                    if (empty($height) || empty($width)) {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                    if (!empty($_FILES['image']['name'])) {
                        $name = $_FILES['image']['name'];
                        $ext = end((explode(".", $name)));
                        $new_name = time() . '.' . $ext;
                        $config['file_name'] = $new_name;
                        $config['upload_path'] = PACKAGE_PATH . '/';
                        $config['allowed_types'] = '*';
                        $config['overwrite'] = TRUE;
                        $config['remove_spaces'] = TRUE;

                        if (!is_dir($config['upload_path']))
                            die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('image')) {
                            $this->response->success = 203;
                            $this->response->message = $this->lang->line('image_not_upload');
                            die(json_encode($this->response));
                        } else {
                            $image_name = $new_name;
                        }
                    } else {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                } else {
                    $image_name = "";
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'pay_online' => $pay_online,
                    'package_name' => $package_name,
                    'package_description' => $package_description,
                    'validity_type' => $validity_type,
                    'validity' => $validity,
                    'no_of_text_consultation' => $no_of_text_consultation,
                    'no_of_audio_consultation' => $no_of_audio_consultation,
                    'no_of_video_consultation' => $no_of_video_consultation,
                    'audio_video_call_duration' => $audio_video_call_duration,
                    'medicine_included' => $medicine_included,
                    'price' => $price,
                    'online_price' => $online_price,
                    'image' => $image_name,
                    'height' => $height,
                    'width' => $width,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('packages', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = 'Package created successfully.';
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function SellPackage() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $country_code = ($this->input->post('country_code')) ? $this->input->post('country_code') : '';
        $mobile_number = ($this->input->post('mobile_number')) ? $this->input->post('mobile_number') : '';
        $name = ($this->input->post('name')) ? $this->input->post('name') : '';
        $email = ($this->input->post('email')) ? $this->input->post('email') : '';
        $text_consultation = ($this->input->post('text_consultation')) ? $this->input->post('text_consultation') : '';
        $audio_consultation = ($this->input->post('audio_consultation')) ? $this->input->post('audio_consultation') : '';
        $video_consultation = ($this->input->post('video_consultation')) ? $this->input->post('video_consultation') : '';
        $validity = ($this->input->post('validity')) ? $this->input->post('validity') : '';
        $currency = ($this->input->post('currency')) ? $this->input->post('currency') : '';
        $price = ($this->input->post('price')) ? $this->input->post('price') : '';

        if (!empty($user_id) && !empty($country_code) && !empty($mobile_number) && !empty($name) && !empty($email) && !empty($text_consultation) && !empty($audio_consultation) && !empty($video_consultation) && !empty($validity) && !empty($currency) && !empty($price)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'country_code' => $country_code,
                    'mobile_number' => $mobile_number,
                    'name' => $name,
                    'email' => $email,
                    'text_consultation' => $text_consultation,
                    'audio_consultation' => $audio_consultation,
                    'video_consultation' => $video_consultation,
                    'validity' => $validity,
                    'currency' => $currency,
                    'price' => $price,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('sell_package', $insertArr);
                if ($insert_id) {
                    $this->response->success = 200;
                    $this->response->message = 'Sell package successfully.';
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetPackages() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';

        if (!empty($user_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $package_record = $this->Custom->get_where('packages', array('doctor_id' => $user_id));
                if (empty($package_record)) {
                    $this->response->success = 205;
                    $this->response->message = $this->lang->line('no_record');
                    die(json_encode($this->response));
                }
                foreach ($package_record as $val) {
                    $val->image = ($val->image) ? base_url() . PACKAGE_URL . '/' . $val->image : "";
                    $val->package_images = $package_images;
                    $package_data[] = $val;
                }
                $this->response->success = 200;
                $this->response->message = 'Package List.';
                $this->response->data = $package_data;
                die(json_encode($this->response));
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function PatientDetails() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        if (!empty($user_id) && !empty($patient_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('patients', array('id' => $patient_id));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                $patient_details[0]->created_by = 'by_me';
                $patient_details[0]->profile_image = ($patient_details[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_details[0]->profile_image : "";
                //record visit
                $record_visit = $this->Custom->get_where('record_visit', array('patient_id' => $patient_id, 'doctor_id' => $user_id));
                $patient_details[0]->visit_count = ($record_visit) ? count($record_visit) : "";
                //appointment
                $today_date = date('Y-m-d');
                $appointments = $this->Custom->query('select * from doctor_appointment where patient_id = "' . $patient_id . '" AND booking_date >= "' . $today_date . '" ORDER BY booking_date ASC');
                $patient_details[0]->next_appointment = ($appointments) ? $appointments[0]->booking_date : "";
                //due amount
                $patient_payment = $this->Custom->get_where('patient_payment', array('patient_id' => $patient_id, 'doctor_id' => $user_id));
                $total_due_amount = 0.00;
                if (!empty($patient_payment)) {
                    foreach ($patient_payment as $row) {
                        $total_due_amount = $total_due_amount + $row->balance_due;
                    }
                    $patient_details[0]->total_due_amount = $total_due_amount;
                } else {
                    $patient_details[0]->total_due_amount = "";
                }

                $this->response->success = 200;
                $this->response->message = 'Patient Details.';
                $this->response->data = $patient_details[0];
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

    public function GetPatientVisitRecord() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        if (!empty($user_id) && !empty($patient_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('patients', array('id' => $patient_id));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                $record_visit = $this->Custom->get_where('record_visit', array('patient_id' => $patient_id, 'doctor_id' => $user_id));
                if (!empty($record_visit)) {
                    foreach ($record_visit as $row) {
                        $treatment_details = $this->Custom->get_where('treatment', array('id' => $row->treatment));
                        $row->treatment_name = ($treatment_details) ? $treatment_details[0]->name : "";
                    }
                    $this->response->success = 200;
                    $this->response->message = 'Visit Record.';
                    $this->response->data = $record_visit;
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

    public function AddRecordVisit() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $visit_date = ($this->input->post('visit_date')) ? $this->input->post('visit_date') : '';
        $clinic_name = ($this->input->post('clinic_name')) ? $this->input->post('clinic_name') : '';
        $treatment = ($this->input->post('treatment')) ? $this->input->post('treatment') : '';
        $patient_complain = ($this->input->post('patient_complain')) ? $this->input->post('patient_complain') : '';
        $doctor_consulatation = ($this->input->post('doctor_consulatation')) ? $this->input->post('doctor_consulatation') : '';
        $start_time = ($this->input->post('start_time')) ? $this->input->post('start_time') : '';
        $end_time = ($this->input->post('end_time')) ? $this->input->post('end_time') : '';
        $payment_id = ($this->input->post('payment_id')) ? $this->input->post('payment_id') : "";
        $prescription_id = ($this->input->post('prescription_id')) ? $this->input->post('prescription_id') : "";
        $send_sms = ($this->input->post('send_sms')) ? $this->input->post('send_sms') : "";
        $next_booking_date = ($this->input->post('next_booking_date')) ? $this->input->post('next_booking_date') : '';

        if (!empty($user_id) && !empty($patient_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patients_record = $this->Custom->get_where('patients', array('id' => $patient_id, 'doctor_id' => $user_id));
                if (empty($patients_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                if (!empty($clinic_name)) {
                    $profile_record = $this->Custom->get_where('doctor_profile', array('user_id' => $user_id));
                    if ($profile_record[0]->clinic_name != $clinic_name) {
                        $this->response->success = 203;
                        $this->response->message = "Clinic name is not valid.";
                        die(json_encode($this->response));
                    }
                }
                if (!empty($payment_id)) {
                    $payment_record = $this->Custom->get_where('patient_payment', array('doctor_id' => $user_id, 'id' => $payment_id, 'patient_id' => $patient_id));
                    if (empty($payment_record)) {
                        $this->response->success = 203;
                        $this->response->message = "Payment id is not valid.";
                        die(json_encode($this->response));
                    }
                }
                if (!empty($prescription_id)) {
                    $prescription_record = $this->Custom->get_where('prescription', array('doctor_id' => $user_id, 'id' => $prescription_id, 'patient_id' => $patient_id));
                    if (empty($prescription_record)) {
                        $this->response->success = 203;
                        $this->response->message = "Prescription id is not valid.";
                        die(json_encode($this->response));
                    }
                }
                if (!empty($next_booking_date)) {
                    if (empty($start_time) || empty($end_time)) {
                        $this->response->success = 201;
                        $this->response->message = $this->lang->line('required_field');
                        die(json_encode($this->response));
                    }
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'patient_id' => $patient_id,
                    'visit_date' => $visit_date,
                    'clinic_name' => $clinic_name,
                    'treatment' => $treatment,
                    'patient_complain' => $patient_complain,
                    'doctor_consulatation' => $doctor_consulatation,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'payment_id' => $payment_id,
                    'prescription_id' => $prescription_id,
                    'next_booking_date' => $next_booking_date,
                    'send_sms' => $send_sms,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('record_visit', $insertArr);
                if ($insert_id) {
                    if (!empty($next_booking_date)) {
                        $insertArr = array(
                            'doctor_id' => $user_id,
                            'patient_id' => $patient_id,
                            'start_time' => $start_time,
                            'end_time' => $end_time,
                            'consultant_id' => 0,
                            'notes' => '',
                            'booking_date' => $next_booking_date,
                            'send_sms' => $send_sms,
                            'created_at' => strtotime(date("Y-m-d H:i:s"))
                        );
                        $insert_id = $this->Custom->insert_data('doctor_appointment', $insertArr);
                    }
                    $this->response->success = 200;
                    $this->response->message = 'Record visit created successfully.';
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function AddPatientDocument() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $image = ($_FILES['image']) ? $_FILES['image'] : "";
        if (!empty($user_id) && !empty($patient_id) && !empty($image)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('patients', array('id' => $patient_id));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                if (!empty($_FILES['image']['name'])) {
                    $name = $_FILES['image']['name'];
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

                    if (!$this->upload->do_upload('image')) {
                        $this->response->success = 203;
                        $this->response->message = 'Document not uploaded.';
                        die(json_encode($this->response));
                    } else {
                        $image_name = $new_name;
                    }
                    $insertArr = array(
                        'doctor_id' => $user_id,
                        'patient_id' => $patient_id,
                        'image' => $image_name,
                        'created_at' => strtotime(date("Y-m-d H:i:s"))
                    );
                    $insert_id = $this->Custom->insert_data('patients_document', $insertArr);
                    if ($insert_id) {
                        $patients_document = $this->Custom->get_where('patients_document', array('id' => $insert_id));
                        $patients_document[0]->image = ($patients_document[0]->image) ? base_url() . PATIENT_MEDICAL_DOCUMENT_URL . '/' . $patients_document[0]->image : "";
                        $this->response->success = 200;
                        $this->response->message = 'Document added successfully.';
                        $this->response->data = $patients_document[0];
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

    public function AddPrescription() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        $medicine_id = ($this->input->post('medicine_id')) ? $this->input->post('medicine_id') : '';
        $notes = ($this->input->post('notes')) ? $this->input->post('notes') : '';
        $send_sms = ($this->input->post('send_sms')) ? $this->input->post('send_sms') : '';

        if (!empty($user_id) && !empty($patient_id) && !empty($medicine_id) && !empty($notes)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patients_record = $this->Custom->get_where('users', array('id' => $patient_id, 'role' => 'patient'));
                if (empty($patients_record)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }

                if (!empty($medicine_id)) {
                    $medicineIdArr = explode(',', $medicine_id);
                    foreach ($medicineIdArr as $val) {
                        $medicine_record = $this->Custom->get_where('medicine', array('doctor_id' => $user_id, 'id' => $val));
                        if (empty($medicine_record)) {
                            $this->response->success = 203;
                            $this->response->message = "Medicine id " . $val . " is not valid.";
                            die(json_encode($this->response));
                        }
                    }
                }
                $insertArr = array(
                    'doctor_id' => $user_id,
                    'patient_id' => $patient_id,
                    'medicine_id' => $medicine_id,
                    'notes' => $notes,
                    'send_sms' => $send_sms,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('prescription', $insertArr);
                if ($insert_id) {
                    $prescription_record = $this->Custom->get_where('prescription', array('id' => $insert_id));
                    $medicineIdArr = explode(',', $prescription_record[0]->medicine_id);
                    foreach ($medicineIdArr as $val) {
                        $medicine_record = $this->Custom->get_where('medicine', array('id' => $val));
                        if ($medicine_record) {
                            $medicine = array(
                                'medicine_id' => $val,
                                'medicine_name' => $medicine_record[0]->medicine_name,
                                'dosage' => $medicine_record[0]->dosage,
                                'frequency' => $medicine_record[0]->frequency,
                                'duration' => $medicine_record[0]->duration,
                                'meal' => $medicine_record[0]->meal,
                                'insruction' => $medicine_record[0]->insruction
                            );
                        } else {
                            $medicine = array(
                                'medicine_id' => $val,
                                'medicine_name' => "",
                                'dosage' => "",
                                'frequency' => "",
                                'duration' => "",
                                'meal' => "",
                                'insruction' => ""
                            );
                        }
                        $medicines[] = $medicine;
                    }
                    $prescription_record[0]->patient_name = $patients_record[0]->name;
                    $prescription_record[0]->medicine = $medicines;

                    $this->response->success = 200;
                    $this->response->message = 'Prescription added successfully.';
                    $this->response->data = $prescription_record[0];
                    die(json_encode($this->response));
                } else {
                    $this->response->success = 202;
                    $this->response->message = $this->lang->line('went_wrong');
                    die(json_encode($this->response));
                }
            } else {
                $this->response->success = 203;
                $this->response->message = $this->lang->line('doctor_id_error');
                die(json_encode($this->response));
            }
        } else {
            $this->response->success = 201;
            $this->response->message = $this->lang->line('required_field');
            die(json_encode($this->response));
        }
    }

    public function GetPatientPrescriptions() {
        $user_id = ($this->input->post('user_id')) ? $this->input->post('user_id') : '';
        $patient_id = ($this->input->post('patient_id')) ? $this->input->post('patient_id') : '';
        if (!empty($user_id) && !empty($patient_id)) {
            $user_record = $this->Custom->get_where('users', array('id' => $user_id, 'role' => 'doctor'));
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
                if ($user_record[0]->status == 0 && $user_record[0]->number_verified == 0) {
                    $this->response->success = 204;
                    $this->response->message = $this->lang->line('email_phone_no_not_verified');
                    die(json_encode($this->response));
                }
                $patient_details = $this->Custom->get_where('users', array('id' => $patient_id, 'role' => 'patient'));
                if (empty($patient_details)) {
                    $this->response->success = 203;
                    $this->response->message = $this->lang->line('patient_id_error');
                    die(json_encode($this->response));
                }
                $prescription_details = $this->Custom->get_where('prescription', array('patient_id' => $patient_id, 'doctor_id' => $user_id));
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

}
