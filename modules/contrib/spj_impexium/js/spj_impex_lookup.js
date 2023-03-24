//https://support.spjnetwork.org/getData.php?c=CHICAGO_PRO
var spjimpex = {
    url: "https://support.spjnetwork.org/getData.php?c=",
    type: "single",
    init: function(){

        if(document.querySelector(".field--name-field-committee-code")){
            let code = document.querySelector(".field--name-field-committee-code .field__item").innerHTML;
            spjimpex.getData(code);
        }
        if(document.querySelector(".views-field-field-committee-code")){
            spjimpex.type ="multi";
            document.querySelectorAll(".views-field-field-committee-code").forEach(function(row){

                let code = row.querySelector(".field-content");
                let codeId = code.innerHTML;
                console.log(codeId)
                spjimpex.getData(codeId, row);
            })
        }
        

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
    buildList: function(data, node){

        let posArr = [];
        data.data.forEach(element => {
            posArr[element.positioncode.toLowerCase()] = element
        });


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
            data.data.forEach(element => {
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
   
            if(data.data.length){
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

            console.log("data for this row", data)
            } else {
                let nope = document.createElement("code");
                nope.innerHTML = "nothing found";
                node.append(nope)
            }
            
        }
    },
    getData: function(code, node){
        let newurl = spjimpex.url + code;

        fetch(newurl)
            .then(resp=>resp.json())
            .then(data=>{

                spjimpex.buildList(data, node);
            })
    }
}

spjimpex.init();