import List from "list.js";


var dog = {
    target: null,
    data: null,
    init: function(){
      dog.target  = document.getElementById("leaderboardDOG");
      dog.getData();
    },
    getData: function(){
      let url = "https://dog.spjnetwork.org/dog-leaderboard-feed-js.php";
      fetch(url)
      .then(resp => resp.text())
      .then(str => new window.DOMParser().parseFromString(str, "text/xml"))
      .then(data => {
        
        dog.data = [];
        data.querySelectorAll("items item").forEach(item=> {
          let temp = {};
          let code =  item.getElementsByTagName("affinitycode")[0].innerHTML;
          if(code ==""){
            code = " ";
          }
          temp['guid'] = item.getElementsByTagName("GUID")[0].innerHTML;
          temp['name'] = item.getElementsByTagName("title")[0].innerHTML;
          temp['donation'] = item.getElementsByTagName("donation")[0].innerHTML;
          temp['code'] = code;
          dog.data.push(temp);
        })
        dog.buildDisplay();
      });
    },
    getDollarFormat: function(val){
      const formattedTotal = 
        new Intl.NumberFormat('en-US', { 
            style: 'currency', 
            currency: 'USD'
        }).format(val);
      return formattedTotal;
    },
    buildDisplay: function(){
      let template = `   
      `;
      dog.data.forEach(row=>{
        template += `<tr class=''>
            <td class='name'>${row.name}</td>
            <td class='donation' data-total="${row.donation}">${dog.getDollarFormat(row.donation)}</td>
            <td class='code'><a href='javascript:window.loadSearch("${row.code}")'>${row.code}</a></td>
          </tr>`;
      })
      template += ``;
      dog.target.innerHTML = template;
      let tfoot = document.createElement("tfoot");
      let total = '0';
      tfoot.innerHTML =`
        <tr>
            <td></td>
            <td><span class="total">0</span></td>
            <td></td>
        </tr>
      `
      dog.target.closest("table").append(tfoot);

      document.querySelector(".searchClear").addEventListener("click", function(e){
        document.querySelector("#dogLeaderboardWrapper input.search").value = "";
        window.listObj.search()
      })
      
      window.updateTotal();
      dog.makeSearch();

    },
    makeSearch: function(){
        var options = {
            valueNames: [ 'name', 'donation' , 'code' ]
          };
          
          window.listObj = new List('dogLeaderboardWrapper', options);
          window.listObj.on('updated', function(e){
            console.log("e",e)
            window.updateTotal()
          });
          
          
          console.log("dog list", window.listObj)
    },
    listObj: null
  }
  dog.init();


window.loadSearch = function(what){
    document.querySelector("#dogLeaderboardWrapper input.search").value = what;
    window.listObj.search(what);
}

window.updateTotal = function(){
    let target = document.querySelector("#dogLeaderboardWrapper table tfoot .total");
    let total = 0;
    document.querySelectorAll("#dogLeaderboardWrapper tbody tr .donation").forEach(donation=>{
        let val = parseInt(donation.getAttribute("data-total"));
        total +=val;
    })
    console.log("update", total);

    const formattedTotal = 
        new Intl.NumberFormat('en-US', { 
            style: 'currency', 
            currency: 'USD'
        }).format(total);
    target.innerHTML = formattedTotal;
}