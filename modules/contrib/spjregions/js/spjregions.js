var spjregions = {

    init: function(){
        console.log("init");
        spjregions.bindings();
    },
    bindings: function(){

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
spjregions.init();