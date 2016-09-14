<?php namespace Radiantweb\Proevents\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Radiantweb\Proevents\Models\Settings as ProeventsSettingsModel;

class Generated_Dates extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public $bodyClass = 'compact-container';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Radiantweb.Proevents', 'proevents', 'generated_dates');
    }

    public function listInjectRowClass($model, $definition)
    {
        $settings = ProeventsSettingsModel::instance(); 
        $PE_DATE_GENERIC = ($settings->get('pe_date_generic')) ? $settings->get('pe_date_generic') : 'Y-m-d'; 
        $PE_DATE_SPOKEN = ($settings->get('pe_date_spoken')) ? $settings->get('pe_date_spoken') : 'l M jS, Y'; 
        $PE_DATE_TIME = ($settings->get('pe_date_time')) ? $settings->get('pe_date_time') : 'g:i a'; 

        $model->date = '('. date($PE_DATE_GENERIC,strtotime($model->date)) .') '.date($PE_DATE_SPOKEN,strtotime($model->date));
        $model->sttime = date($PE_DATE_TIME,strtotime($model->sttime));
        $model->entime = date($PE_DATE_TIME,strtotime($model->entime));
    }
}