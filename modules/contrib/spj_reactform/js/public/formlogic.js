var formlogic = {
    reactloaded: false,
    reactTimer: null,
    init: function(){
        console.log("form logic");
        formlogic.reactTimer = window.setInterval(function(){
            console.log("looking for react");
            if(document.querySelector(".App")){
                let mainSelector = document.querySelector(".App .form-control[name='mainCategory']");
                if(mainSelector.querySelectorAll("option").length>1){
                    if(mainSelector.value!=="_none"){
                        formlogic.reactloaded = true;
                        console.log("react is loaded");
                        clearInterval(formlogic.reactTimer);
        
                        formlogic.bindings();
                    }
                }
                
            }
        },500);
    },
    getAwardName: function(){
        
        let href = window.location.href;
        let awardCodeName = "naa";
        let awardCode;
        if(href.indexOf("?type")>-1){
            awardCodeName = href.substring(href.indexOf("?type=")+6, href.length);
            console.log("award", awardCode);
            switch(awardCodeName){
            case "moe": awardCode = 193; break;
            case "naa": awardCode = 194; break;
            case "sdx": awardCode = 192; break;
            }
            let target = document.querySelector("input[data-drupal-selector='edit-field-award-submission-code-0-target-id']")
            if(target){
            target.value=awardCode;
            target.setAttribute("readonly", true);
            }
        }
          

    },
    bindings: function(){
        //formlogic.getAwardName();
        document.querySelectorAll(".App .form-control").forEach(function(el){
            console.log("finding el", el);

            if(el.name=="mainCategory"){
                let initTarget = null;
                if(document.getElementById("edit-field-main-category")){
                    initTarget = document.getElementById("edit-field-main-category");
                } else {
                    initTarget = document.getElementById("edit-maincat");
                }
                console.log("initial value", initTarget.value)
                initTarget.value = el.value;
            }
            el.addEventListener("change", function(e){
                console.log("react change element with access to full DOM");
                let target= null;
                if(e.target.name=="mainCategory"){
                    console.log("react change for main");
                    if(document.getElementById("edit-field-main-category")){
                        target = document.getElementById("edit-field-main-category");
                    } else {
                        target = document.getElementById("edit-maincat");
                    }
                } else {

                    if(document.getElementById("edit-field-sub-category")){
                        target = document.getElementById("edit-field-sub-category");
                    } else {
                        target = document.getElementById("edit-subcat");
                    }
                }
                target.value = e.target.value;
            })


            if(document.querySelector(".form-item-maincat")){
                document.querySelector(".form-item-maincat").classList.add("formhidden")
            }
            if(document.querySelector(".form-item-subcat")){
                document.querySelector(".form-item-subcat").classList.add("formhidden")
            }
            
        });

        
    }
}
window.addEventListener("load", function(){
    formlogic.init();
    
})
