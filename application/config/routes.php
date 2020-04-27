<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['Admin'] = 'admin/AdminController/index';
$route['Logout'] = 'admin/AdminController/Logout';
$route['Dashboard'] = 'admin/AdminController/Dashboard';
$route['Profile'] = 'admin/AdminController/Profile';
$route['ChangePassword'] = 'admin/AdminController/ChangePassword';
$route['ForgotPassword'] = 'admin/AdminController/ForgotPassword';

$route['Speciality'] = 'admin/AdminController/Speciality';
$route['AddSpeciality'] = 'admin/AdminController/AddSpeciality';
$route['UpdateSpeciality/(:any)'] = 'admin/AdminController/UpdateSpeciality';
//$route['DeleteSpeciality/(:any)'] = 'admin/AdminController/DeleteSpeciality';

$route['FeedCategory'] = 'admin/AdminController/FeedCategory';
$route['AddFeedCategory'] = 'admin/AdminController/AddFeedCategory';
$route['UpdateFeedCategory/(:any)'] = 'admin/AdminController/UpdateFeedCategory';
$route['FeedList/(:any)'] = 'admin/AdminController/FeedList';
$route['ApproveFeed/(:any)/(:any)/(:any)'] = 'admin/AdminController/ApproveFeed';
$route['FeedDetails/(:any)'] = 'admin/AdminController/FeedDetails';

$route['Diseases'] = 'admin/AdminController/Diseases';
$route['AddDiseases'] = 'admin/AdminController/AddDiseases';
$route['UpdateDiseases/(:any)'] = 'admin/AdminController/UpdateDiseases';

$route['SubSpeciality'] = 'admin/AdminController/SubSpeciality';
$route['AddSubSpeciality'] = 'admin/AdminController/AddSubSpeciality';
$route['UpdateSubSpeciality/(:any)'] = 'admin/AdminController/UpdateSubSpeciality';
//$route['DeleteSubSpeciality/(:any)'] = 'admin/AdminController/DeleteSubSpeciality';

$route['Treatment'] = 'admin/AdminController/Treatment';
$route['AddTreatment'] = 'admin/AdminController/AddTreatment';
$route['UpdateTreatment/(:any)'] = 'admin/AdminController/UpdateTreatment';

$route['Doctors'] = 'admin/AdminController/Doctors';
$route['DoctorDetail/(:any)'] = 'admin/AdminController/DoctorDetail';
$route['ChangeDoctorAccountStatus/(:any)/(:any)'] = 'admin/AdminController/ChangeDoctorAccountStatus';
$route['ChangeDoctorApproveStatus/(:any)/(:any)'] = 'admin/AdminController/ChangeDoctorApproveStatus';
$route['ChangeDoctorFeatureStatus/(:any)'] = 'admin/AdminController/ChangeDoctorFeatureStatus';

$route['Patients'] = 'admin/AdminController/Patients';
$route['PatientDetail/(:any)'] = 'admin/AdminController/PatientDetail';
$route['ChangePatientAccountStatus/(:any)/(:any)'] = 'admin/AdminController/ChangePatientAccountStatus';
$route['UserAppointment/(:any)/(:any)'] = 'admin/AdminController/UserAppointment';

$route['Payment'] = 'admin/AdminController/Payment';

$route['Report'] = 'admin/AdminController/Report';

$route['Promotion'] = 'admin/AdminController/Promotion';
$route['AddPromotion'] = 'admin/AdminController/AddPromotion';
$route['UpdatePromotion/(:any)'] = 'admin/AdminController/UpdatePromotion';
$route['PromoDetails/(:any)'] = 'admin/AdminController/PromoDetails';

$route['AppHomeScreenSetting/(:any)'] = 'admin/AdminController/AppHomeScreenSetting';
$route['AddDoctorsForApp/(:any)/(:any)'] = 'admin/AdminController/AddDoctorsForApp';
$route['AddSpecialityForApp/(:any)/(:any)'] = 'admin/AdminController/AddSpecialityForApp';

$route['Appointment'] = 'admin/AdminController/Appointment';

$route['Earnings'] = 'admin/AdminController/Earnings';
$route['MakePaymentToDoctor'] = 'admin/AdminController/MakePaymentToDoctor';
$route['EarningsDetails/(:any)'] = 'admin/AdminController/EarningsDetails';
$route['EarningAppointment/(:any)'] = 'admin/AdminController/EarningAppointment';

$route['AppBanners'] = 'admin/AdminController/AppBanners';
$route['AddAppBanners'] = 'admin/AdminController/AddAppBanners';
$route['DeleteBannerImage/(:any)'] = 'admin/AdminController/DeleteBannerImage';