<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->response = new stdClass();
        ini_set("display_errors", 0);
        error_reporting(0);
        date_default_timezone_set("UTC");
        $this->load->model('AdminModel');
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

    public function index() {
        if (isset($_POST['login_sub_btn'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $admin_record = $this->Custom->get_where('admin_details', array('email' => $email, 'password' => md5($password)));
            if (!empty($admin_record)) {
                $this->session->set_userdata('admin_data', $admin_record);
                redirect(base_url() . 'Dashboard');
            } else {
                $this->session->set_flashdata('error_msg', "Email/Password is not correct.");
                redirect(base_url() . 'Admin');
            }
        } else {
            $this->load->view('admin/login');
        }
    }

    public function Logout() {
        $this->session->sess_destroy();
        redirect(base_url() . 'Admin');
    }

    public function Dashboard() {
        CheckAdminLogin();
        $data['title'] = 'Dashboard';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('admin/footer', $data);
    }

    public function Profile() {
        CheckAdminLogin();
        $data['title'] = 'Profile';
        $session_data = $this->session->userdata('admin_data');
        $admin_details = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['admin_details'] = $admin_details;
        if (isset($_POST['update_btn'])) {
            $update_arr = array(
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone_number' => $_POST['phone_number']
            );
            $update_status = $this->Custom->update_where('admin_details', $update_arr, array('id' => $admin_details[0]->id));
            if ($update_status) {
                $this->session->set_flashdata('success_msg', 'Profile updated successfully.');
                redirect(base_url() . 'Profile');
            } else {
                $this->session->set_flashdata('error_msg', 'Something went wrong.');
                redirect(base_url() . 'Profile');
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/update_profile', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function ChangePassword() {
        CheckAdminLogin();
        $data['title'] = 'Change Password';
        $session_data = $this->session->userdata('admin_data');
        $admin_details = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['admin_details'] = $admin_details;
        if (isset($_POST['update_btn'])) {
            if (md5($_POST['old_pass']) != $admin_details[0]->password) {
                $this->session->set_flashdata('error_msg', "Old Password doesn't match.");
                redirect(base_url() . 'ChangePassword');
            }
            if ($_POST['new_pass'] != $_POST['confirm_pass']) {
                $this->session->set_flashdata('error_msg', "Password & confirm password doesn't match.");
                redirect(base_url() . 'ChangePassword');
            }
            $update_arr = array(
                'password' => md5($_POST['new_pass'])
            );
            $update_status = $this->Custom->update_where('admin_details', $update_arr, array('id' => $admin_details[0]->id));
            if ($update_status) {
                $this->session->set_flashdata('success_msg', 'Password changed successfully.');
                redirect(base_url() . 'ChangePassword');
            } else {
                $this->session->set_flashdata('error_msg', 'Something went wrong.');
                redirect(base_url() . 'ChangePassword');
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/change_password', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function ForgotPassword() {
        $email = $_POST['email'];
        $admin_details = $this->Custom->get_where('admin_details', array('email' => $email));
        if ($admin_details) {
            $password = GenerateRandomNumber(6);
            $update_status = $this->Custom->update_where('admin_details', array('password' => md5($password)), array('id' => $admin_details[0]->id));
            if ($update_status) {
                $mail_data = array('password' => $password);
                $content = $this->load->view('mail/admin_forgot_mail', $mail_data, TRUE);
                $subject = 'Forgot Password - Zumcare App';

                $config['protocol'] = 'sendmail';
                $config['mailtype'] = 'html';
                $config['mailpath'] = '/usr/sbin/sendmail';
                $config['charset'] = 'utf-8';
                $config['wordwrap'] = TRUE;

                $this->email->initialize($config);

                $this->email->from('help@mobi.doctor', 'zumcare App');
                $this->email->to($email);
                $this->email->subject($subject);
                $this->email->message($content);
                $this->email->send();

                $this->session->set_flashdata('modal_success_msg', 'New password send on your email.');
                redirect(base_url() . 'Admin');
            } else {
                $this->session->set_flashdata('modal_error_msg', 'Something went wrong.');
                redirect(base_url() . 'Admin');
            }
        } else {
            $this->session->set_flashdata('modal_error_msg', "Email is not valid.");
            redirect(base_url() . 'Admin');
        }
    }

    /*     * ****** Speciality Management ********* */

    public function Speciality() {
        CheckAdminLogin();
        $data['title'] = 'Speciality List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['specility_details'] = $this->Custom->get_where('specility', array('parent_id' => 0));
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/speciality/speciality_list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function AddSpeciality() {
        CheckAdminLogin();
        $data['title'] = 'Add Speciality';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));

        if (isset($_POST['add_speciality_btn'])) {
            $this->form_validation->set_rules('speciality_name', 'Speciality Name', 'trim|required|is_unique[specility.specility_name]', array('is_unique' => 'Speciality Name Already Exists.'));
            $this->form_validation->set_rules('specility_name_ru', 'Speciality Name', 'trim|required|is_unique[specility.specility_name_ru]', array('is_unique' => 'Speciality Name Already Exists.'));

            if ($this->form_validation->run() == false) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/speciality/add_speciality', $data);
                $this->load->view('admin/footer', $data);
            } else {
                if (!empty($_FILES['image']['name'])) {
                    $name = $_FILES['image']['name'];
                    $ext = end((explode(".", $name)));
                    $new_name = time() . '.' . $ext;
                    $config['file_name'] = $new_name;
                    $config['upload_path'] = SPECIALITY_PATH . '/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $config['remove_spaces'] = TRUE;

                    if (!is_dir($config['upload_path']))
                        die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('image')) {
                        $this->session->set_flashdata('error_msg', "Image not uploaded.");
                        redirect(base_url() . 'AddSpeciality');
                    } else {
                        $image = $new_name;
                    }
                } else {
                    $image = "";
                }
                $insert_arr = array(
                    'specility_name' => $_POST['speciality_name'],
                    'specility_name_ru' => $_POST['specility_name_ru'],
                    'image' => $image,
                    'parent_id' => 0,
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('specility', $insert_arr);
                if ($insert_id) {
                    $this->session->set_flashdata('success_msg', "Speciality added successfully.");
                    redirect(base_url() . 'Speciality');
                } else {
                    $this->session->set_flashdata('error_msg', "Something went wrong.");
                    redirect(base_url() . 'AddSpeciality');
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/speciality/add_speciality', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function UpdateSpeciality() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $data['title'] = 'Update Speciality';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $specility_details = $this->Custom->get_where('specility', array('parent_id' => 0, 'id' => $id));
        $data['specility_details'] = $specility_details;
        if (isset($_POST['edit_speciality_sub'])) {
            //set validation
            $this->form_validation->set_rules("speciality_name", "Speciality Name", "trim|required");

            $original_speciality_name = $this->db->query("SELECT specility_name FROM specility WHERE id = " . $id)->row()->specility_name;
            if ($this->input->post('speciality_name') != $original_speciality_name) {
                $is_unique = 'error';
            } else {
                $is_unique = '';
            }
            $original_specility_name_ru = $this->db->query("SELECT specility_name_ru FROM specility WHERE id = " . $id)->row()->specility_name_ru;
            if ($this->input->post('specility_name_ru') != $original_specility_name_ru) {
                $is_unique1 = 'error';
            } else {
                $is_unique1 = '';
            }

            if ($is_unique != '') {
                $this->form_validation->set_rules('speciality_name', 'Speciality Name', 'trim|required|is_unique[specility.specility_name]', array('is_unique' => 'Speciality Name Already Exists.'));
            } else {
                $this->form_validation->set_rules('speciality_name', 'Speciality Name', 'trim|required');
            }
            if ($is_unique1 != '') {
                $this->form_validation->set_rules('specility_name_ru', 'Speciality Name', 'trim|required|is_unique[specility.specility_name_ru]', array('is_unique' => 'Speciality Name Already Exists.'));
            } else {
                $this->form_validation->set_rules('specility_name_ru', 'Speciality Name', 'trim|required');
            }

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/speciality/edit_speciality', $data);
                $this->load->view('admin/footer', $data);
            } else {
                if (!empty($_FILES['image']['name'])) {
                    $name = $_FILES['image']['name'];
                    $ext = end((explode(".", $name)));
                    $new_name = time() . '.' . $ext;
                    $config['file_name'] = $new_name;
                    $config['upload_path'] = SPECIALITY_PATH . '/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $config['remove_spaces'] = TRUE;

                    if (!is_dir($config['upload_path']))
                        die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('image')) {
                        $this->session->set_flashdata('error_msg', "Image not uploaded.");
                        redirect(base_url() . 'AddSpeciality');
                    } else {
                        if ($specility_details[0]->image != "")
                            unlink(SPECIALITY_PATH . '/' . $specility_details[0]->image);
                        $image = $new_name;
                    }
                } else {
                    $image = $specility_details[0]->image;
                }

                $update_arr = array(
                    'specility_name' => $_POST['speciality_name'],
                    'specility_name_ru' => $_POST['specility_name_ru'],
                    'image' => $image
                );
                $update_status = $this->Custom->update_where('specility', $update_arr, array('id' => $id));
                if ($update_status) {
                    $this->session->set_flashdata('success_msg', 'Specility updated successfully.');
                    redirect(base_url() . 'Speciality');
                } else {
                    $this->session->set_flashdata('error_msg', 'Something went wrong.');
                    redirect(base_url() . 'UpdateSpecility/' . $id);
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/speciality/edit_speciality', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function DeleteSpeciality($id) {
        $specility_details = $this->Custom->get_where('specility', array('parent_id' => 0, 'id' => $id));
        if (!empty($specility_details)) {
            $del_status = $this->Custom->delete_where('specility', array('id' => $id));
            if ($del_status) {
                $this->Custom->delete_where('specility', array('parent_id' => $id));
                $this->session->set_flashdata('success_msg', "Speciality deleted successfully.");
                redirect(base_url() . 'Speciality');
            } else {
                $this->session->set_flashdata('error_msg', "Something went wrong.");
                redirect(base_url() . 'Speciality');
            }
        } else {
            $this->session->set_flashdata('error_msg', "Speciality is not valid.");
            redirect(base_url() . 'Speciality');
        }
    }

    /*     * ****** Diseases Management ********* */

    public function Diseases() {
        CheckAdminLogin();
        $data['title'] = 'Diseases List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['diseases_details'] = $this->Custom->get_where('diseases');
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/diseases/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function AddDiseases() {
        CheckAdminLogin();
        $data['title'] = 'Add Diseases';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));

        if (isset($_POST['add_diseases_btn'])) {
            $this->form_validation->set_rules('diseases_name', 'Diseases Name', 'trim|required|is_unique[diseases.diseases_name]', array('is_unique' => 'Diseases Name Already Exists.'));

            if ($this->form_validation->run() == false) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/diseases/add', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $insert_arr = array(
                    'diseases_name' => $_POST['diseases_name'],
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('diseases', $insert_arr);
                if ($insert_id) {
                    $this->session->set_flashdata('success_msg', "Diseases added successfully.");
                    redirect(base_url() . 'Diseases');
                } else {
                    $this->session->set_flashdata('error_msg', "Something went wrong.");
                    redirect(base_url() . 'AddDiseases');
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/diseases/add', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function UpdateDiseases() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $data['title'] = 'Update Diseases';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $diseases_details = $this->Custom->get_where('diseases', array('id' => $id));
        $data['diseases_details'] = $diseases_details;
        if (isset($_POST['edit_diseases_sub'])) {
            //set validation
            $this->form_validation->set_rules("diseases_name", "Diseases Name", "trim|required");

            $original_diseases_name = $this->db->query("SELECT diseases_name FROM diseases WHERE id = " . $id)->row()->diseases_name;
            if ($this->input->post('diseases_name') != $original_diseases_name) {
                $is_unique = 'error';
            } else {
                $is_unique = '';
            }

            if ($is_unique != '') {
                $this->form_validation->set_rules('diseases_name', 'Diseases Name', 'trim|required|is_unique[diseases.diseases_name]', array('is_unique' => 'Diseases Name Already Exists.'));
            } else {
                $this->form_validation->set_rules('diseases_name', 'Diseases Name', 'trim|required');
            }

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/diseases/edit', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $update_arr = array(
                    'diseases_name' => $_POST['diseases_name']
                );
                $update_status = $this->Custom->update_where('diseases', $update_arr, array('id' => $id));
                if ($update_status) {
                    $this->session->set_flashdata('success_msg', 'Diseases updated successfully.');
                    redirect(base_url() . 'Diseases');
                } else {
                    $this->session->set_flashdata('error_msg', 'Something went wrong.');
                    redirect(base_url() . 'UpdateDiseases/' . $id);
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/diseases/edit', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    /*     * ****** Sub Speciality Management ********* */

    public function SubSpeciality() {
        CheckAdminLogin();
        $data['title'] = 'Sub Speciality List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['sub_specility_details'] = $this->AdminModel->GetSubSpecialityData();
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/sub_speciality/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function AddSubSpeciality() {
        CheckAdminLogin();
        $data['title'] = 'Add Sub Speciality';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['specility_list'] = $this->Custom->get_where('specility', array('parent_id' => 0));

        if (isset($_POST['add_sub_speciality_btn'])) {
            $this->form_validation->set_rules('speciality_id', 'Speciality Name', 'trim|required');
            $this->form_validation->set_rules('speciality_name', 'Sub Speciality Name', 'trim|required|is_unique[specility.specility_name]', array('is_unique' => 'Sub Speciality Name Already Exists.'));

            if ($this->form_validation->run() == false) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/sub_speciality/add_subspeciality', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $insert_arr = array(
                    'specility_name' => $_POST['speciality_name'],
                    'parent_id' => $_POST['speciality_id'],
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('specility', $insert_arr);
                if ($insert_id) {
                    $this->session->set_flashdata('success_msg', "Sub Speciality added successfully.");
                    redirect(base_url() . 'SubSpeciality');
                } else {
                    $this->session->set_flashdata('error_msg', "Something went wrong.");
                    redirect(base_url() . 'AddSubSpeciality');
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/sub_speciality/add_subspeciality', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function UpdateSubSpeciality() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $data['title'] = 'Update Sub Speciality';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['specility_list'] = $this->Custom->get_where('specility', array('parent_id' => 0));
        $sub_specility_details = $this->AdminModel->GetSubSpecialityData(array('s.id' => $id));
        $data['sub_specility_details'] = $sub_specility_details;
        if (isset($_POST['edit_sub_speciality_sub'])) {
            //set validation
            $this->form_validation->set_rules('speciality_id', 'Speciality Name', 'trim|required');
            $this->form_validation->set_rules("speciality_name", "Speciality Name", "trim|required");

            $original_speciality_name = $this->db->query("SELECT specility_name FROM specility WHERE id = " . $id)->row()->specility_name;
            if ($this->input->post('speciality_name') != $original_speciality_name) {
                $is_unique = 'error';
            } else {
                $is_unique = '';
            }

            if ($is_unique != '') {
                $this->form_validation->set_rules('speciality_name', 'Speciality Name', 'trim|required|is_unique[specility.specility_name]', array('is_unique' => 'Sub Speciality Name Already Exists.'));
            } else {
                $this->form_validation->set_rules('speciality_name', 'Speciality Name', 'trim|required');
            }

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/sub_speciality/edit_subspeciality', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $update_arr = array(
                    'specility_name' => $_POST['speciality_name'],
                    'parent_id' => $_POST['speciality_id']
                );
                $update_status = $this->Custom->update_where('specility', $update_arr, array('id' => $id));
                if ($update_status) {
                    $this->session->set_flashdata('success_msg', 'Sub Specility updated successfully.');
                    redirect(base_url() . 'SubSpeciality');
                } else {
                    $this->session->set_flashdata('error_msg', 'Something went wrong.');
                    redirect(base_url() . 'UpdateSubSpecility/' . $id);
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/sub_speciality/edit_subspeciality', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    /*     * ****** Speciality Management ********* */

    public function Treatment() {
        CheckAdminLogin();
        $data['title'] = 'Treatment List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['treatment_details'] = $this->Custom->get_where('treatment');
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/treatment/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function AddTreatment() {
        CheckAdminLogin();
        $data['title'] = 'Add Treatment';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));

        if (isset($_POST['add_btn'])) {
            $this->form_validation->set_rules('name', 'Treatment Name', 'trim|required|is_unique[treatment.name]', array('is_unique' => 'Treatment Name Already Exists.'));

            if ($this->form_validation->run() == false) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/treatment/add', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $insert_arr = array(
                    'name' => $_POST['name'],
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('treatment', $insert_arr);
                if ($insert_id) {
                    $this->session->set_flashdata('success_msg', "Treatment added successfully.");
                    redirect(base_url() . 'Treatment');
                } else {
                    $this->session->set_flashdata('error_msg', "Something went wrong.");
                    redirect(base_url() . 'AddTreatment');
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/treatment/add', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function UpdateTreatment() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $data['title'] = 'Update Treatment';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $treatment_details = $this->Custom->get_where('treatment', array('id' => $id));
        $data['treatment_details'] = $treatment_details;
        if (isset($_POST['edit_sub'])) {
            //set validation
            $this->form_validation->set_rules("name", "Treatment Name", "trim|required");

            $original_name = $this->db->query("SELECT name FROM treatment WHERE id = " . $id)->row()->name;
            if ($this->input->post('name') != $original_name) {
                $is_unique = 'error';
            } else {
                $is_unique = '';
            }

            if ($is_unique != '') {
                $this->form_validation->set_rules('name', 'Treatment Name', 'trim|required|is_unique[treatment.name]', array('is_unique' => 'Treatment Name Already Exists.'));
            } else {
                $this->form_validation->set_rules('name', 'Treatment Name', 'trim|required');
            }

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/treatment/edit', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $update_arr = array(
                    'name' => $_POST['name']
                );
                $update_status = $this->Custom->update_where('treatment', $update_arr, array('id' => $id));
                if ($update_status) {
                    $this->session->set_flashdata('success_msg', 'Treatment updated successfully.');
                    redirect(base_url() . 'Treatment');
                } else {
                    $this->session->set_flashdata('error_msg', 'Something went wrong.');
                    redirect(base_url() . 'UpdateTreatment/' . $id);
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/treatment/edit', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    /*     * ****** Doctor Management ********* */

    public function Doctors() {
        CheckAdminLogin();
        $data['title'] = 'Doctors List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $doctor_data = $this->Custom->query("select users.*, doctor_profile.dob,doctor_profile.profile_image, doctor_profile.speciality from users INNER JOIN doctor_profile ON users.id = doctor_profile.user_id where users.role = 'doctor' ORDER BY id DESC");
        if ($doctor_data) {
            foreach ($doctor_data as $row) {
                $row->profile_image = ($row->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $row->profile_image : "";
                if ($row->speciality != '') {
                    $speciality_name = "";
                    $speciality = explode(',', $row->speciality);
                    foreach ($speciality as $spe) {
                        $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                        if ($specility_record)
                            $speciality_name[] = $specility_record[0]->specility_name;
                        else
                            $speciality_name[] = "";
                    }
                    $row->speciality_name = ($speciality_name) ? implode(',', $speciality_name) : "";
                }else {
                    $row->speciality_name = "N/A";
                }
            }
        }
        $data['doctor_data'] = $doctor_data;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/doctor/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function DoctorDetail() {
        CheckAdminLogin();
        $this->load->model('DoctorModel');
        $id = $this->uri->segment(2);
        $data['title'] = 'Doctor Detail';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $doctor_data = $this->DoctorModel->GetDoctorProfile($id);
        if ($doctor_data[0]->speciality != '') {
            $speciality = explode(',', $doctor_data[0]->speciality);
            foreach ($speciality as $spe) {
                $specility_record = $this->Custom->get_where('specility', array('id' => $spe));
                if ($specility_record)
                    $speciality_name[] = $specility_record[0]->specility_name;
                else
                    $speciality_name[] = "";
            }
            $doctor_data[0]->speciality_name = implode(',', $speciality_name);
        }else {
            $doctor_data[0]->speciality_name = array();
        }
        if ($doctor_data[0]->diseases != '') {
            $doctor_data[0]->diseases_name = "";
        } else {
            $doctor_data[0]->diseases_name = "";
        }
        $education_details = GetDetails('doctor_education', array('user_id' => $id));
        $consultation_settings = GetDetails('consultation_settings', array('user_id' => $id));
        $doctor_data[0]->profile_image = ($doctor_data[0]->profile_image) ? base_url() . DOCTOR_PROFILE_URL . '/' . $doctor_data[0]->profile_image : "";
        $doctor_data[0]->medical_registration_proof = ($doctor_data[0]->medical_registration_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->medical_registration_proof : "";
        $doctor_data[0]->degree_proof = ($doctor_data[0]->degree_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->degree_proof : "";
        $doctor_data[0]->photo_id_proof = ($doctor_data[0]->photo_id_proof) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->photo_id_proof : "";
        $doctor_data[0]->signature = ($doctor_data[0]->signature) ? base_url() . DOCTOR_DOCUMENT_URL . '/' . $doctor_data[0]->signature : "";
        $doctor_data[0]->education_details = ($education_details) ? $education_details : array();
        $doctor_data[0]->consultation_settings = $consultation_settings[0];
        $data['doctor_data'] = $doctor_data;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/doctor/details', $data);
        $this->load->view('admin/footer', $data);
    }

    public function ChangeDoctorAccountStatus() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $status = $this->uri->segment(3);
        if ($status == 1)
            $new_status = 0;
        if ($status == 0)
            $new_status = 1;
        $update_status = $this->Custom->update_where('users', array('activate_status' => $new_status), array('id' => $id));
        if ($update_status) {
            $this->session->set_flashdata('success_msg', 'Account status updated successfully.');
            redirect(base_url() . 'Doctors');
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'Doctors');
        }
    }

    public function ChangeDoctorApproveStatus() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $status = $this->uri->segment(3);
        $update_status = $this->Custom->update_where('users', array('approve_status' => $status), array('id' => $id));
        if ($update_status) {
            if ($status == 1) {
                $doctor_data = $this->Custom->get_where('users', array('id' => $id));
                //send mail
                $Hello = $this->CreateMessageForNotification('ru', 'congrates');
                $approval_content = $this->CreateMessageForNotification('ru', 'approval_content');
                $Thanks = $this->CreateMessageForNotification('ru', 'Thanks');
                $team = $this->CreateMessageForNotification('ru', 'team');

                $mail_data = array('Hello' => $Hello, 'approval_content' => $approval_content, 'Thanks' => $Thanks, 'team' => $team);
                $content = $this->load->view('mail/doctor_approved', $mail_data, TRUE);
                $subject = $this->CreateMessageForNotification('ru', 'approval_subject');
                $this->SendMail($doctor_data[0]->email, $subject, $content);
            }

            $this->session->set_flashdata('success_msg', 'Approve status updated successfully.');
            redirect(base_url() . 'Doctors');
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'Doctors');
        }
    }

    public function ChangeDoctorFeatureStatus() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $feature_status = $_POST['feature_status'];
        if ($feature_status == 'on')
            $feature_status1 = 1;
        else
            $feature_status1 = 0;

        $update_status = $this->Custom->update_where('users', array('feature_status' => $feature_status1, 'feature_date' => date('Y-m-d')), array('id' => $id));
        if ($update_status) {
            $this->session->set_flashdata('success_msg', 'Featured status updated successfully.');
            redirect(base_url() . 'Doctors');
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'Doctors');
        }
    }

    /*     * ****** Patient Management ********* */

    public function Patients() {
        CheckAdminLogin();
        $data['title'] = 'Patients List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $patient_data = $this->Custom->query("select users.*, patient_profile.dob,patient_profile.profile_image from users INNER JOIN patient_profile ON users.id = patient_profile.user_id where users.role = 'patient'");
        if ($patient_data) {
            foreach ($patient_data as $row) {
                $row->profile_image = ($row->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $row->profile_image : "";
            }
        }
        $data['patient_data'] = $patient_data;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/patient/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function ChangePatientAccountStatus() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $status = $this->uri->segment(3);
        if ($status == 1)
            $new_status = 0;
        if ($status == 0)
            $new_status = 1;
        $update_status = $this->Custom->update_where('users', array('activate_status' => $new_status), array('id' => $id));
        if ($update_status) {
            $this->session->set_flashdata('success_msg', 'Account status updated successfully.');
            redirect(base_url() . 'Patients');
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'Patients');
        }
    }

    public function PatientDetail() {
        CheckAdminLogin();
        $data['title'] = 'Patient Detail';
        $id = $this->uri->segment(2);
        $this->load->model('PatientModel');
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $patient_data = $this->PatientModel->GetPatientProfile($id);
        $patient_data[0]->profile_image = ($patient_data[0]->profile_image) ? base_url() . PATIENT_PROFILE_URL . '/' . $patient_data[0]->profile_image : "";
        $data['patient_data'] = $patient_data;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/patient/details', $data);
        $this->load->view('admin/footer', $data);
    }

    /*     * ****** Payment Management ********* */

    public function Payment() {
        CheckAdminLogin();
        $data['title'] = 'Payment List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        /* $this->db->select('transaction.*, appointment.user_id, appointment.doctor_id, appointment.appointment_uid, appointment.appointment_date, patient_user.name as patient_name,doctor_user.title, doctor_user.name as doctor_name');
          $this->db->from('transaction');
          $this->db->join('appointment', 'transaction.appointment_id = appointment.id');
          $this->db->join('users as patient_user', 'appointment.user_id = patient_user.id');
          $this->db->join('users as doctor_user', 'appointment.doctor_id = doctor_user.id'); */
        $this->db->select('transaction.*, patient_user.name as patient_name,doctor_user.title, doctor_user.name as doctor_name, cards.bank_name');
        $this->db->from('transaction');
        $this->db->join('users as patient_user', 'transaction.user_id = patient_user.id');
        $this->db->join('users as doctor_user', 'transaction.doctor_id = doctor_user.id');
        $this->db->join('cards', 'transaction.card_id = cards.id');
        $query = $this->db->get();
        $payment_data = $query->result();
        if ($payment_data) {
            foreach ($payment_data as $row) {
                if ($row->type == 'appointment') {
                    $appointment = $this->Custom->get_where('appointment', array('id' => $row->appointment_id));
                    $row->appointment_status = ($appointment) ? $appointment[0]->status : "";
                } else {
                    $row->appointment_status = "";
                }
            }
        }
        $data['payment_data'] = $payment_data;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/payment/list', $data);
        $this->load->view('admin/footer', $data);
    }

    /*     * ****** Report Management ********* */

    public function Report() {
        CheckAdminLogin();
        $data['title'] = 'Report List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $this->db->select('report_issue.*, users.user_uid, users.name, users.email, users.country_code, users.phone_number');
        $this->db->from('report_issue');
        $this->db->join('users', 'report_issue.user_id = users.id');
        $query = $this->db->get();
        $data['report_data'] = $query->result();
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/reports/list', $data);
        $this->load->view('admin/footer', $data);
    }

    /*     * ****** Feed Category ********* */

    public function FeedCategory() {
        CheckAdminLogin();
        $data['title'] = 'Category List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $feed_category = $this->Custom->get_where('feed_category');

        if ($feed_category) {
            foreach ($feed_category as $row) {
                $FeedList = "";
                $NewFeedList = "";
                $FeedList = $this->Custom->query("select * from doctor_feed where feed_category_id = $row->id");
                $NewFeedList = $this->Custom->query("select * from doctor_feed where feed_category_id = $row->id AND status = 0");
                $row->TotalFeedCount = ($FeedList) ? count($FeedList) : 0;
                $row->NewFeedCount = ($NewFeedList) ? count($NewFeedList) : 0;
            }
        }

        $data['feed_category'] = $feed_category;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/category/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function FeedList() {
        CheckAdminLogin();
        $feed_cat_id = $this->uri->segment(2);
        $data['title'] = 'Feed List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $feed_list = $this->Custom->query("select doctor_feed.*, users.name from doctor_feed INNER JOIN users ON doctor_feed.user_id = users.id where users.role = 'doctor' AND doctor_feed.feed_category_id = $feed_cat_id ORDER BY id DESC");

        $data['feed_list'] = $feed_list;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/category/feed_list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function AddFeedCategory() {
        CheckAdminLogin();
        $data['title'] = 'Add Feed Category';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));

        if (isset($_POST['add_btn'])) {
            $this->form_validation->set_rules('category_name_en', 'Category Name', 'trim|required|is_unique[feed_category.category_name_en]', array('is_unique' => 'Category Name Already Exists.'));
            $this->form_validation->set_rules('category_name_ru', 'Category Name', 'trim|required|is_unique[feed_category.category_name_ru]', array('is_unique' => 'Category Name Already Exists.'));

            if ($this->form_validation->run() == false) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/category/add', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $insert_arr = array(
                    'category_name_en' => $_POST['category_name_en'],
                    'category_name_ru' => $_POST['category_name_ru'],
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('feed_category', $insert_arr);
                if ($insert_id) {
                    $this->session->set_flashdata('success_msg', "Category added successfully.");
                    redirect(base_url() . 'FeedCategory');
                } else {
                    $this->session->set_flashdata('error_msg', "Something went wrong.");
                    redirect(base_url() . 'AddFeedCategory');
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/category/add', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function UpdateFeedCategory() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $data['title'] = 'Update Feed Category';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $feed_category = $this->Custom->get_where('feed_category', array('id' => $id));
        $data['feed_category'] = $feed_category;
        if (isset($_POST['edit_sub'])) {
            //set validation
            $this->form_validation->set_rules("category_name_en", "Category Name", "trim|required");
            $this->form_validation->set_rules("category_name_ru", "Category Name", "trim|required");

            $original_category_name_en = $this->db->query("SELECT category_name_en FROM feed_category WHERE id = " . $id)->row()->category_name_en;
            if ($this->input->post('category_name_en') != $original_category_name_en) {
                $is_unique = 'error';
            } else {
                $is_unique = '';
            }
            $original_category_name_ru = $this->db->query("SELECT category_name_ru FROM feed_category WHERE id = " . $id)->row()->category_name_ru;
            if ($this->input->post('category_name_ru') != $original_category_name_ru) {
                $is_unique1 = 'error';
            } else {
                $is_unique1 = '';
            }

            if ($is_unique != '') {
                $this->form_validation->set_rules('category_name_en', 'Category Name', 'trim|required|is_unique[feed_category.category_name_en]', array('is_unique' => 'Category Name Already Exists.'));
            } else {
                $this->form_validation->set_rules("category_name_en", "Category Name", "trim|required");
            }
            if ($is_unique1 != '') {
                $this->form_validation->set_rules('category_name_ru', 'Category Name', 'trim|required|is_unique[feed_category.category_name_ru]', array('is_unique' => 'Category Name Already Exists.'));
            } else {
                $this->form_validation->set_rules("category_name_ru", "Category Name", "trim|required");
            }

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/category/edit', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $update_arr = array(
                    'category_name_en' => $_POST['category_name_en'],
                    'category_name_ru' => $_POST['category_name_ru'],
                );
                $update_status = $this->Custom->update_where('feed_category', $update_arr, array('id' => $id));
                if ($update_status) {
                    $this->session->set_flashdata('success_msg', 'Category updated successfully.');
                    redirect(base_url() . 'FeedCategory');
                } else {
                    $this->session->set_flashdata('error_msg', 'Something went wrong.');
                    redirect(base_url() . 'UpdateFeedCategory/' . $id);
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/category/edit', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function ApproveFeed() {
        CheckAdminLogin();
        $status = $this->uri->segment(2);
        $feed_category_id = $this->uri->segment(3);
        $feed_id = $this->uri->segment(4);
        $doctor_feed = $this->Custom->get_where('doctor_feed', array('id' => $feed_id));
        if (empty($doctor_feed)) {
            $this->session->set_flashdata('error_msg', 'Feed id is not valid.');
            redirect(base_url() . 'FeedList/' . $feed_category_id);
        }
        $update_arr = array(
            'status' => $status,
        );
        $update_status = $this->Custom->update_where('doctor_feed', $update_arr, array('id' => $feed_id));
        if ($update_status) {
            $this->session->set_flashdata('success_msg', 'Feed status updated successfully.');
            redirect(base_url() . 'FeedList/' . $feed_category_id);
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'FeedList/' . $feed_category_id);
        }
    }

    public function FeedDetails() {
        CheckAdminLogin();
        $feed_id = $this->uri->segment(2);
        $data['title'] = 'Feed Details';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $feedData = $this->Custom->query("select doctor_feed.*, users.name from doctor_feed INNER JOIN users ON doctor_feed.user_id = users.id where users.role = 'doctor' AND doctor_feed.id = $feed_id");
        $feed_category_id = $feedData[0]->feed_category_id;
        $feed_category = GetDetails('feed_category', "id=$feed_category_id");
        $feedData[0]->category_name_en = $feed_category[0]->category_name_en;
        $feedData[0]->category_name_ru = $feed_category[0]->category_name_ru;
        $feedData[0]->image = ($feedData[0]->image) ? base_url() . DOCTOR_FEED_URL . '/' . $feedData[0]->image : "";

        $data['feedData'] = $feedData;
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/category/feed_details', $data);
        $this->load->view('admin/footer', $data);
    }

    /*     * ****** Promotion ********* */

    public function Promotion() {
        CheckAdminLogin();
        $data['title'] = 'Promotion List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['promotion'] = $this->Custom->get_where('promotion');
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/promotion/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function AddPromotion() {
        CheckAdminLogin();
        $data['title'] = 'Add Promotion';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));

        if (isset($_POST['add_btn'])) {
            $this->form_validation->set_rules('title', 'Title', 'trim|required');
            $this->form_validation->set_rules('promo_code', 'Promo Code', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim');
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
            $this->form_validation->set_rules('expiry_date', 'Expiry Date', 'trim|required');

            if ($this->form_validation->run() == false) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/promotion/add', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $insert_arr = array(
                    'title' => $_POST['title'],
                    'promo_code' => $_POST['promo_code'],
                    'description' => $_POST['description'],
                    'discount_type' => 'percentage',
                    'amount' => $_POST['amount'],
                    'expiry_date' => $_POST['expiry_date'],
                    'created_at' => strtotime(date("Y-m-d H:i:s"))
                );
                $insert_id = $this->Custom->insert_data('promotion', $insert_arr);
                if ($insert_id) {
                    $this->session->set_flashdata('success_msg', "Promotion added successfully.");
                    redirect(base_url() . 'Promotion');
                } else {
                    $this->session->set_flashdata('error_msg', "Something went wrong.");
                    redirect(base_url() . 'AddPromotion');
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/promotion/add', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function UpdatePromotion() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $data['title'] = 'Update Promotion';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $promotion = $this->Custom->get_where('promotion', array('id' => $id));
        $data['promotion'] = $promotion;
        if (isset($_POST['edit_sub'])) {
            //set validation
            $this->form_validation->set_rules('title', 'Title', 'trim|required');
            $this->form_validation->set_rules('promo_code', 'Promo Code', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim');
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required');
            $this->form_validation->set_rules('expiry_date', 'Expiry Date', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/promotion/edit', $data);
                $this->load->view('admin/footer', $data);
            } else {
                $update_arr = array(
                    'title' => $_POST['title'],
                    'promo_code' => $_POST['promo_code'],
                    'description' => $_POST['description'],
                    'discount_type' => 'percentage',
                    'amount' => $_POST['amount'],
                    'expiry_date' => $_POST['expiry_date'],
                );
                $update_status = $this->Custom->update_where('promotion', $update_arr, array('id' => $id));
                if ($update_status) {
                    $this->session->set_flashdata('success_msg', 'Promotion updated successfully.');
                    redirect(base_url() . 'Promotion');
                } else {
                    $this->session->set_flashdata('error_msg', 'Something went wrong.');
                    redirect(base_url() . 'UpdatePromotion/' . $id);
                }
            }
        } else {
            $this->load->view('admin/header', $data);
            $this->load->view('admin/leftpanel', $data);
            $this->load->view('admin/promotion/edit', $data);
            $this->load->view('admin/footer', $data);
        }
    }

    public function PromoDetails() {
        CheckAdminLogin();
        $data['title'] = 'Promotion List';
        $id = $this->uri->segment(2);
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['promotion'] = $this->Custom->get_where('promotion', array('id' => $id));
        $UsesData = array();
        $appointment = $this->Custom->query("select appointment.appointment_type, appointment.promo_uses_date, users.id as patient_id, users.name as patient_name from appointment INNER JOIN users ON users.id = appointment.user_id where appointment.promo_id = $id");
        if ($appointment) {
            foreach ($appointment as $row) {
                switch ($row->appointment_type):
                    case 0;
                        $service_type = "Appointment(Clinic Visit)";
                        break;
                    case 1;
                        $service_type = "Appointment(Audio Consultation)";
                        break;
                    case 2;
                        $service_type = "Appointment(Video Consultation)";
                        break;
                    case 3;
                        $service_type = "Appointment(Chat Consultation)";
                        break;
                    case 4;
                        $service_type = "Appointment(Home Visit)";
                        break;
                    default :
                        $service_type = "N/A";
                endswitch;
                $UsesData[] = (object) array(
                            'patient_id' => $row->patient_id,
                            'patient_name' => $row->patient_name,
                            'service_type' => $service_type,
                            'promo_uses_date' => $row->promo_uses_date,
                );
            }
        }
        $treatment_enquiry = $this->Custom->query("select treatment_enquiry.promo_uses_date, users.id as patient_id, users.name as patient_name from treatment_enquiry INNER JOIN users ON users.id = treatment_enquiry.patient_id where treatment_enquiry.promo_id = $id");
        if ($treatment_enquiry) {
            foreach ($treatment_enquiry as $row) {
                $UsesData[] = (object) array(
                            'patient_id' => $row->patient_id,
                            'patient_name' => $row->patient_name,
                            'service_type' => 'Enquiry',
                            'promo_uses_date' => $row->promo_uses_date,
                );
            }
        }
        $data['UsesData'] = $UsesData;

        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/promotion/details', $data);
        $this->load->view('admin/footer', $data);
    }

    /*     * ****** App Screen Management ********* */

    public function AppHomeScreenSetting() {
        CheckAdminLogin();
        $data['title'] = 'App Home Screen Setting';
        $session_data = $this->session->userdata('admin_data');
        $type = $this->uri->segment(2);
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $app_home_screen_setting = $this->Custom->get_where('app_home_screen_setting');
        $data['app_home_screen_setting'] = $app_home_screen_setting;
        switch ($type):
            case 'doctors':
                $doctor_data = $this->Custom->query("select * from users where role = 'doctor' AND activate_status = 1 AND approve_status = 1");
                $data['doctor_data'] = $doctor_data;
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/app_screen_setting/doctor_list', $data);
                $this->load->view('admin/footer', $data);
                break;

            case 'speciality':
                $data['specility'] = $this->Custom->query("select * from specility where parent_id = 0");
                $this->load->view('admin/header', $data);
                $this->load->view('admin/leftpanel', $data);
                $this->load->view('admin/app_screen_setting/speciality_list', $data);
                $this->load->view('admin/footer', $data);
                break;
        endswitch;
    }

    public function AddDoctorsForApp() {
        $action_type = $this->uri->segment(2);
        $id = $this->uri->segment(3);
        $app_home_screen_setting = $this->Custom->get_where('app_home_screen_setting');
        if ($action_type == 1) {
            if ($app_home_screen_setting[0]->doctors != "") {
                $doctors = explode(',', $app_home_screen_setting[0]->doctors);
                $count = count($doctors);
                if ($count == 3) {
                    $this->session->set_flashdata('error_msg', '3 Doctors already added for app,please remove one doctor to add new one.');
                    redirect(base_url() . 'AppHomeScreenSetting/doctors');
                }
                array_push($doctors, $id);
                $updateVal = implode(',', $doctors);
            } else {
                $updateVal = $id;
            }
        }
        if ($action_type == 0) {
            if ($app_home_screen_setting[0]->doctors != "") {
                $doctors = explode(',', $app_home_screen_setting[0]->doctors);
                if (in_array($id, $doctors)) {
                    $doctors = array_flip($doctors);
                    unset($doctors[$id]);
                    $doctors = array_flip($doctors);
                }
                $updateVal = implode(',', $doctors);
            } else {
                $updateVal = "";
            }
        }
        $update_status = $this->Custom->update_where('app_home_screen_setting', array('doctors' => $updateVal), array('id' => $app_home_screen_setting[0]->id));
        if ($update_status) {
            if ($action_type == 1)
                $this->session->set_flashdata('success_msg', 'Doctor Added for App successfully.');
            else
                $this->session->set_flashdata('success_msg', 'Doctor removed successfully.');
            redirect(base_url() . 'AppHomeScreenSetting/doctors');
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'AppHomeScreenSetting/doctors');
        }
    }

    public function AddSpecialityForApp() {
        $action_type = $this->uri->segment(2);
        $id = $this->uri->segment(3);
        $app_home_screen_setting = $this->Custom->get_where('app_home_screen_setting');
        if ($action_type == 1) {
            if ($app_home_screen_setting[0]->speciality != "") {
                $speciality = explode(',', $app_home_screen_setting[0]->speciality);
                $count = count($speciality);
                if ($count == 3) {
                    $this->session->set_flashdata('error_msg', '3 Speciality already added for app,please remove one speciality to add new one.');
                    redirect(base_url() . 'AppHomeScreenSetting/speciality');
                }
                array_push($speciality, $id);
                $updateVal = implode(',', $speciality);
            } else {
                $updateVal = $id;
            }
        }
        if ($action_type == 0) {
            if ($app_home_screen_setting[0]->speciality != "") {
                $speciality = explode(',', $app_home_screen_setting[0]->speciality);
                if (in_array($id, $speciality)) {
                    $speciality = array_flip($speciality);
                    unset($speciality[$id]);
                    $speciality = array_flip($speciality);
                }
                $updateVal = implode(',', $speciality);
            } else {
                $updateVal = "";
            }
        }
        $update_status = $this->Custom->update_where('app_home_screen_setting', array('speciality' => $updateVal), array('id' => $app_home_screen_setting[0]->id));
        if ($update_status) {
            if ($action_type == 1)
                $this->session->set_flashdata('success_msg', 'Speciality Added for App successfully.');
            else
                $this->session->set_flashdata('success_msg', 'Speciality removed successfully.');
            redirect(base_url() . 'AppHomeScreenSetting/speciality');
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'AppHomeScreenSetting/speciality');
        }
    }

    /*     * ****** Appointment Management ********* */

    public function Appointment() {
        CheckAdminLogin();
        $data['title'] = 'Appointment List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $this->db->select('appointment.*, pat_users.name as patient_name, doc_users.name as doctor_name');
        $this->db->from('appointment');
        $this->db->join('users as pat_users', 'pat_users.id = appointment.user_id');
        $this->db->join('users as doc_users', 'doc_users.id = appointment.doctor_id');
        $this->db->order_by("id", "DESC");
        $query = $this->db->get();
        //echo $this->db->last_query();
        $appointment_data = $query->result();
        //echo "<pre>";
        //print_r($appointment_data);
        //echo "</pre>";

        $data['appointment_data'] = $appointment_data;

        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/appointment/list', $data);
        $this->load->view('admin/footer', $data);
    }

    /*     * ****** Doctor Earnings ********* */

    public function Earnings() {
        CheckAdminLogin();
        $data['title'] = 'Earnings';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $doctor_data = $this->Custom->query("select * from users where role = 'doctor' ORDER BY id DESC");
        $total_amount = 0;
        $payable_amount = 0;
        $paid_amount = 0;
        if ($doctor_data) {
            foreach ($doctor_data as $row) {
                $total_amount = 0;
                $payable_amount = 0;
                $paid_amount = 0;
                $doctor_id = $row->id;
                $transaction = $this->Custom->query("select * from transaction where doctor_id = $row->id");
                if ($transaction) {
                    foreach ($transaction as $doctor_transaction) {
                        $total_amount = $total_amount + $doctor_transaction->amount;
                        if ($doctor_transaction->type == 'appointment') {
                            $appointment = $this->Custom->query("select sum(amount) as doctor_amount from appointment where doctor_id = $doctor_id AND appointment_uid = '$doctor_transaction->uid' AND ((status = 5 AND status_updated_by = '') OR status = 6 OR status = 7)");
                            $payable_amount = ($appointment[0]->doctor_amount != NULL) ? $payable_amount + $appointment[0]->doctor_amount : $payable_amount;
                        }
                        if ($doctor_transaction->type == 'enquiry') {
                            $payable_amount = $payable_amount + $doctor_transaction->amount;
                        }
                    }
                }
                $payment_history = $this->Custom->query("select sum(amount) as paid_amount from payment_history where doctor_id = $doctor_id");
                $paid_amount = ($payment_history[0]->paid_amount != NULL) ? $payment_history[0]->paid_amount : $paid_amount;
                $row->total_amount = $total_amount;
                $row->payable_amount = $payable_amount;
                $row->paid_amount = $paid_amount;
            }
        }

        $data['doctor_data'] = $doctor_data;

        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/earnings/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function MakePaymentToDoctor() {
        CheckAdminLogin();
        $session_data = $this->session->userdata('admin_data');
        $amount = $_POST['amount'];
        $date = $_POST['date'];
        $doctor_id = $_POST['doctor_id'];
        $insertArr = array(
            'doctor_id' => $doctor_id,
            'amount' => $amount,
            'payment_date' => $date
        );
        $insert_id = $this->Custom->insert_data("payment_history", $insertArr);
        if ($insert_id) {
            $this->session->set_flashdata('success_msg', 'Payment done successfully.');
            redirect(base_url() . 'Earnings');
        } else {
            $this->session->set_flashdata('error_msg', 'Something went wrong.');
            redirect(base_url() . 'Earnings');
        }
    }

    public function EarningsDetails() {
        CheckAdminLogin();
        $doctor_id = $this->uri->segment(2);
        $session_data = $this->session->userdata('admin_data');
        $data['title'] = 'Earnings';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $doctor_data = $this->Custom->query("select * from users where role = 'doctor' AND id = $doctor_id");
        $payment_history = $this->Custom->query("select * from payment_history where doctor_id = $doctor_id");
        $doctor_data[0]->payment_history = ($payment_history) ? $payment_history : array();

        $data['doctor_data'] = $doctor_data[0];

        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/earnings/details', $data);
        $this->load->view('admin/footer', $data);
    }

    /*     * ****** App Banners ********* */

    public function AppBanners() {
        CheckAdminLogin();
        $data['AdminModelData'] = $this->Adminmodel;
        $data['title'] = 'App Banners';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $data['app_banners'] = $this->Custom->get_where('app_banners');
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/app_banner/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function AddAppBanners() {
        CheckAdminLogin();
        $data['title'] = 'App Banners';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        if (isset($_POST['add_btn'])) {
            if (!empty($_FILES['image']['name'])) {
                $name = $_FILES['image']['name'];
                $ext = end((explode(".", $name)));
                $new_name = time() . '.' . $ext;
                $config['file_name'] = $new_name;
                $config['upload_path'] = APP_BANNER_PATH . '/';
                $config['allowed_types'] = '*';
                $config['overwrite'] = TRUE;
                $config['remove_spaces'] = TRUE;

                if (!is_dir($config['upload_path']))
                    die("THE UPLOAD DIRECTORY DOES NOT EXIST");

                $this->load->library('upload', $config);
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('image')) {
                    $this->session->set_flashdata('error_msg', "Image not uploaded.");
                    redirect(base_url() . 'AddAppBanners');
                } else {
                    $image = $new_name;
                }
            } else {
                $this->session->set_flashdata('error_msg', "Please upload image.");
                redirect(base_url() . 'AddAppBanners');
            }
            $insert_arr = array(
                'banner_url' => $_POST['banner_url'],
                'banner_image' => $image,
                'created_at' => strtotime(date('Y-m-d H:i:s'))
            );
            $insert_id = $this->Custom->insert_data('app_banners', $insert_arr);
            if ($insert_id) {
                $this->session->set_flashdata('success_msg', "Banner added successfully.");
                redirect(base_url() . 'AppBanners');
            } else {
                $this->session->set_flashdata('error_msg', "Something went wrong.");
                redirect(base_url() . 'AddAppBanners');
            }
        }
        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/app_banner/add', $data);
        $this->load->view('admin/footer', $data);
    }

    public function DeleteBannerImage() {
        CheckAdminLogin();
        $id = $this->uri->segment(2);
        $app_banners = $this->Custom->get_where('app_banners', array('id' => $id));
        if (!empty($app_banners)) {
            $del_status = $this->Custom->delete_where('app_banners', array('id' => $id));
            if ($del_status) {
                unlink(APP_BANNER_PATH . '/' . $app_banners[0]->banner_image);
                $this->session->set_flashdata('success_msg', "Banner deleted successfully.");
                redirect(base_url() . 'AppBanners');
            } else {
                $this->session->set_flashdata('error_msg', "Something went wrong.");
                redirect(base_url() . 'AppBanners');
            }
        } else {
            $this->session->set_flashdata('error_msg', "Banner is not valid.");
            redirect(base_url() . 'AppBanners');
        }
    }

    public function CreateMessageForNotification($lang, $key) {
        if ($lang == 'en')
            $this->lang->load('message', 'english');
        else if ($lang == 'ru')
            $this->lang->load('message', 'russian');
        else
            $this->lang->load('message', 'english');

        $value = $this->lang->line($key);

        return $value;
    }

    public function UserAppointment() {
        CheckAdminLogin();
        $user_id = $this->uri->segment(2);
        $user_type = $this->uri->segment(3);
        $data['title'] = 'Appointment List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $appointment_data = "";
        switch ($user_type):
            case 'patient':
                $this->db->select('appointment.*, pat_users.name as patient_name, doc_users.name as doctor_name');
                $this->db->from('appointment');
                $this->db->join('users as pat_users', 'pat_users.id = appointment.user_id');
                $this->db->join('users as doc_users', 'doc_users.id = appointment.doctor_id');
                $this->db->where('appointment.user_id', $user_id);
                $this->db->where('pat_users.role', 'patient');
                $this->db->order_by("id", "DESC");
                $query = $this->db->get();
                $appointment_data = $query->result();

                $data['appointment_data'] = $appointment_data;
                break;

            case 'doctor':
                $this->db->select('appointment.*, pat_users.name as patient_name, doc_users.name as doctor_name');
                $this->db->from('appointment');
                $this->db->join('users as pat_users', 'pat_users.id = appointment.user_id');
                $this->db->join('users as doc_users', 'doc_users.id = appointment.doctor_id');
                $this->db->where('appointment.doctor_id', $user_id);
                $this->db->where('doc_users.role', 'doctor');
                $this->db->order_by("id", "DESC");
                $query = $this->db->get();
                $appointment_data = $query->result();
                break;
        endswitch;

        $data['appointment_data'] = $appointment_data;

        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/appointment/list', $data);
        $this->load->view('admin/footer', $data);
    }

    public function EarningAppointment() {
        CheckAdminLogin();
        $user_id = $this->uri->segment(2);
        $data['title'] = 'Appointment List';
        $session_data = $this->session->userdata('admin_data');
        $data['admin_details'] = $this->Custom->get_where('admin_details', array('id' => $session_data[0]->id));
        $appointment_data = "";
        $type = ($_POST['type']) ? $_POST['type'] : 'total';
        switch ($type):
            case 'total':
                $this->db->select('appointment.*, pat_users.name as patient_name, doc_users.name as doctor_name, transaction.amount');
                $this->db->from('appointment');
                $this->db->join('users as pat_users', 'pat_users.id = appointment.user_id');
                $this->db->join('users as doc_users', 'doc_users.id = appointment.doctor_id');
                $this->db->join('transaction', 'transaction.appointment_id = appointment.id');
                $this->db->where('appointment.doctor_id', $user_id);
                $this->db->where('doc_users.role', 'doctor');
                $this->db->order_by("id", "DESC");
                $query = $this->db->get();
                $appointment_data = $query->result();
                //echo $this->db->last_query(); die();

                $data['appointment_data'] = $appointment_data;
                $data['type_val'] = "Total Amount";
                $data['search_type'] = "total";
                break;

            case 'payable':
                $where = "((appointment.status = 5 AND appointment.status_updated_by = '') OR appointment.status = 6 OR appointment.status = 7)";
                $this->db->select('appointment.*, pat_users.name as patient_name, doc_users.name as doctor_name, transaction.amount');
                $this->db->from('appointment');
                $this->db->join('users as pat_users', 'pat_users.id = appointment.user_id');
                $this->db->join('users as doc_users', 'doc_users.id = appointment.doctor_id');
                $this->db->join('transaction', 'transaction.appointment_id = appointment.id');
                $this->db->where('appointment.doctor_id', $user_id);
                $this->db->where('doc_users.role', 'doctor');
                $this->db->where($where);
                $this->db->order_by("id", "DESC");
                $query = $this->db->get();
                $appointment_data = $query->result();
                $data['appointment_data'] = $appointment_data;
                $data['type_val'] = "Payable Amount";
                $data['search_type'] = "payable";
                break;
        endswitch;


        $this->load->view('admin/header', $data);
        $this->load->view('admin/leftpanel', $data);
        $this->load->view('admin/earnings/earning_amount_list', $data);
        $this->load->view('admin/footer', $data);
    }

}
