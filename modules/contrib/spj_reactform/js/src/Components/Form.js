import React, { Component, useState, useEffect } from "react";
import Maincat from "./Maincat";


function Form(props){

    let awardName = "sdx";
    let loc = window.location.href;
    if(loc.toLowerCase().indexOf("/awards/form/")>-1){
        let splits = loc.split("/");
        awardName = splits[splits.length-1];
    }
    if(loc.toLowerCase().indexOf("/awards/add/")>-1){
        let splits = loc.split("/");
        awardName = splits[splits.length-1];
    }
    if(loc.toLowerCase().indexOf("/node/add/")>-1){
        let splits = loc.split("/");
        let awardNameBase = splits[splits.length-1];
        switch(awardNameBase){
            case "national_awards_entry": awardName="moe"; break;
            case "national_awards_entry_sdx": awardName="sdx"; break;
            case "national_awards_entry_naa": awardName="naa"; break;
        }
    }
    console.log("awardName", awardName)
    const [awardCode, setAwardCode] = useState();
    
    const [mainCatSelected, setMainCatSelected] = useState("");
    
    const [subCatSelected, setSubCatSelected] = useState("");
    

    const mainCatChange=(e)=> {
        console.log("change", e.target.value)
        setMainCatSelected(e.target.value);
    }
    const subCatChange=(e)=> {
        console.log("change", e.target.value)
        setSubCatSelected(e.target.value);
    }

    useEffect(() => {
        //?page[offset]=50&page[limit]=50
        //?filter[parent.meta.drupal_internal__target_id]=193
        //get top level taxonomies

      
        fetch(`/jsonapi/taxonomy_term/award_submission_categories?filter[parent.meta.drupal_internal__target_id]=null`)
            .then(response=>response.json())
            .then(data => {

                data.data.forEach(function(el){

                    if(el.attributes.name.toLowerCase()==awardName){
                        setAwardCode(el.attributes.drupal_internal__tid);
                    }
                });
            });
            
        
        
      }, [awardCode]);

    return (<div>

        <Maincat change={mainCatChange} mainCatSelected={mainCatSelected} awardCode={awardCode} />

    </div>)
}
export default Form;