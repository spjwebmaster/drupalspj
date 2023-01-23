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
                el.querySelector("h2").innerHTML = "Submissions not available currently";
                el.querySelector(".field-content-value").classList.add("hidden")
            })
        }
    }
}

awardpage.dateCheck();
