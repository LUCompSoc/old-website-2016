<?php namespace Radiantweb\Proevents\Components;


use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Radiantweb\Proevents\Models\Event  as EventModel;
use Radiantweb\Proevents\Models\Calendar  as CalendarModel;
use Radiantweb\Proevents\Models\GeneratedDate  as GeneratedDateModel;
use Radiantweb\Proevents\Models\Settings as ProeventsSettingsModel;

class EventList extends ComponentBase
{

    public function componentDetails()
    {
        return [
            'name'        => 'radiantweb.proevents::lang.components.event_list.details.name',
            'description' => 'radiantweb.proevents::lang.components.event_list.details.description'
        ];
    }

    public function defineProperties()
    {
        $calendars = $this->getCalendars();
        $calendars_select_options = array('All Calendars'=>'All Calendars');
        foreach($calendars as $calendar){
            $calendars_select_options[$calendar->id] = $calendar->name;
        }

        return [
            'eventpage' => [
                'title'       => 'radiantweb.proevents::lang.components.event_list.properties.eventpage.title',
                'description' => 'radiantweb.proevents::lang.components.event_list.properties.eventpage.description',
                'type'        => 'dropdown' // @todo Page picker
            ],
            'style' => [
                'description' => 'radiantweb.proevents::lang.components.event_list.properties.style.description',
                'title'       => 'radiantweb.proevents::lang.components.event_list.properties.style.title',
                'default'     => 'simple_list',
                'type'        => 'dropdown',
                'options'     => [
                    'simple_list'=>'radiantweb.proevents::lang.components.event_list.properties.style.options.simple',
                    'footer_list'=>'radiantweb.proevents::lang.components.event_list.properties.style.options.footer',
                    'show_schedule'=>'radiantweb.proevents::lang.components.event_list.properties.style.options.show'
                ]
            ],
            'calendar' => [
                'description' => 'radiantweb.proevents::lang.components.event_list.properties.calendar.description',
                'title'       => 'radiantweb.proevents::lang.components.event_list.properties.calendar.title',
                'default'     => 'All Calendars',
                'type'        => 'dropdown',
                'options'     => $calendars_select_options
            ],
            'num' => [
                'description' => 'radiantweb.proevents::lang.components.event_list.properties.num.description',
                'title'       => 'radiantweb.proevents::lang.components.event_list.properties.num.title',
                'default'     => '5',
                'type'        => 'string'
            ],
            'pagination' => [
                'description' => 'radiantweb.proevents::lang.components.event_list.properties.pagination.description',
                'title'       => 'radiantweb.proevents::lang.components.event_list.properties.pagination.title',
                'type'        => 'checkbox'
            ]
        ];
    }

    public function getCalendars(){
        return CalendarModel::get();
    }

    public function onRun()
    {
        $this->page['events'] = $this->listEvents();
        $this->page['event_list_view'] = $this->alias.'::'.$this->property('style');
        $this->page['eventpage'] =  $this->property('eventpage');
        $this->page['pagination'] = $this->property('pagination');

        $settings = ProeventsSettingsModel::instance();

        $this->page['PE_DATE_GENERIC'] = ($settings->get('pe_date_generic')) ? $settings->get('pe_date_generic') : 'Y-m-d';
        $this->page['PE_DATE_FULL'] = ($settings->get('pe_date_full')) ? $settings->get('pe_date_full') : 'Y-m-d g:i a';
        $this->page['PE_DATE_SPOKEN'] = ($settings->get('pe_date_spoken')) ? $settings->get('pe_date_spoken') : 'l M jS, Y';
        $this->page['PE_DATE_TIME'] = ($settings->get('pe_date_time')) ? $settings->get('pe_date_time') : 'g:i a';

        $this->addCss('/plugins/radiantweb/proevents/assets/css/proevents_list.css');
    }

    public function getEventpageOptions()
    {
        $ParentOptions = array(''=>'-- chose one --');
        $pages = Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');

        $ParentOptions = array_merge($ParentOptions, $pages);

        //\Log::info($ParentOptions);
        return $ParentOptions;
    }


    /*
     * @getItemDates
     * - get grouped dates array
     */
    public function getItemDates($group,$event_id){
        $dates_string = '';
        $events = new GeneratedDateModel();
        $grouped_events = $events->getGroupedDates($event_id,$group);
        foreach($grouped_events  as $e){
            $dates_string .= date($this->page['PE_DATE_SPOKEN'],strtotime($e->date)).' | ';
            if($e->allday > 0){
                $dates_string .= 'All Day <br/>';
            }else{
                $dates_string .= date($this->page['PE_DATE_TIME'],strtotime($e->sttime)).' - '.date($this->page['PE_DATE_TIME'],strtotime($e->entime)).'<br/>';
            }
        }


        return $dates_string;
    }


    /*
     * @getFromToDates
     * - get condensed grouped dates array
     */
    public function getFromToDates($group,$event_id){
        $dates_string = '';
        $events = new GeneratedDateModel();
        $grouped_events = $events->getGroupedDates($event_id,$group);
        $ec = 0;
        foreach($grouped_events  as $e){
            $ec++;
            if($ec == 1){
                $dates_string .= date($this->page['PE_DATE_SPOKEN'],strtotime($e->date)).' - ';
            }elseif($ec == count($grouped_events)){
                $dates_string .= date($this->page['PE_DATE_SPOKEN'],strtotime($e->date)).' | ';
            }
            if($e->allday > 0){
                $time = 'All Day <br/>';
            }else{
                $time = date($this->page['PE_DATE_TIME'],strtotime($e->sttime)).' - '.date($this->page['PE_DATE_TIME'],strtotime($e->entime)).'<br/>';
            }
        }


        return $dates_string.$time;
    }

    public function listEvents()
    {
        $this->page['eventpage'] =  $this->property('eventpage');

        if ($this->events !== null)
            return $this->events;

        $calendar_ids = null;

        /*
        * if we have a calendar property
        * and it's not 'All Calendars'
        * assemble calendar ID filters
        */
        if($this->property('calendar') && $this->property('calendar') != 'All Calendars'){
            $calendars = explode(',',$this->property('calendar'));
            $calendar_ids =  '(';
            $ci = 0;
            foreach($calendars as $id){
                if($ci>0){ $calendar_ids .= ' OR '; }
                $calendar_ids .=  "calendar_id = '$id'";
                $ci++;
            }
            $calendar_ids .=  ')';
        }

        $events = new GeneratedDateModel();
        /*
        * return list of events
        */
        if($calendar_ids){
            return $this->events = $events->getCalendarEventsList($calendar_ids,$this->property('num'));
        }else{
            return $this->events = $events->getEventsList($this->property('num'));
        }
    }

}
