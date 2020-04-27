<?php

class DoctorModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetDoctorProfile($user_id = NULL, $where = NULL) {
        $this->db->select('users.id as user_id,users.user_uid,users.role,users.title, users.name, users.email,users.country_code, users.phone_number, users.device_type, users.device_token,users.latitude, users.longitude, users.authentication_token,users.status,users.activate_status, users.feature_status,users.feature_date,users.approve_status, users.created_at,doctor_profile.dob,doctor_profile.language,doctor_profile.gender,doctor_profile.profile_image,doctor_profile.professional_level, doctor_profile.speciality,doctor_profile.diseases,doctor_profile.treatment,doctor_profile.experience,doctor_profile.professional_qualification,doctor_profile.city,doctor_profile.id_number,doctor_profile.consulation_fee,doctor_profile.clinic_name,doctor_profile.clinic_phone_number,doctor_profile.clinic_address,doctor_profile.clinic_city, doctor_profile.clinic_locality,doctor_profile.clinic_pincode,doctor_profile.clinic_state, doctor_profile.clinic_lat, doctor_profile.clinic_lng, doctor_profile.availability, doctor_profile.medical_registration_proof, doctor_profile.photo_id_proof, doctor_profile.degree_proof, doctor_profile.signature, doctor_profile.personal_address');
        $this->db->from('users');
        $this->db->join('doctor_profile', 'users.id = doctor_profile.user_id');
        if ($where)
            $this->db->where($where);
        if ($user_id)
            $this->db->where('users.id', $user_id);
        $this->db->where('users.role', 'doctor');
        $this->db->where('users.activate_status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function GetClinicHours($user_id) {
        $this->db->select('*');
        $this->db->from('clinic_hours');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('day_id', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function DeleteDocument($id, $key_name) {
        $images = $this->Custom->get_where('doctor_profile', array('id' => $id));
        if ($images) {
            unlink(DOCTOR_DOCUMENT_PATH . '/' . $images[0]->$key_name);
            $x = "";
            return $this->Custom->update_where('doctor_profile', array($key_name => $x), array('id' => $id));
        } else {
            return false;
        }
    }

    public function GetFeeds($user_id, $type, $offset, $per_page) {
        $this->db->select('doctor_feed.*, feed_category.category_name_en, feed_category.category_name_ru, users.title, users.name, doctor_profile.profile_image');
        $this->db->from('doctor_feed');
        $this->db->join('feed_category', 'doctor_feed.feed_category_id = feed_category.id');
        $this->db->join('users', 'doctor_feed.user_id = users.id');
        $this->db->join('doctor_profile', 'users.id = doctor_profile.user_id');
        if ($type == 'by_user')
            $this->db->where('doctor_feed.user_id', $user_id);
        if ($type == 'by_other')
            $this->db->where('doctor_feed.user_id !=', $user_id);
        $this->db->where('doctor_feed.status', 1);
        $this->db->order_by('doctor_feed.id', 'DESC');
        $this->db->limit($per_page, $offset);
        $query = $this->db->get();
        return $query->result();
    }

    public function GetPatientProfile($user_id) {
        $this->db->select('users.id as user_id,users.role,users.name, users.email,users.country_code, users.phone_number, users.device_type, users.device_token,users.latitude, users.longitude, users.authentication_token,users.status,users.created_at,patient_profile.gender, patient_profile.dob, patient_profile.height, patient_profile.weight, patient_profile.bmi,patient_profile.language, patient_profile.blood_group,patient_profile.emergency_phone_number,patient_profile.address,patient_profile.building_name, patient_profile.street, patient_profile.town, patient_profile.zipcode, patient_profile.profile_image,patient_medical_lifestyle.allergies,patient_medical_lifestyle.current_medication,patient_medical_lifestyle.past_medication,patient_medical_lifestyle.diseases,patient_medical_lifestyle.injuries,patient_medical_lifestyle.surgeries,patient_medical_lifestyle.smoking_habit,patient_medical_lifestyle.alcohol_consumption,patient_medical_lifestyle.activity_level,patient_medical_lifestyle.food_preference,patient_medical_lifestyle.occupation,patient_medical_lifestyle.specialNeeds,patient_medical_lifestyle.bloodTransfusion');
        $this->db->from('users');
        $this->db->join('patient_profile', 'users.id = patient_profile.user_id');
        $this->db->join('patient_medical_lifestyle', 'patient_profile.user_id = patient_medical_lifestyle.user_id');
        $this->db->where('users.id', $user_id);
        $this->db->where('users.role', 'patient');
        $query = $this->db->get();
        return $query->result();
    }

}
