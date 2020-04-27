<?php

class PatientModel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function GetPatientProfile($user_id = NULL, $where = NULL) {
        $this->db->select('users.id as user_id,users.user_uid,users.comet_chat_id,users.role,users.name, users.email,users.country_code, users.phone_number, users.device_type, users.device_token,users.latitude, users.longitude, users.authentication_token,users.status,users.activate_status,patient_profile.gender, patient_profile.dob, patient_profile.height, patient_profile.weight, patient_profile.bmi,patient_profile.language, patient_profile.blood_group,patient_profile.emergency_phone_number,patient_profile.address,patient_profile.building_name, patient_profile.street, patient_profile.town, patient_profile.zipcode, patient_profile.profile_image,patient_medical_lifestyle.allergies,patient_medical_lifestyle.current_medication,patient_medical_lifestyle.past_medication,patient_medical_lifestyle.diseases,patient_medical_lifestyle.injuries,patient_medical_lifestyle.surgeries,patient_medical_lifestyle.smoking_habit,patient_medical_lifestyle.alcohol_consumption,patient_medical_lifestyle.activity_level,patient_medical_lifestyle.food_preference,patient_medical_lifestyle.occupation,patient_medical_lifestyle.specialNeeds,patient_medical_lifestyle.bloodTransfusion');
        $this->db->from('users');
        $this->db->join('patient_profile', 'users.id = patient_profile.user_id');
        $this->db->join('patient_medical_lifestyle', 'patient_profile.user_id = patient_medical_lifestyle.user_id');
        if ($where)
            $this->db->where($where);
        if ($user_id)
            $this->db->where('users.id', $user_id);
        $this->db->where('users.role', 'patient');
        $this->db->where('users.status', 1);
        $this->db->where('users.activate_status', 1);
        $query = $this->db->get();
        return $query->result();
    }

    public function SearchDoctors($where = NULL) {
        $this->db->select('users.id as user_id,users.role,users.title, users.name, users.email,users.country_code, users.phone_number, users.device_type, users.device_token,users.latitude, users.longitude, users.authentication_token,users.status,users.activate_status,users.feature_status,users.feature_date,users.approve_status,users.created_at,doctor_profile.dob,doctor_profile.professional_qualification,doctor_profile.gender,doctor_profile.profile_image,doctor_profile.language, doctor_profile.speciality,doctor_profile.diseases,doctor_profile.treatment,doctor_profile.experience,doctor_profile.professional_level,doctor_profile.city,doctor_profile.id_number,doctor_profile.consulation_fee,doctor_profile.clinic_name,doctor_profile.clinic_phone_number,doctor_profile.clinic_address,doctor_profile.clinic_city, doctor_profile.clinic_locality,doctor_profile.clinic_pincode,doctor_profile.clinic_state, doctor_profile.clinic_lat, doctor_profile.clinic_lng, doctor_profile.availability, doctor_profile.medical_registration_proof, doctor_profile.photo_id_proof, doctor_profile.degree_proof, doctor_profile.signature');
        $this->db->from('users');
        $this->db->join('doctor_profile', 'users.id = doctor_profile.user_id');
        if ($where)
            $this->db->where($where);
        $this->db->where('users.role', 'doctor');
        //$this->db->where('users.activate_status', 1);
        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        return $query->result();
    }

    public function GetReviews($receiver_id, $type, $limit = NULL) {
        if ($type == 'doctor')
            $join_table = 'patient_profile';
        else
            $join_table = 'doctor_profile';
        $this->db->select('rating_and_reviews.id, rating_and_reviews.receiver_id, rating_and_reviews.sender_id, rating_and_reviews.rating, rating_and_reviews.review, rating_and_reviews.created_at,users.name as sender_name,' . $join_table . '.profile_image as sender_profile_image');
        $this->db->from('rating_and_reviews');
        $this->db->join('users', 'rating_and_reviews.sender_id = users.id');
        $this->db->join($join_table, 'users.id = ' . $join_table . '.user_id');
        $this->db->where('rating_and_reviews.receiver_id', $receiver_id);
        $this->db->where('rating_and_reviews.type', $type);
        $this->db->order_by('rating_and_reviews.id', 'DESC');
        if ($limit != '')
            $this->db->limit($limit);
        $query = $this->db->get();
        //echo $this->db->last_query(); die();
        return $query->result();
    }

    public function GetFeeds($user_id = NULL, $type = NULL) {
        $this->db->select('doctor_feed.*, feed_category.category_name_ru, feed_category.category_name_en,feed_category.category_name_ru, users.title, users.name, doctor_profile.profile_image, doctor_profile.clinic_name, doctor_profile.clinic_address , doctor_profile.speciality');
        $this->db->from('doctor_feed');
        $this->db->join('feed_category', 'doctor_feed.feed_category_id = feed_category.id');
        $this->db->join('users', 'doctor_feed.user_id = users.id');
        $this->db->join('doctor_profile', 'users.id = doctor_profile.user_id');
        if (!empty($type)) {
            if ($type == 'by_user')
                $this->db->where('doctor_feed.user_id', $user_id);
            if ($type == 'by_other')
                $this->db->where('doctor_feed.user_id !=', $user_id);
        }
        $this->db->where('doctor_feed.status', 1);
        $this->db->order_by('doctor_feed.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    public function GetSavedFeeds($where) {
        $this->db->select('doctor_feed.*, feed_category.category_name_en,feed_category.category_name_ru, users.title, users.name, doctor_profile.profile_image, doctor_profile.clinic_name, doctor_profile.clinic_address , doctor_profile.speciality');
        $this->db->from('doctor_feed');
        $this->db->join('feed_category', 'doctor_feed.feed_category_id = feed_category.id');
        $this->db->join('users', 'doctor_feed.user_id = users.id');
        $this->db->join('doctor_profile', 'users.id = doctor_profile.user_id');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result();
    }

}
