var awardpage = {
    dateCheck: function(){
        let datenode = document.querySelector(".block-field-blocknodeawardfield-deadline");
        if(datenode){
            let deadline = datenode.querySelector("time");
            if(deadline){
                let dateVal = deadline.getAttribute("datetime");
                console.log("checking for date", dateVal);
                let dateObj = new Date(dateVal);
                console.log(dateObj);
                let todayDate = new Date();
                if(todayDate.getTime() < dateObj.getTime()){
                    console.log("valid")
                } else {
                    console.log("too late");
                    awardpage.hideForm();
                }
            }
            
        }

    },
    hideForm: function(){
        let formNode = document.querySelectorAll(".block-field-blocknodeawardfield-submission-form");
        if(formNode){
            formNode.forEach(function(el){
                console.log("submission link", el)
                if(el.querySelector("h2")){
                    el.querySelector("h2").innerHTML = "Submissions not available currently";
                }
                el.querySelector(".field-content-value").classList.add("hidden")
            })
        }
    }
    
    
}
var winnerList = {
    init: function(){
        console.log(winnerList.winnerTarget);
        winnerList.winnerListShell();
    },
    data: null,
    awardCodeId: null,
    winnerTarget: document.querySelector("#awardWinners"),
    winnerTagId: function(){
        let awardCodeNode = document.querySelector(".block-field-blocknodeawardfield-award-code");
        if(awardCodeNode){
            awardCode = awardCodeNode.innerText;
            return awardCode;
        } else {
            return null;
        }
    },
    winnerListData: function(){
        let tagid = winnerList.winnerTagId();
        winnerList.awardCodeId = tagid;
        let url = `/jsonapi/node/news_item?filter[field_award_code.meta.drupal_internal__target_id]=${tagid}&sort=-field_active_date&page[size]=20&page[number]=1`;
        console.log("url", url)
        fetch(url)
            .then(response=>response.json())
            .then(data => {
                //console.log("data", data);
                let tempData= {};
                data.data.forEach(function(el){

                    let region = el.relationships.field_region_term.data;
                    let regionId = null;
                    if(region!=null){
                        regionId = region.meta.drupal_internal__target_id;

                        console.log("moe region", regionId)
                    }
                    
                    let title = el.attributes.title;
                    let date = el.attributes.field_active_date;
                    let year = date.split("-")[0];
                    
                    let winnernamesshort= el.attributes.field_award_winner_names;
                    let winnernameslong= el.attributes.field_award_winner_names_long;
                    let winnernames ="";
                    if(winnernameslong!="" && winnernameslong!=null){
                        winnernames= winnernameslong.value;
                    }
                    if(winnernamesshort!="" && winnernamesshort!=null){
                        winnernames= winnernamesshort;
                    }
                    let temp = {
                        title: title,
                        date: date,
                        winnernames: winnernames,
                        id: el.attributes.drupal_internal__nid,
                        link: el.attributes.path.alias,
                        year: year,
                        region: regionId
                    }


                    if(year!="" && year!="" && year!=null){
                    
                        if(tempData["y_"+year]!=null && typeof tempData["y_"+year]!= 'undefined') {
                            tempData["y_"+year].push(temp);
                        } else {
                            tempData["y_"+year] = [];
                            tempData["y_"+year].push(temp);
                        }
                    }

                });
                

                if(winnerList.awardCodeId==117){
                    // MOE just get latest year
                    let counter = 0;
                    let tempDataReplace = null;
                    for (let key in tempData) {
                        if (tempData.hasOwnProperty(key)) {
                            if(counter==0){
                                tempDataReplace = {};


                                let sorted = tempData[key].sort((a, b) => {
                                    return a.region - b.region;
                                });
                                tempDataReplace[key] = sorted;
                                console.log("moe data to sort", tempDataReplace);
                            }
                            counter++;
                        }
                    }
                    
                    winnerList.data = tempDataReplace;
                } else {
                    winnerList.data = tempData;
                }
                
                winnerList.build();
        });
    },
    sortObj: function (obj) {
        return Object.keys(obj).sort().reduce(function (result, key) {
          result[key] = obj[key];
          return result;
        }, {});
      },

    winnerListShell: function(){
        let wrapper = document.createElement("div");
        wrapper.classList.add("winnerList-wrapper");
        let tagid = winnerList.winnerTagId();
               
        winnerList.winnerTarget.append(wrapper);
        winnerList.winnerListData();
    },
    build: function(){
        console.log("winner data",winnerList.data);
        let dataObj = winnerList.data;
        let parent = winnerList.winnerTarget.querySelector(".winnerList-wrapper");
        parent.classList.add("view");
        let counter = 0;
        for (let key in dataObj) {
            if (dataObj.hasOwnProperty(key)) {
                let value = dataObj[key];
                console.log(key, value);

                let activeYear = key.replace("y_","");
                
                let row = document.createElement("div");
                row.classList.add("section");

                let heading = document.createElement("span");
                heading.innerHTML = "<time>" + activeYear + "</time>";

                row.append(heading);

                let list = document.createElement("div");
                list.classList.add("views-row")
                row.append(list)

                value.forEach(entry=>{
                    let entryWrap = document.createElement("div");
                    entryWrap.classList.add("entry");
                    
                    let linky = document.createElement("a");
                    linky.innerHTML = "<strong>" + entry.title + "</strong>";
                    linky.setAttribute("href", entry.link);
                    entryWrap.append(linky);
                    
                    let desc = document.createElement("p");
                    desc.innerHTML = entry.winnernames;
                    entryWrap.append(desc);

                    if(winnerList.awardCodeId==117){
                        if(entry.region!=null){
                            row.querySelector(".views-row").append(entryWrap)
                        }
                    } else {
                        row.querySelector(".views-row").append(entryWrap)
                    }
                })

                
                parent.append(row);
                

            }
        }
    }
}

awardpage.dateCheck();
winnerList.init();

