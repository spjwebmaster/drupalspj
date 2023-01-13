

let calendar;
document.addEventListener('DOMContentLoaded', function() {

    let events = [];
    let url = "https://calendar.spjnetwork.org/feed.php?ex=";
    jQuery.ajax({
        url: url,
        success: function(res){
            console.log(res);

            let filters = document.querySelector(".calendar_filters");
            let filterval = filters.getAttribute("data-value");
            let filterSplits = filterval.split("|");
            let filterTags = filterSplits[0].split(":");
            let filterTag = filterTags[1];
            let filterCats = filterSplits[1].split(":");
            let filterCat = filterCats[1];

            console.log("filterTag", filterTag, "filterCat", filterCat)

            res.querySelectorAll("item").forEach(function(item){
                let temp = {};
                console.log("item", item)
                
                let starttime = item.querySelector("eventstarttime").innerHTML;
                let start = new Date(item.querySelector("eventstart").innerHTML + " " + starttime);
                let eventendtime = item.querySelector("eventendtime").innerHTML;
                let end = new Date(item.querySelector("eventend").innerHTML  + " " + eventendtime);
                let desc = item.querySelector("description").innerHTML;
                let cat = item.querySelector("category").innerHTML;
                cat = cat.replaceAll(" ", "_").toLowerCase();

                let addThisEntry = true;
                if(filterCat!="" && filterCat!=null){
                  addThisEntry = false;
                  if(cat == filterCat){
                    addThisEntry = true;
                  }
                }
                
                console.log("category is good? ", addThisEntry)
                let tags = item.querySelector("tags").innerHTML;
                let tagList = tags.split(",");
                let newTagList = [];
                tagList.forEach(function(t){
                  let tag = t.replaceAll(" ", "_").toLowerCase();
                  newTagList.push(tag);
                })

                if(addThisEntry==true){

                  temp["id"] = item.querySelector("GUID").innerHTML,
                  temp["title"] = item.querySelector("title").innerHTML.replaceAll("#039;", "'").replaceAll("&amp;", ""),
                  temp["start"] = start,
                  temp["end"] = end,
                  temp["url"] = item.querySelector("link").innerHTML,
                  temp["color"] = '#87A88C';
                  temp["extendedProps"] = {
                      "description":desc
                  }

                  
                  events.push(temp);
                }
            });

            console.log("events", events)
            var calendarEl = document.getElementById('spjcalendar');
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


