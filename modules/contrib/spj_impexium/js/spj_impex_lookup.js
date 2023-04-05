//https://support.spjnetwork.org/getData.php?c=CHICAGO_PRO
var spjimpex = {
    url: "https://support.spjnetwork.org/getData.php?c=",
    newurl: "/api/impexium/list?type=committee&id=",
    whichUrl: null,
    type: "single",
    init: function(){
        spjimpex.whichUrl = spjimpex.newurl;

        if(document.querySelector(".field--name-field-committee-code")){
            let code = document.querySelector(".field--name-field-committee-code .field__item").innerHTML;
            spjimpex.getData(code);
        }
        if(document.querySelector(".views-field-field-committee-code")){
            spjimpex.type ="multi";
            document.querySelectorAll(".views-field-field-committee-code").forEach(function(row){

                let code = row.querySelector(".field-content");
                let codeId = code.innerHTML;
 
                spjimpex.getData(codeId, row);
            })
        }

        spjimpex.accordionify();
        

    },
    accordionify: function(){

        document.querySelectorAll(".view-region-detail .view-grouping-content h3").forEach(function(el){

            let toggler = document.createElement("a");
            toggler.setAttribute("href", "#");
            toggler.classList.add("accordionify")
            //el.append(toggler)
            el.addEventListener("click", function(e){
                console.log("try to find the related rows to toggle")
                //alert("click")
                /*
                let tar = e.target;
                let content = tar.closest(".view-grouping-content");
                if(content.classList.contains("hiddenV")){
                    content.classList.remove("hiddenV")
                } else {
                    content.classList.add("hidden")
                }
                */
            })
        })
    },
    createRow: function(element, type, node){
  
        var d = document.createElement("div");
        d.innerHTML = `
        <span class="badge bg-success text-white">${type}</span>
                <h3>${element.firstName} ${element.lastName}</h3>
                <a href="mailto:${element.email}">Email</a>
        `;
        node.append(d)

    },
    buildListNew: function(data, node){
        console.log("getting new data,", data)
        let posArr = [];
        if(data!=null){
            data.forEach(element=> {
                posArr[element.positionCode.toLowerCase()] = element
            })
        }

        spjimpex.buildRow(posArr, node)
        
    },
    buildRow: function(posArr, node){
        if(spjimpex.type=="single"){
            let par = document.querySelector(".field--name-field-committee-code");
       
            let addition  = document.createElement("div");

            if(posArr["p"]){
                spjimpex.createRow(posArr["p"], "President", addition);
            }
            if(posArr["vp"]){
                spjimpex.createRow(posArr["vp"], "Vice President", addition);
            }
            if(posArr["adv"]){
                spjimpex.createRow(posArr["adv"], "Adviser", addition);
            }
            posArr.forEach(element => {
                let entry  = document.createElement("div");

            
                entry.innerHTML = `
                    <code>
                    ${element.positioncode} |
                    ${element.firstName} ${element.lastName} |
                    ${element.email}
                    </code>
                `
                addition.append(entry);
                
                
            });
            par.append(addition);

        } else {
            console.log("posArr pass", typeof posArr, posArr)
            if(Object.keys(posArr).length){
            //node.innerHTML = "working"
                if(posArr["p"]){
                    spjimpex.createRow(posArr["p"], "President", node);
                }
                if(posArr["vp"]){
                    spjimpex.createRow(posArr["vp"], "Vice President", node);
                }
                if(posArr["adv"]){
                    spjimpex.createRow(posArr["adv"], "Adviser", node);
                }

            } else {
                let nope = document.createElement("code");
                nope.innerHTML = "nothing found";
                node.append(nope)
            }
            
        }
    },
    buildList: function(data, node){



        let posArr = [];
        data.data.forEach(element => {
            posArr[element.positioncode.toLowerCase()] = element
        });

        spjimpex.buildRow(posArr, node)


        
    },
    getData: function(code, node){
        let newurl = spjimpex.whichUrl + code;
        if(spjimpex.whichUrl == spjimpex.url){
            fetch(newurl)
            .then(resp=>resp.json())
            .then(data=>{

                spjimpex.buildList(data, node);
            });
        } else {
            fetch(newurl)
            .then(resp=>resp.json())
            .then(data=>{

               let newdat = data.data[0];
               
               let passOnData = (newdat!==null? newdat.dataList: null);

               spjimpex.buildListNew(passOnData, node);
            });
        }
        
    

        
    }
}

window.addEventListener("DOMContentLoaded", (event) => {
    spjimpex.init();
});
