<?php
use Radiantweb\Proevents\Classes\IcalManager;

Route::group(['prefix' => 'radiantweb_api/events/ical/'], function() {

    Route::get('all', function(){ return View::make('radiantweb.proevents::ical.all', ['events' => IcalManager::getAllEvents()]); });
    Route::get('calendar', function(){ return View::make('radiantweb.proevents::ical.calendar', []); });
    Route::get('event', function(){ return View::make('radiantweb.proevents::ical.event', []); });

});
?>