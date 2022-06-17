

let calendar;
document.addEventListener('DOMContentLoaded', function() {

    let events = [];
    let url = "https://calendar.spjnetwork.org/feed.php?ex=";
    jQuery.ajax({
        url: url,
        success: function(res){
            console.log(res);
            
            res.querySelectorAll("item").forEach(function(item){
                let temp = {};
                console.log("item", item)
                
                let starttime = item.querySelector("eventstarttime").innerHTML;
                let start = new Date(item.querySelector("eventstart").innerHTML + " " + starttime);
                let eventendtime = item.querySelector("eventendtime").innerHTML;
                let end = new Date(item.querySelector("eventend").innerHTML  + " " + eventendtime);
                let desc = item.querySelector("description").innerHTML;
                temp["id"] = item.querySelector("GUID").innerHTML,
                temp["title"] = item.querySelector("title").innerHTML,
                temp["start"] = start,
                temp["end"] = end,
                temp["url"] = item.querySelector("link").innerHTML,
                temp["color"] = '#87A88C';
                temp["extendedProps"] = {
                    "description":desc
                }
                events.push(temp);
            });

            console.log("events", events)
            var calendarEl = document.getElementById('calendar');
            calendar = new FullCalendar.Calendar(calendarEl, {
              initialView: 'dayGridMonth',
              events: events,
              eventClick: function(info) {
                  console.log("info", info)
                var eventObj = info.event;
        
                let id = eventObj.id;
                let desc = eventObj.extendedProps.description;
                console.log(desc)
                if (eventObj.url) {

                  window.open(eventObj.url);
        
                  info.jsEvent.preventDefault(); // prevents browser from following link in current tab.
                } else {
                  // popup?
                }
              },
            });
            calendar.render();
        }, 
        error: function(e){

        }
    })

   
  });


