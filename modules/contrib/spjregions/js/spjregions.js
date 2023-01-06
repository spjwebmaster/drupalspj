var spjregions = {

    init: function(){
        console.log("init");
        spjregions.makeTabs();
        
    },
    makeTabs: function(){
        let container = document.querySelector(".layout__region--first");
        let newWrapper = document.createElement("div");
        newWrapper.classList.add("region-map-tab-wrapper");
        newWrapper.innerHTML = `
        <ul class="nav nav-tabs regionTabToggler">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#tabRegionMap">Map</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#tabRegionList">Chapter List</a>
            </li>
        </ul>`;
            


        let sib = container.querySelector(".block-field-blocknodepagebody");
        sib.after(newWrapper);

        let mapNode = document.querySelector(".region-map").closest(".block-block-content");
        mapNode.id = "tabRegionMap";
        mapNode.classList.add("hidden");
        let listNode = document.querySelector(".block-views-blockchapter-list-block-1");
        listNode.id = "tabRegionList";
        listNode.classList.add("hidden");

        spjregions.bindings();


    }, 
    bindings: function(){


        document.querySelectorAll(".regionTabToggler li a").forEach(function(el){

            if(window.location.href.indexOf("?field_region_target_id")>-1){
                // it should load the list view
                if(el.getAttribute("href")=="#tabRegionList"){
                    el.classList.add("active");
                } else {
                    el.classList.remove("active");
                }
            } 
            if(el.classList.contains("active")){
                console.log( el, el.getAttribute("href"))
                let tar = document.querySelector(el.getAttribute("href"));
                
                tar.classList.remove("hidden");
            }
            
            el.addEventListener("click", function(e){
                e.preventDefault();

                let href = e.target.getAttribute("href").replace("#","");
                console.log("href", href)
                let tar = document.getElementById(href);
                tar.classList.remove("hidden");

                let otherTabWrapper = document.querySelector(".regionTabToggler");
                otherTabWrapper.querySelectorAll("li a").forEach(function(el){
                    let compareHref = el.getAttribute("href").replace("#","");
                    if(compareHref==href) {
                        //
                        el.classList.add("active"); 
                        document.getElementById(href).classList.remove("hidden")
                    } else {
                        el.classList.remove("active"); 
                        document.getElementById(compareHref).classList.add("hidden");

                    }
                })

            })
        })

        document.querySelectorAll("path, circle").forEach(function(el){
           
            el.addEventListener("mouseover", function(e){
               
                let classes = e.target.classList;
                let region = classes[0];
                document.querySelectorAll("." + region).forEach(function(el){
                    el.classList.add("hover");
                })
            })
            el.addEventListener("mouseout", function(e){
              
                let classes = e.target.classList;
                let region = classes[0];
                document.querySelectorAll("." + region).forEach(function(el){
                    el.classList.remove("hover");
                })
            })
            el.addEventListener("click", function(e){
                let tar = e.target;
                
                tar.classList.remove("hover");
                let region = tar.classList[0];
                region = region.replace("region","")
                let state = tar.id;
                console.log("click", region, state);

                if(document.getElementById("edit-field-region-target-id")){
                    /*
                    let sel = document.getElementById("edit-field-region-target-id");
                    let value = "";
                    sel.querySelectorAll("option").forEach(function(op){
                        let text = op.innerText;
                        text = text.replace("Region ", "");
                        //console.log(text);
                        if(region==text){
                            value= op.value
                        }
                    })
                    sel.value = value;
                    sel.closest("form").submit();
                    */
                   let info = el.getAttribute("data-info");

                   let temp = document.createElement("div");
                   temp.innerHTML = info;
                   let st = temp.querySelector("div:first-child").innerHTML;
                   st = st.replace("State: ","");
                   
                   let url = "/chapters/regions/region-" + region + "/?state=" + st;

                   window.location.href=url;


                }
            })
        })

        document.querySelectorAll(".regionTextWrapper").forEach(function(el){

            el.addEventListener("mouseover", function(e){
                let region = e.target.getAttribute("data-region");
    
                document.querySelectorAll(".region" + region).forEach(function(el){
                    el.classList.add("hover");
                })
            })
            el.addEventListener("mouseout", function(e){
                let region = e.target.getAttribute("data-region");
    
                document.querySelectorAll(".region" + region).forEach(function(el){
                    el.classList.remove("hover");
                })
            })

            el.addEventListener("click", function(e){
                let region = e.target.getAttribute("data-region");
                let url = "/chapters/regions/region-" + region + "/";
                window.location.href=url;
            })
            
        });
       


        /*
        $("path, circle").hover(function(e) {
            //$('#info-box').css('display','block');
            let classes = e.target.classList;
            let region = classes[0];
            document.querySelectorAll("." + region).forEach(function(el){
                el.classList.add("hover");
            })
            $('#info-box').html(region);
        });
        $(".regionTextWrapper").hover(function(e) {
    
    
            let region = e.target.getAttribute("data-region");
    
            document.querySelectorAll(".region" + region).forEach(function(el){
                el.classList.add("hover");
            })
        });
        $(".regionTextWrapper").mouseleave(function(e) {
            let region = e.target.getAttribute("data-region");
    
            document.querySelectorAll(".region" + region).forEach(function(el){
                el.classList.remove("hover");
            })
        });
    
        $("path, circle").mouseleave(function(e) {
            let classes = e.target.classList;
            let region = classes[0];
            document.querySelectorAll("." + region).forEach(function(el){
                el.classList.remove("hover");
            })
            //$('#info-box').css('display','none');
        });
        $("path, circle, .regionTextWrapper").click(function(e){
            e.target.classList.remove("hover");
            let reg = "";
            if(e.target.classList.contains("regionText")){
                reg = "region"+e.target.getAttribute("data-region")
            } else {
                reg = e.target.classList[0];
            }
            
            window.location.href = reg+".asp";
        })
    
    
        $(document).mousemove(function(e) {
            $('#info-box').css('top',e.pageY-$('#info-box').height()-30);
            $('#info-box').css('left',e.pageX-($('#info-box').width())/2);
        }).mouseover();
    
        var ios = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        if(ios) {
        $('a').on('click touchend', function() { 
            var link = $(this).attr('href');   
            window.open(link,'_blank');
            return false;
        });
        }
        */

    }
}

window.addEventListener("load", function(){
    spjregions.init();
});
