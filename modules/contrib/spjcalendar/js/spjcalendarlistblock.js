let calendarlist = {
    data: [],
    getData: function(){
        let url = "https://calendar.spjnetwork.org/feed.php?ex=";
        jQuery.ajax({
            url: url,
            success: function(res){
    
    
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

                  let tags = item.querySelector("tags").innerHTML;
                  let tagList = tags.split(",");
                  let newTagList = [];
                  tagList.forEach(function(t){
                    let tag = t.replaceAll(" ", "_").toLowerCase();
                    newTagList.push(tag);
                  });
    
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
                    calendarlist.data.push(temp);
                  }
              });

              calendarlist.buildList();
              //calendarlist.bindings();
            }
          });
      },
      init: function(){
        calendarlist.getData();
      },
      buildList: function(){
        console.log("building ",calendarlist.data);
        let listNode = document.querySelector(".block-spjcalendar .view ul");

        calendarlist.data.forEach(function(row){
            let ret = calendarlist.buildListEntry(row);
            listNode.append(ret)
          })
      },
      buildListEntry: function(row){

        let rowNode = document.createElement("li");
          rowNode.classList.add("item-list");
    
          let dateHeading = document.createElement("span");

          let d = new Date(row.start);
          let startPretty = d.getDate()  + "-" + (d.getMonth()+1) + "-" + d.getFullYear()
          dateHeading.innerHTML = d.toDateString();

          rowNode.append(dateHeading);

          let breaker = document.createElement("span");
            breaker.innerHTML = "<br />";
            rowNode.append(breaker);
    
          let titleHeading = document.createElement("span");
          let title = document.createElement("a");
          title.innerHTML = row.title;
          title.setAttribute("href", row.url);
          title.setAttribute("target", "_blank");
          titleHeading.append(title);
          rowNode.append(titleHeading);
    
          /*
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
          */
          
          return rowNode;
      },
}

document.addEventListener('DOMContentLoaded', function() {
    calendarlist.init();
});
  