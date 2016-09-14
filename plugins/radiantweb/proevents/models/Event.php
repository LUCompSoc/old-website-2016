<?php namespace Radiantweb\Proevents\Models;

use Str;
use Model;
use BackendAuth;
use Backend\Models\User as BackendUser;
use October\Rain\Database\ModelException;
use Radiantweb\Proevents\Classes\MultidateHelper;
use Radiantweb\Proevents\Models\GeneratedDate as GeneratedDateModel;

class Event extends Model
{
    use \October\Rain\Database\Traits\Purgeable;

    public $table = 'radiantweb_events';

    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    /**
     * @var array Attributes that support translation, if available.
     */
    public $translatable = [
        'title',
        'excerpt',
        'content',
        'location_name',
        'location_address',
        'contact_email'
    ];

    public $purgeable = [
    	'update_generated_booking_info'
    ];

    /*
     * Validation
     */
    public $rules = [
        'title' => 'required',
        'excerpt' => 'required',
        'calendar_id' => 'required'
    ];

    public $customMessages = [
       'calendar_id.required' => 'You must select a Calendar!',
    ];

    protected $guarded = [];

    protected $jsonable = ['multidate','excluded'];

    /*
     * Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User'],
        'calendar' => ['Radiantweb\Proevents\Models\Calendar', 'table' => 'radiantweb_events_calendars', 'order' => 'name']
    ];

    public $attachMany = [
        'featured_images' => ['System\Models\File']
    ];

	public $hasMany = [
		'generated_dates' => ['Radiantweb\Proevents\Models\GeneratedDate']
	];

    //
    // Events
    //
    public function beforeValidate()
    {
        // Generate a URL slug for this model
        if (!$this->exists && !$this->slug)
            $this->slug = Str::slug($this->title);
    }

    public function beforeDelete()
    {
        GeneratedDateModel::where('event_id', '=', $this->id)->delete();
    }

	public function beforeSave(){
		$_REQUEST['update_generated'] = $_REQUEST['Event']['update_generated_booking_info'];
	}

	public function afterSave(){

        $user = BackendAuth::getUser();
        Event::where('id',$this->id)->update(array('user_id'=>$user->id));

		/*
		* generate dates array from inputs
		*/
		$dates = new MultidateHelper($this->multidate,$this->recur,$this->thru);
		$dates_array = $dates->getDates();
		$grouped = 0;
		$gt = 0;

		$exclude_dates = json_decode($this->excluded, true);

		$exclude_dates = $exclude_dates['date'];
		if(!is_array($exclude_dates)){
			$exclude_dates = array();
		}

		//\Log::info(json_encode($dates_array));

		/*
		* loop through dates array and create/update dates
		*/
		if(is_array($dates_array)){
			foreach($dates_array as $dategroup){
				if($this->grouped){
					$gt = 0;
				}
				if(is_array($dategroup)){

					foreach($dategroup as $date){

                        if(!is_array($date)){
                            $date = array('date' => $date);
                        }

						if(!in_array($date['date'],$exclude_dates)){

							if($this->recur != 'daily'){
								$grouped = $gt++;
							}

							/*
							* maintain Taxonomy & try and retrieve existing date ID
							*/
							$generated_date = GeneratedDateModel
								::where('event_id', '=', $this->id)
								->where('date','=',$date['date'])
								->where('sttime','=',date('H:i:s',strtotime($date['sttime'])))
								->first();

							$data = [
									'event_id' => $this->id,
									'calendar_id' => $this->calendar_id,
									'title' => $this->title,
									'content' => $this->content,
									'excerpt' => $this->excerpt,
									'date' => $date['date'],
									'sttime' => date('H:i:s',strtotime($date['sttime'])),
									'entime' => date('H:i:s',strtotime($date['entime'])),
									'allday' => $this->allday,
									'recur' => $this->recur,
									'grouped' => $this->grouped,
									'grouped_id' => $grouped,
									'thru' => $this->thru,
									'updated' => 1,
                                    'user_id' => $this->user_id
								];

							if($_REQUEST['update_generated']=='update'){
								$data['status'] = $this->status;
								$data['event_qty'] = $this->event_qty;
								$data['event_price'] = $this->event_price;
							}

							/* if we have an exeisting date
							* matching this event_id and group#
							* update it. else create new.
							*/
							if(is_object($generated_date)){
								$this->generated_dates()->where('id', '=', $generated_date->id)->update($data);
							}else{
								$this->generated_dates()->create($data);
							}

						}
					}
				}
			}
		}else{
			$date_info = json_decode($this->multidate);
			/*
			* maintain Taxonomy & try and retrieve existing date ID
			*/
			$generated_date = GeneratedDateModel
							::where('event_id', '=', $this->id)
							->where('date','=',$date_info->date[0])
							->where('sttime','=',date('H:i:s',strtotime($date_info->sttime[0])))
							->first();

			$data = [
					'event_id' => $this->id,
					'calendar_id' => $this->calendar_id,
					'title' => $this->title,
					'content' => $this->content,
					'excerpt' => $this->excerpt,
					'date' => $date_info->date[0],
					'sttime' => date('H:i:s',strtotime($date_info->sttime[0])),
					'entime' => date('H:i:s',strtotime($date_info->entime[0])),
					'allday' => $this->allday,
					'recur' => $this->recur,
					'grouped' => $this->grouped,
					'grouped_id' => 1,
					'thru' => $this->thru,
					'status' => null,
					'updated' => 1,
					'event_qty' => null,
					'event_price' => null,
                    'user_id' => $this->user_id
				];

			/* if we have an exeisting date
			* matching this event_id and group#
			* update it. else create new.
			*/
			if(is_object($generated_date)){
				$this->generated_dates()->where('id', '=', $generated_date->id)->update($data);
			}else{
				$this->generated_dates()->create($data);
			}
		}

		/*
		* remove all dates of this event id that have not been updated
		*/
		$this->generated_dates()->where('event_id', '=', $this->id)->where('updated', '<', 1)->delete();
		/*
		* reset all dates that have been updated
		*/
		$this->generated_dates()->where('event_id', '=', $this->id)->update(array('updated' => 0));
	}
}
