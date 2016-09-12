<?php namespace Radiantweb\Proevents\Classes;

use Radiantweb\Proevents\Models\GeneratedDate;

/**
 * print out iCal views
 * Requires min php 5.3  
 *
 * @package radiantweb/proevents
 * @author ChadStrat
 */
class IcalManager
{
    public function __construct($type=null)
    {

    }
    
    public static function getAllEvents(){
        return GeneratedDate::getEventsList();
    }

}