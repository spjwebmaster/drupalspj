var calendarobj = {
  calendar: null,
  data: [],
  bindings: function(){
    let contain = document.querySelector(".block-spj-calendar-block-calendar");
    contain.querySelectorAll(".tabs .nav-tabs li a").forEach(function(a){
      a.addEventListener("click", function(e){
        e.preventDefault();
        let href = e.target.href;
        href = href.substring(href.indexOf("#")+1, href.length)

        document.querySelectorAll(".block-spj-calendar-block-calendar .nav-item").forEach(function(el){
          el.classList.remove("active")
        });
        e.target.closest("li").classList.add("active")

        document.querySelectorAll(".block-spj-calendar-block-calendar .tab-pane").forEach(function(el){
          if(el.id == href){
            el.classList.add("active")
          } else {
            el.classList.remove("active")
          }
        })
      })
    })
  },
  buildFilterDisplay: function(filterCat, filterTags){
    let contain = document.querySelector(".block-spj-calendar-block-calendar .inputs");
    console.log(filterCat, filterTags);
    let filterWrapper = document.createElement("div");
    filterWrapper.classList.add("calendar-filter-wrapper");
    let filterBody = "";
    if(filterCat!=null){
      filterBody+= `
        <div class="alert alert-warning">
          Showing Events in the 
          <span>
            <strong>
           ${filterCat.replaceAll("_", " ")}
            </strong>
          </span> category.  
          <a href="/events" class="float-right">Reset filter</a>
        </div>
      `;
    } 
    if(filterTags!=null){

    }
    filterWrapper.innerHTML = filterBody;
    contain.append(filterWrapper);
  },
  getFilters: function(){

    let uri = window.location.href;
    uri = uri.substring(uri.indexOf("?")+1, uri.length);
    console.log("uri", uri);
    let category = null;
    let tags = null;
    uri.split("&").forEach(function(list){
      let listSplit = list.split("=");
      if(listSplit[0].toLowerCase()=="category"){
        category = listSplit[1]
      } else if(listSplit[0].toLowerCase()=="tag"){
        tags = listSplit[1];
      }
    })

    let filters = {
      tags: tags,
      category: category
    }
    return filters;
  },
  getData: function(){
    let url = "https://calendar.spjnetwork.org/feed.php?ex=";
    jQuery.ajax({
        url: url,
        success: function(res){


          let queryFilters = calendarobj.getFilters();
          let filterTags = queryFilters.tags;
          let filterCat = queryFilters.category;
          calendarobj.buildFilterDisplay(filterCat, filterTags);


          res.querySelectorAll("item").forEach(function(item){
              let temp = {};
              
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
              
              let tags = item.querySelector("tags").innerHTML;
              let tagList = tags.split(",");
              let newTagList = [];
              tagList.forEach(function(t){
                let tag = t.replaceAll(" ", "_").toLowerCase();
                newTagList.push(tag);
              });

              

              //colors
            
              let thisColor = "#444";
              if (cat=="uncategorized") {thisColor="#666";}
              if (cat.indexOf("spj_local") >-1) {thisColor="#87A88C";}
              if (cat.indexOf("spj_community") >-1) {thisColor="#8FB5C6";}
              if (cat=="spj_national") {thisColor="#5D79AF";}
              if (cat=="general_journalism") {thisColor="#C9665A";}


              if(addThisEntry==true){

                temp["id"] = item.querySelector("GUID").innerHTML,
                temp["title"] = item.querySelector("title").innerHTML.replaceAll("#039;", "'").replaceAll("&amp;", ""),
                temp["start"] = start,
                temp["end"] = end,
                temp["url"] = item.querySelector("link").innerHTML,
                temp["color"] = thisColor;
                temp["extendedProps"] = {
                    "description":desc,
                    "tags": item.querySelector("tags").innerHTML,
                    "category": cat,
                }

                
                calendarobj.data.push(temp);
              }
          });

          calendarobj.buildCalendar();
          calendarobj.buildList();
          calendarobj.bindings();
        }
      });
  },
  buildCalendar: function(){
    let events = calendarobj.data;
    let defaultViewP = "";
    if (/Android|webOS|iPhone|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Silk|Opera Mini/i.test(navigator.userAgent)) {
       defaultViewP = "basicWeek";
      } else {
       defaultViewP = "month";
    }
    var today = new Date();

    var calendarEl = document.getElementById('spjcalendar');
    calendarobj.calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      themeSystem: 'bootstrap',
      headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: 'dayGridMonth,timeGridWeek,listMonth'
			},
      bootstrapFontAwesome: {
        close: 'fa-times',
        prev: 'fa-chevron-left',
        next: 'fa-chevron-right',
        prevYear: '',
        nextYear: ''
      },
      defaultDate: today,
			defaultView: defaultViewP,
      events: events,
      navLinks: true,
      eventClick: function(info) {
        console.log("info", info)
        var eventObj = info.event;

        let id = eventObj.id;
        let desc = eventObj.extendedProps.description;

        if (eventObj.url) {
          console.log("will open", eventObj.url)
          //window.open(eventObj.url);

          info.jsEvent.preventDefault(); // prevents browser from following link in current tab.
        } else {
          // popup?
        }

        calendarobj.setCalendarModal(eventObj)
      },
    });
    calendarobj.calendar.render();
    calendarobj.buildCalendarModal();
        
  },
  buildListEntry: function(row){

    let rowNode = document.createElement("div");
      rowNode.classList.add("views-row");

      let dateHeading = document.createElement("h3");
      dateHeading.innerHTML = row.start
      rowNode.append(dateHeading);

      let titleHeading = document.createElement("h2");
      let title = document.createElement("a");
      title.innerHTML = row.title;
      title.setAttribute("href", row.url);
      title.setAttribute("target", "_blank");
      titleHeading.append(title);
      rowNode.append(titleHeading);

      let catText = (row.extendedProps.category? row.extendedProps.category: " - ");
      let category = document.createElement("a");
      category.classList.add("badge");
      if(row.backgroundColor){
        category.style.backgroundColor = row.backgroundColor;
      } else if(row.color){
        category.style.backgroundColor = row.color;
      } else{
        category.classList.add("bg-secondary");
      }
      category.classList.add("text-white");
      category.classList.add("category");
      let catDisplay = catText.replace(" ", "_").toLowerCase();
      category.setAttribute("href", "/events?category=" + catDisplay)
      category.innerHTML = catText.replaceAll("_", " ");
      rowNode.append(category);

      let tags = document.createElement("div");
      let taglist = row.extendedProps.tags.split(",");
      taglist.forEach(function(ta){
        let thistag = document.createElement("a");
        thistag.innerHTML = ta;
        thistag.href = "/events?tag="+ta.replace(" ", "_").toLowerCase();
        thistag.classList.add("badge")
        thistag.classList.add("bg-success")
        thistag.classList.add("text-white")
        thistag.classList.add("mr-1")
        tags.append(thistag);
      })
      rowNode.append(tags);

      let url = document.createElement("a");
      url.href = row.url;
      url.setAttribute("target", "_blank");
      url.innerHTML = "Link";
      url.classList.add("btn")
      url.classList.add("text-white")
      url.classList.add("mb-2")
      url.classList.add("btn-secondary");
      rowNode.append(url)

      let desc = document.createElement("div");
      desc.classList.add("calendar-description");
      desc.innerHTML = row.extendedProps.description;
      rowNode.append(desc)

      return rowNode;
  },
  buildList: function(){
    let listNode = document.getElementById("spjcalendarlist");
    listNode.classList.add("view");
    
    calendarobj.data.forEach(function(row){


      let ret = calendarobj.buildListEntry(row);
      listNode.append(ret)
    })

  },
  buildCalendarModal: function(){
    let par = document.getElementById("tabCalendar");
    let modalWrapper = document.createElement("div");
    modalWrapper.classList.add("calendarmodal");
    modalWrapper.classList.add("hidden");

    let modalContent = `
      <div class="calendarmodal-header">
        <h4>Details</h4>
        <a href="#" class="calendarmodal-close pull-right"><i class="fa fa-times"></i></a>
      </div>
      <div class="calendarmodal-body">
        Content
      </div>
    `;
    modalWrapper.innerHTML = modalContent;


    par.append(modalWrapper);
    document.querySelector("#tabCalendar .calendarmodal .calendarmodal-close").addEventListener("click", function(el){
      el.preventDefault();
      el.target.closest(".calendarmodal").classList.add("hidden");
    })
  },
  setCalendarModal: function(data){
    console.log("setting", data);
    let node = document.querySelector("#tabCalendar .calendarmodal");
    node.classList.remove("hidden")
    //let header = node.querySelector(".calendarmodal-header h3");
    //header.innerHTML = data.title;
    let body = node.querySelector(".calendarmodal-body");
    let ret = calendarobj.buildListEntry(data);
    
    body.innerHTML = "";
    body.append(ret);


  },
  init: function(){
    calendarobj.getData();
  }
}

document.addEventListener('DOMContentLoaded', function() {
  calendarobj.init();
});



