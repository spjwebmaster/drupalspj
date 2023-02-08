<?php 
namespace Drupal\SpjCalendar\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;



class SpjCalendarController extends ControllerBase {


    public function HomeCalendarJson(){

        $calrss = "https://feeds.feedburner.com/spjcalendarspj";
        $calRes = readRSS($calrss, 5);
    
        return new JsonResponse([ 'data' => $calRes, 'method' => 'GET', 'status'=> 200]);
    }
    public function HomeCalendarGenJson(){

        $generalrss = "https://feeds.feedburner.com/calendargeneraljournalism";
        $generalRes = readRSS($generalrss, 5);

        return new JsonResponse([ 'data' => $generalRes, 'method' => 'GET', 'status'=> 200]);
    }
    public function CalendarJson(){

        $calrss = "https://calendar.spjnetwork.org/feed.php?ex=";
    
        $calRes = readRSS($calrss, 5);

        $dat = $calRes;

        return new JsonResponse([ 'data' => $dat, 'method' => 'GET', 'status'=> 200]);
    }
}