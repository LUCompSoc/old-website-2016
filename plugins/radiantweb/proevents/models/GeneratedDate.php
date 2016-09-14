<?php namespace Radiantweb\Proevents\Models;

use Str;
use Model;
use October\Rain\Database\ModelException;

class GeneratedDate extends Model
{
    public $table = 'radiantweb_generated_dates';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * @var array Attributes that support translation, if available.
     */
     public $translatable = [
         'title',
         'excerpt',
         'content'
     ];

    /*
     * Validation
     */
    public $rules = [];

    protected $guarded = [];

    /*
     * Relations
     */
    public $belongsTo = [
        'event' => ['Radiantweb\Proevents\Models\Event'],
		'calendars' => ['Radiantweb\Proevents\Models\Calendar', 'table'=> 'radiantweb_events_calendars', 'key' => 'event_id']
	];


    /*
     * Get specific calendar event dates by month for calendar views
     */
    public function getCalendarEvents($month,$calendar_ids=null)
    {
        if($calendar_ids){
            $events = GeneratedDate
                ::whereRaw($calendar_ids)
                ->whereRaw("DATE_FORMAT(date,'%m') = $month")
                ->orderBy('date')
                ->orderBy('sttime','asc')
                ->get();
        }else{
            $events = GeneratedDate
                ::whereRaw("DATE_FORMAT(date,'%m') = $month")
                ->orderBy('date')
                ->orderBy('sttime','asc')
                ->get();
        }

        return $events;
    }


    /*
     * Get specific calendar event dates by start-end for calendar views
     */
    public function getFromToCalendarEvents($start,$end,$calendar_ids=null)
    {
        if($calendar_ids){
            $events = GeneratedDate
                ::whereRaw($calendar_ids)
                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >= DATE_FORMAT('$start','%Y-%m-%d')")
                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') <= DATE_FORMAT('$end','%Y-%m-%d')")
                ->orderBy('date')
                ->orderBy('sttime','asc')
                ->get();
        }else{
            $events = GeneratedDate
                ::whereRaw("DATE_FORMAT(date,'%Y-%m-%d') >= DATE_FORMAT('$start','%Y-%m-%d')")
                ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') <= DATE_FORMAT('$end','%Y-%m-%d')")
                ->orderBy('date')
                ->orderBy('sttime','asc')
                ->get();
        }

        return $events;
    }

    /*
     * Get specific calendar event dates by month for list views
     */
    public function getCalendarEventsList($calendar_ids=null,$num=null)
    {
        $events = GeneratedDate
            ::whereRaw($calendar_ids)
            ->groupBy('event_id','grouped_id')
            ->orderBy('date')
            ->orderBy('sttime','asc')
            ->paginate($num);

        return $events;
    }


    /*
     * Get event dates by month for list views
     */
    public function getEventsList($num=null)
    {
        $events = GeneratedDate
            ::groupBy('event_id','grouped_id')
            ->orderBy('date')
            ->orderBy('sttime','asc')
            ->paginate($num);

        return $events;
    }


    /*
     * Get grouped events for given eventID and groupID
     */
    public function getGroupedDates($event_id,$group)
    {
        return json_decode(GeneratedDate
                        ::where('event_id', '=', $event_id)
                        ->where('grouped_id', '=', $group)
                        ->orderBy('date')
                        ->orderBy('sttime','asc')
                        ->get());
    }

    /*
     * Check whether or not a given date has an event
     */
    public static function dayHasEvents($date,$calendar_ids=null)
    {
        if($calendar_ids){
            return GeneratedDate
                            ::whereRaw($calendar_ids)
                            ->whereRaw("DATE_FORMAT(date,'%Y-%m-%d') = DATE_FORMAT('$date','%Y-%m-%d')")
                            ->first();
        }else{
            return GeneratedDate
                            ::whereRaw("DATE_FORMAT(date,'%Y-%m-%d') = DATE_FORMAT('$date','%Y-%m-%d')")
                            ->first();
        }
    }
}
